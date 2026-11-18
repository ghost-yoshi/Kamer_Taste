<?php
class Adresse extends Model
{
    public function byClient(int $idClient): array
    {
        $st = $this->db->prepare('SELECT * FROM adresse WHERE id_client = ? ORDER BY id_adresse DESC');
        $st->execute([$idClient]);
        return $st->fetchAll();
    }

    // Retourne l'adresse seulement si elle appartient bien au client
    public function find(int $id, int $idClient): ?array
    {
        $st = $this->db->prepare('SELECT * FROM adresse WHERE id_adresse = ? AND id_client = ?');
        $st->execute([$id, $idClient]);
        return $st->fetch() ?: null;
    }

    public function creer(int $idClient, string $quartier, string $rue, string $repere): int
    {
        $st = $this->db->prepare(
            'INSERT INTO adresse (quartier, rue, repere, id_client) VALUES (?, ?, ?, ?)'
        );
        $st->execute([$quartier, $rue !== '' ? $rue : null, $repere !== '' ? $repere : null, $idClient]);
        return (int) $this->db->lastInsertId();
    }
}