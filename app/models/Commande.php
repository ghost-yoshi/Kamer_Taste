<?php
class Commande extends Model
{
    public function creer(array $c, array $lignes): int
    {
        $this->db->beginTransaction();
        try {
            $st = $this->db->prepare(
                "INSERT INTO commande (statut, mode_recuperation, total, id_client, id_promo)
                 VALUES ('en_attente', ?, ?, ?, ?)"
            );
            $st->execute([$c['mode'], $c['total'], $c['id_client'], $c['id_promo']]);
            $id = (int) $this->db->lastInsertId();

            $insLigne = $this->db->prepare(
                'INSERT INTO ligne_commande (id_commande, id_produit, options_choisies, quantite, prix_unitaire)
                 VALUES (?, ?, ?, ?, ?)'
            );
            $majStock = $this->db->prepare(
                'UPDATE produit SET stock = stock - ? WHERE id_produit = ? AND stock IS NOT NULL'
            );
            foreach ($lignes as $l) {
                $insLigne->execute([$id, $l['id_produit'], $l['options_choisies'], $l['quantite'], $l['prix_unitaire']]);
                $majStock->execute([$l['quantite'], $l['id_produit']]);
            }

            $this->db->prepare(
                "INSERT INTO paiement (mode_paiement, montant, statut, id_commande)
                 VALUES (?, ?, 'en_attente', ?)"
            )->execute([$c['paiement'], $c['total'], $id]);

            if ($c['mode'] === 'livraison') {
                $this->db->prepare(
                    "INSERT INTO livraison (frais, statut, id_commande, id_adresse)
                     VALUES (?, 'en_attente', ?, ?)"
                )->execute([$c['frais'], $id, $c['id_adresse']]);
            }

            $this->db->commit();
            return $id;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // Une commande n'est visible que par son propriétaire
    public function find(int $id, int $idClient): ?array
    {
        $st = $this->db->prepare(
            'SELECT c.*, pr.code AS code_promo
             FROM commande c
             LEFT JOIN promotion pr ON pr.id_promo = c.id_promo
             WHERE c.id_commande = ? AND c.id_client = ?'
        );
        $st->execute([$id, $idClient]);
        return $st->fetch() ?: null;
    }

    public function lignes(int $id): array
    {
        $st = $this->db->prepare(
            'SELECT l.*, p.nom_produit
             FROM ligne_commande l
             JOIN produit p ON p.id_produit = l.id_produit
             WHERE l.id_commande = ?'
        );
        $st->execute([$id]);
        return $st->fetchAll();
    }

    public function paiement(int $id): ?array
    {
        $st = $this->db->prepare('SELECT * FROM paiement WHERE id_commande = ?');
        $st->execute([$id]);
        return $st->fetch() ?: null;
    }

    public function livraison(int $id): ?array
    {
        $st = $this->db->prepare(
            'SELECT l.*, a.quartier, a.rue, a.repere
             FROM livraison l
             JOIN adresse a ON a.id_adresse = l.id_adresse
             WHERE l.id_commande = ?'
        );
        $st->execute([$id]);
        return $st->fetch() ?: null;
    }
    // ===== Côté administration =====

public function stats(): array
{
    $n = fn(string $sql) => $this->db->query($sql)->fetchColumn();
    return [
        'en_attente' => (int) $n("SELECT COUNT(*) FROM commande WHERE statut = 'en_attente'"),
        'aujourdhui' => (int) $n('SELECT COUNT(*) FROM commande WHERE DATE(date_commande) = CURDATE()'),
        'ca_jour'    => (float) $n("SELECT COALESCE(SUM(total), 0) FROM commande
                                    WHERE DATE(date_commande) = CURDATE() AND statut <> 'annulee'"),
        'produits'   => (int) $n('SELECT COUNT(*) FROM produit'),
        'clients'    => (int) $n('SELECT COUNT(*) FROM client'),
    ];
}

public function liste(?string $statut = null, int $limite = 200): array
{
    $sql = 'SELECT c.*, cl.nom, cl.prenom, cl.telephone
            FROM commande c
            JOIN client cl ON cl.id_client = c.id_client';
    $params = [];
    if ($statut !== null) {
        $sql .= ' WHERE c.statut = ?';
        $params[] = $statut;
    }
    $sql .= ' ORDER BY c.date_commande DESC, c.id_commande DESC LIMIT ' . (int) $limite;

    $st = $this->db->prepare($sql);
    $st->execute($params);
    return $st->fetchAll();
}

public function findAdmin(int $id): ?array
{
    $st = $this->db->prepare(
        'SELECT c.*, cl.nom, cl.prenom, cl.telephone, cl.email, pr.code AS code_promo
         FROM commande c
         JOIN client cl ON cl.id_client = c.id_client
         LEFT JOIN promotion pr ON pr.id_promo = c.id_promo
         WHERE c.id_commande = ?'
    );
    $st->execute([$id]);
    return $st->fetch() ?: null;
}

// "livree" et "annulee" sont des statuts finaux.
// Une annulation remet les quantités en stock.
public function changerStatut(int $id, string $nouveau): bool
{
    $this->db->beginTransaction();
    try {
        $st = $this->db->prepare('SELECT statut FROM commande WHERE id_commande = ? FOR UPDATE');
        $st->execute([$id]);
        $actuel = $st->fetchColumn();

        if ($actuel === false || $actuel === $nouveau || in_array($actuel, ['livree', 'annulee'], true)) {
            $this->db->rollBack();
            return false;
        }

        $this->db->prepare('UPDATE commande SET statut = ? WHERE id_commande = ?')
                 ->execute([$nouveau, $id]);

        if ($nouveau === 'annulee') {
            $st = $this->db->prepare(
                'SELECT id_produit, SUM(quantite) AS q FROM ligne_commande
                 WHERE id_commande = ? GROUP BY id_produit'
            );
            $st->execute([$id]);
            $maj = $this->db->prepare(
                'UPDATE produit SET stock = stock + ? WHERE id_produit = ? AND stock IS NOT NULL'
            );
            foreach ($st->fetchAll() as $l) {
                $maj->execute([(int) $l['q'], (int) $l['id_produit']]);
            }
            $this->db->prepare("UPDATE paiement SET statut = 'echoue' WHERE id_commande = ? AND statut = 'en_attente'")
                     ->execute([$id]);
        }

        if ($nouveau === 'en_livraison') {
            $this->db->prepare("UPDATE livraison SET statut = 'en_cours' WHERE id_commande = ?")
                     ->execute([$id]);
        }
        if ($nouveau === 'livree') {
            $this->db->prepare("UPDATE livraison SET statut = 'livree', date_livraison = NOW() WHERE id_commande = ?")
                     ->execute([$id]);
        }

        $this->db->commit();
        return true;
    } catch (Throwable $e) {
        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }
        throw $e;
    }
}

public function majPaiement(int $id, string $statut): void
{
    $this->db->prepare('UPDATE paiement SET statut = ? WHERE id_commande = ?')
             ->execute([$statut, $id]);
}

public function majLivreur(int $id, string $livreur): void
{
    $this->db->prepare('UPDATE livraison SET livreur = ? WHERE id_commande = ?')
             ->execute([$livreur !== '' ? $livreur : null, $id]);
}

public function byClient(int $idClient): array
{
    $st = $this->db->prepare(
        'SELECT c.*,
                (SELECT COALESCE(SUM(l.quantite), 0)
                 FROM ligne_commande l
                 WHERE l.id_commande = c.id_commande) AS nb_articles
         FROM commande c
         WHERE c.id_client = ?
         ORDER BY c.date_commande DESC, c.id_commande DESC'
    );
    $st->execute([$idClient]);
    return $st->fetchAll();
}

}