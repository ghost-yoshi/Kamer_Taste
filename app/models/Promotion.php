<?php
class Promotion extends Model
{
    public function findValide(string $code): ?array
    {
        $st = $this->db->prepare(
            'SELECT * FROM promotion
             WHERE code = ? AND CURDATE() BETWEEN date_debut AND date_fin'
        );
        $st->execute([$code]);
        return $st->fetch() ?: null;
    }
}