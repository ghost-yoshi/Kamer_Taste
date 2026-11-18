<?php
class Produit extends Model
{
    public function all(): array
    {
        $sql = 'SELECT p.*, c.nom_categorie
                FROM produit p
                JOIN categorie c ON c.id_categorie = p.id_categorie
                WHERE p.disponible = 1
                ORDER BY c.nom_categorie, p.nom_produit';
        return $this->db->query($sql)->fetchAll();
    }
   public function byCategorie(int $idCategorie): array
{
    $st = $this->db->prepare(
        'SELECT p.*, c.nom_categorie
         FROM produit p
         JOIN categorie c ON c.id_categorie = p.id_categorie
         WHERE p.disponible = 1 AND p.id_categorie = ?
         ORDER BY p.nom_produit'
    );
    $st->execute([$idCategorie]);
    return $st->fetchAll();
}

public function find(int $id): ?array
{
    $st = $this->db->prepare(
        'SELECT p.*, c.nom_categorie
         FROM produit p
         JOIN categorie c ON c.id_categorie = p.id_categorie
         WHERE p.id_produit = ?'
    );
    $st->execute([$id]);
    return $st->fetch() ?: null;
}

public function options(int $idProduit): array
{
    $st = $this->db->prepare(
        'SELECT o.*
         FROM option_produit o
         JOIN produit_option po ON po.id_option = o.id_option
         WHERE po.id_produit = ?'
    );
    $st->execute([$idProduit]);
    return $st->fetchAll();
}
// Options demandées, limitées à celles qui existent vraiment pour ce produit
public function optionsByIds(int $idProduit, array $ids): array
{
    if (!$ids) {
        return [];
    }
    $in = implode(',', array_fill(0, count($ids), '?'));
    $st = $this->db->prepare(
        "SELECT o.*
         FROM option_produit o
         JOIN produit_option po ON po.id_option = o.id_option
         WHERE po.id_produit = ? AND o.id_option IN ($in)"
    );
    $st->execute(array_merge([$idProduit], array_map('intval', $ids)));
    return $st->fetchAll();
}

// ===== Côté administration =====

public function allAdmin(): array
{
    return $this->db->query(
        'SELECT p.*, c.nom_categorie
         FROM produit p
         JOIN categorie c ON c.id_categorie = p.id_categorie
         ORDER BY c.nom_categorie, p.nom_produit'
    )->fetchAll();
}

public function creer(array $d): int
{
    $st = $this->db->prepare(
        'INSERT INTO produit (nom_produit, description, prix, image, disponible, stock, id_categorie)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $st->execute([
        $d['nom_produit'], $d['description'], $d['prix'], $d['image'],
        $d['disponible'], $d['stock'], $d['id_categorie'],
    ]);
    return (int) $this->db->lastInsertId();
}

public function modifier(int $id, array $d): void
{
    $st = $this->db->prepare(
        'UPDATE produit
         SET nom_produit = ?, description = ?, prix = ?, image = ?,
             disponible = ?, stock = ?, id_categorie = ?
         WHERE id_produit = ?'
    );
    $st->execute([
        $d['nom_produit'], $d['description'], $d['prix'], $d['image'],
        $d['disponible'], $d['stock'], $d['id_categorie'], $id,
    ]);
}

public function basculer(int $id): void
{
    $this->db->prepare('UPDATE produit SET disponible = 1 - disponible WHERE id_produit = ?')->execute([$id]);
}

public function desactiver(int $id): void
{
    $this->db->prepare('UPDATE produit SET disponible = 0 WHERE id_produit = ?')->execute([$id]);
}

// false si le produit figure déjà dans des commandes ou des menus
public function supprimer(int $id): bool
{
    try {
        $this->db->prepare('DELETE FROM produit WHERE id_produit = ?')->execute([$id]);
        return true;
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
            return false;
        }
        throw $e;
    }
}


// Fin de la classe Produit


public function optionsIds(int $id): array
{
    $st = $this->db->prepare('SELECT id_option FROM produit_option WHERE id_produit = ?');
    $st->execute([$id]);
    return array_map('intval', $st->fetchAll(PDO::FETCH_COLUMN));
}

public function syncOptions(int $id, array $ids): void
{
    $this->db->beginTransaction();
    try {
        $this->db->prepare('DELETE FROM produit_option WHERE id_produit = ?')->execute([$id]);
        $ins = $this->db->prepare('INSERT INTO produit_option (id_produit, id_option) VALUES (?, ?)');
        foreach (array_unique($ids) as $idOption) {
            $ins->execute([$id, (int) $idOption]);
        }
        $this->db->commit();
    } catch (Throwable $e) {
        $this->db->rollBack();
        throw $e;
    }
}
}
