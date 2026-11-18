<h1>Créer l'administrateur</h1>
<p class="centre">Premier démarrage : créez le compte administrateur du site. Cette page se fermera ensuite d'elle-même.</p>

<?php if ($erreurs): ?>
    <div class="erreurs"><ul>
        <?php foreach ($erreurs as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
    </ul></div>
<?php endif; ?>

<form class="formulaire" method="post" action="<?= url('admin/auth/installation') ?>">
    <?= csrfChamp() ?>
    <div class="champ">
        <label for="nom">Nom</label>
        <input type="text" id="nom" name="nom" value="<?= e($old['nom']) ?>" required>
    </div>
    <div class="champ">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= e($old['email']) ?>" required>
    </div>
    <div class="champ">
        <label for="mot_de_passe">Mot de passe</label>
        <input type="password" id="mot_de_passe" name="mot_de_passe" minlength="10" required>
        <small>10 caractères minimum</small>
    </div>
    <div class="champ">
        <label for="confirmation">Confirmer le mot de passe</label>
        <input type="password" id="confirmation" name="confirmation" minlength="10" required>
    </div>
    <button type="submit" class="btn">Créer l'administrateur</button>
</form>