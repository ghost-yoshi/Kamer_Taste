<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titre ?? 'Administration') ?> - Admin <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>?v=<?= time() ?>">
</head>
<body class="admin">
<header class="admin-top">
    <strong><?= e(APP_NAME) ?> · Administration</strong>
    <div class="admin-top-droite">
        <span><?= e($admin['nom']) ?></span>
        <a href="<?= url() ?>" target="_blank">Voir le site</a>
        <form method="post" action="<?= url('admin/auth/deconnexion') ?>">
            <?= csrfChamp() ?>
            <button type="submit" class="lien-bouton">Déconnexion</button>
        </form>
    </div>
</header>

<div class="admin-layout">
    <aside class="admin-menu">
        <a href="<?= url('admin') ?>" class="<?= ($menuActif ?? '') === 'dashboard' ? 'actif' : '' ?>">Tableau de bord</a>
        <a href="<?= url('admin/commande') ?>" class="<?= ($menuActif ?? '') === 'commandes' ? 'actif' : '' ?>">Commandes</a>
        <a href="<?= url('admin/produit') ?>" class="<?= ($menuActif ?? '') === 'produits' ? 'actif' : '' ?>">Produits</a>
        <a href="<?= url('admin/categorie') ?>" class="<?= ($menuActif ?? '') === 'categories' ? 'actif' : '' ?>">Catégories</a>
    </aside>

    <section class="admin-contenu">
        <?php if (!empty($message)): ?>
            <p class="message"><?= e($message) ?></p>
        <?php endif; ?>
        <?php if (!empty($messageErreur)): ?>
    <p class="message message-erreur"><?= e($messageErreur) ?></p>
<?php endif; ?>