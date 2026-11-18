<h1>Espace administrateur</h1>

<?php if (!empty($message)): ?>
    <p class="message"><?= e($message) ?></p>
<?php endif; ?>

<?php if ($erreurs): ?>
    <div class="erreurs"><ul>
        <?php foreach ($erreurs as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
    </ul></div>
<?php endif; ?>

<form class="formulaire" method="post" action="<?= url('admin/auth/connexion') ?>">
    <?= csrfChamp() ?>
    <div class="champ">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= e($email) ?>" required autofocus>
    </div>
    <div class="champ">
        <label for="mot_de_passe">Mot de passe</label>
        <input type="password" id="mot_de_passe" name="mot_de_passe" required>
    </div>
    <button type="submit" class="btn">Se connecter</button>
</form>