<?php
// Limite les tentatives de connexion échouées (protection contre les mots de passe devinés)
class Throttle
{
    public const MAX     = 5;    // échecs autorisés
    public const FENETRE = 15;   // en minutes

    public static function bloque(string $espace): bool
    {
        try {
            $st = Database::getInstance()->prepare(
                'SELECT COUNT(*) FROM tentative_connexion
                 WHERE espace = ? AND ip = ?
                   AND date_tentative > (NOW() - INTERVAL ' . self::FENETRE . ' MINUTE)'
            );
            $st->execute([$espace, self::ip()]);
            return (int) $st->fetchColumn() >= self::MAX;
        } catch (PDOException $e) {
            error_log('Throttle : ' . $e->getMessage());
            return false;
        }
    }

    public static function enregistrerEchec(string $espace, string $identifiant): void
    {
        try {
            $db = Database::getInstance();
            $db->prepare('INSERT INTO tentative_connexion (ip, identifiant, espace) VALUES (?, ?, ?)')
               ->execute([self::ip(), mb_substr($identifiant, 0, 150), $espace]);
            // Nettoyage des anciennes lignes
            $db->exec('DELETE FROM tentative_connexion WHERE date_tentative < (NOW() - INTERVAL 1 DAY)');
        } catch (PDOException $e) {
            error_log('Throttle : ' . $e->getMessage());
        }
    }

    public static function reinitialiser(string $espace): void
    {
        try {
            Database::getInstance()
                ->prepare('DELETE FROM tentative_connexion WHERE espace = ? AND ip = ?')
                ->execute([$espace, self::ip()]);
        } catch (PDOException $e) {
            error_log('Throttle : ' . $e->getMessage());
        }
    }

    private static function ip(): string
    {
        return substr($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0', 0, 45);
    }
}
