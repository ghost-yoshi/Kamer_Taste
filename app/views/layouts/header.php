<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titre ?? APP_NAME) ?> - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
</head>
<body class="<?= !empty($accueil) ? 'accueil' : '' ?>">
<header>
    <a class="logo-lien" href="<?= url() ?>">
        <?php if (is_file(ROOT . '/public/assets/images/logo.png')): ?>
            <img class="logo-header" src="<?= url('assets/images/logo.png') ?>" alt="<?= e(APP_NAME) ?>">
        <?php else: ?>
            <?= e(APP_NAME) ?>
        <?php endif; ?>
    </a>
    <nav>
    <a href="<?= url() ?>">Accueil</a>
    <a href="<?= url('produit') ?>">La carte</a>
    <a href="<?= url('commande/historique') ?>">Mes commandes</a>
    <a href="<?= url('panier') ?>">Panier <span class="badge"><?= Panier::compter() ?></span></a>

    <?php if (estConnecte()): ?>
        <a href="<?= url('client/profil') ?>"><?= e(clientConnecte()['prenom']) ?></a>
        <form method="post" action="<?= url('client/deconnexion') ?>">
            <?= csrfChamp() ?>
            <button type="submit" class="lien-bouton">Déconnexion</button>
        </form>
    <?php else: ?>
        <a href="<?= url('client/connexion') ?>">Connexion</a>
        <a href="<?= url('client/inscription') ?>">Inscription</a>
    <?php endif; ?>
</nav>
</header>
<main>