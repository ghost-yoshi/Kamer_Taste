<?php
class Administrateur extends Model
{
    public function compter(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM administrateur')->fetchColumn();
    }

    public function findByEmail(string $email): ?array
    {
        $st = $this->db->prepare('SELECT * FROM administrateur WHERE email = ?');
        $st->execute([$email]);
        return $st->fetch() ?: null;
    }

    public function creer(string $nom, string $email, string $hash, string $role = 'admin'): int
    {
        $st = $this->db->prepare(
            'INSERT INTO administrateur (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)'
        );
        $st->execute([$nom, $email, $hash, $role]);
        return (int) $this->db->lastInsertId();
    }
}