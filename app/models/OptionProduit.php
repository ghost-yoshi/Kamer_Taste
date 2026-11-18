<?php
class OptionProduit extends Model
{
    public function all(): array
    {
        return $this->db->query('SELECT * FROM option_produit ORDER BY libelle')->fetchAll();
    }

    public function creer(string $libelle, float $prixSupplement): int
    {
        $st = $this->db->prepare('INSERT INTO option_produit (libelle, prix_supplement) VALUES (?, ?)');
        $st->execute([$libelle, $prixSupplement]);
        return (int) $this->db->lastInsertId();
    }
}