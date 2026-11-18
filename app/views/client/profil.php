<h1>Mon compte</h1>

<?php if ($message): ?>
    <p class="message"><?= e($message) ?></p>
<?php endif; ?>

<div class="profil-carte">
    <p><strong>Nom :</strong> <?= e($client['nom']) ?></p>
    <p><strong>Prénom :</strong> <?= e($client['prenom']) ?></p>
    <p><strong>Téléphone :</strong> <?= e($client['telephone']) ?></p>
    <p><strong>Email :</strong> <?= e($client['email'] ?: 'non renseigné') ?></p>
</div>

<p>
    <a class="btn" href="<?= url('produit') ?>">Voir la carte</a>
    <a class="btn btn-clair" href="<?= url('panier') ?>">Mon panier</a>
    <a class="btn btn-clair" href="<?= url('commande/historique') ?>">Mes commandes</a>
</p>