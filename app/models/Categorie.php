<?php
class Categorie extends Model
{
    public function all(): array
    {
        return $this->db->query('SELECT * FROM categorie ORDER BY nom_categorie')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $st = $this->db->prepare('SELECT * FROM categorie WHERE id_categorie = ?');
        $st->execute([$id]);
        return $st->fetch() ?: null;
    }

   public function allAvecCompte(): array
{
    return $this->db->query(
        'SELECT c.*, COUNT(p.id_produit) AS nb_produits
         FROM categorie c
         LEFT JOIN produit p ON p.id_categorie = c.id_categorie
         GROUP BY c.id_categorie
         ORDER BY c.nom_categorie'
    )->fetchAll();
}

public function nomExiste(string $nom, int $sauf = 0): bool
{
    $st = $this->db->prepare('SELECT 1 FROM categorie WHERE nom_categorie = ? AND id_categorie <> ?');
    $st->execute([$nom, $sauf]);
    return (bool) $st->fetchColumn();
}

public function creer(string $nom, string $description): int
{
    $st = $this->db->prepare('INSERT INTO categorie (nom_categorie, description) VALUES (?, ?)');
    $st->execute([$nom, $description !== '' ? $description : null]);
    return (int) $this->db->lastInsertId();
}

public function modifier(int $id, string $nom, string $description): void
{
    $st = $this->db->prepare('UPDATE categorie SET nom_categorie = ?, description = ? WHERE id_categorie = ?');
    $st->execute([$nom, $description !== '' ? $description : null, $id]);
}

public function nbProduits(int $id): int
{
    $st = $this->db->prepare('SELECT COUNT(*) FROM produit WHERE id_categorie = ?');
    $st->execute([$id]);
    return (int) $st->fetchColumn();
}

public function supprimer(int $id): void
{
    $this->db->prepare('DELETE FROM categorie WHERE id_categorie = ?')->execute([$id]);
}
}