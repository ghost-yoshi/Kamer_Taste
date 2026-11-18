<?php
class Client extends Model
{
    // Connexion possible avec le téléphone OU l'email
    public function findByLogin(string $login): ?array
    {
        $st = $this->db->prepare('SELECT * FROM client WHERE telephone = ? OR email = ? LIMIT 1');
        $st->execute([$login, $login]);
        return $st->fetch() ?: null;
    }

    public function find(int $id): ?array
    {
        $st = $this->db->prepare('SELECT * FROM client WHERE id_client = ?');
        $st->execute([$id]);
        return $st->fetch() ?: null;
    }

    public function telephoneExiste(string $telephone): bool
    {
        $st = $this->db->prepare('SELECT 1 FROM client WHERE telephone = ?');
        $st->execute([$telephone]);
        return (bool) $st->fetchColumn();
    }

    public function emailExiste(string $email): bool
    {
        $st = $this->db->prepare('SELECT 1 FROM client WHERE email = ?');
        $st->execute([$email]);
        return (bool) $st->fetchColumn();
    }

    public function creer(array $d): int
    {
        $st = $this->db->prepare(
            'INSERT INTO client (nom, prenom, telephone, email, mot_de_passe)
             VALUES (?, ?, ?, ?, ?)'
        );
        $st->execute([
            $d['nom'],
            $d['prenom'],
            $d['telephone'],
            $d['email'] !== '' ? $d['email'] : null,
            $d['mot_de_passe'],
        ]);
        return (int) $this->db->lastInsertId();
    }
}