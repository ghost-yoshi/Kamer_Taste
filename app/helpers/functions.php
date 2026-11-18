<?php
function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

// Échappe le texte avant affichage (protection XSS)
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function formatPrix(float $prix): string
{
    // Espaces insécables : le prix ne se coupe jamais en fin de ligne
    return number_format($prix, 0, ',', "\u{00A0}") . "\u{00A0}FCFA";
}
// ----- Sécurité des formulaires (protection CSRF) -----
function csrfToken(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrfChamp(): string
{
    return '<input type="hidden" name="csrf" value="' . csrfToken() . '">';
}

function csrfValide(): bool
{
    return isset($_POST['csrf'], $_SESSION['csrf'])
        && hash_equals($_SESSION['csrf'], (string) $_POST['csrf']);
}

// ----- Client connecté -----
function estConnecte(): bool
{
    return !empty($_SESSION['client']);
}

function clientConnecte(): ?array
{
    return $_SESSION['client'] ?? null;
}
function libelleStatut(string $statut): string
{
    return [
        'en_attente'     => 'En attente',
        'confirmee'      => 'Confirmée',
        'en_preparation' => 'En préparation',
        'prete'          => 'Prête',
        'en_livraison'   => 'En livraison',
        'livree'         => 'Livrée',
        'annulee'        => 'Annulée',
        'reussi'         => 'Réussi',
        'echoue'         => 'Échoué',
        'en_cours'       => 'En cours',
        'echouee'        => 'Échouée',
    ][$statut] ?? ucfirst(str_replace('_', ' ', $statut));
}

function libellePaiement(string $mode): string
{
    return [
        'mobile_money' => 'Mobile Money',
        'especes'      => 'Espèces (à la livraison ou au retrait)',
        'carte'        => 'Carte bancaire',
    ][$mode] ?? $mode;
}