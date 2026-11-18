<h1>Connexion</h1>
<?php if (!empty($message)): ?>
    <p class="message"><?= e($message) ?></p>
<?php endif; ?>
<?php if ($erreurs): ?>
    <div class="erreurs">
        <ul>
            <?php foreach ($erreurs as $err): ?>
                <li><?= e($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form class="formulaire" method="post" action="<?= url('client/connexion') ?>">
    <?= csrfChamp() ?>

    <div class="champ">
        <label for="login">Téléphone ou email</label>
        <input type="text" id="login" name="login" value="<?= e($login) ?>" required autofocus>
    </div>
    <div class="champ">
        <label for="mot_de_passe">Mot de passe</label>
        <input type="password" id="mot_de_passe" name="mot_de_passe" required>
    </div>

    <button type="submit" class="btn">Se connecter</button>
    <p class="lien-form">Pas encore de compte ? <a href="<?= url('client/inscription') ?>">S'inscrire</a></p>
</form>