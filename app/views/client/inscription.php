<h1>Créer un compte</h1>

<?php if ($erreurs): ?>
    <div class="erreurs">
        <ul>
            <?php foreach ($erreurs as $err): ?>
                <li><?= e($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form class="formulaire" method="post" action="<?= url('client/inscription') ?>">
    <?= csrfChamp() ?>

    <div class="champ">
        <label for="nom">Nom</label>
        <input type="text" id="nom" name="nom" value="<?= e($old['nom']) ?>" required>
    </div>
    <div class="champ">
        <label for="prenom">Prénom</label>
        <input type="text" id="prenom" name="prenom" value="<?= e($old['prenom']) ?>" required>
    </div>
    <div class="champ">
        <label for="telephone">Téléphone</label>
        <input type="tel" id="telephone" name="telephone" value="<?= e($old['telephone']) ?>" required>
    </div>
    <div class="champ">
        <label for="email">Email <small>(facultatif)</small></label>
        <input type="email" id="email" name="email" value="<?= e($old['email']) ?>">
    </div>
    <div class="champ">
        <label for="mot_de_passe">Mot de passe</label>
        <input type="password" id="mot_de_passe" name="mot_de_passe" minlength="8" required>
        <small>8 caractères minimum</small>
    </div>
    <div class="champ">
        <label for="confirmation">Confirmer le mot de passe</label>
        <input type="password" id="confirmation" name="confirmation" minlength="8" required>
    </div>

    <button type="submit" class="btn">Créer mon compte</button>
    <p class="lien-form">Déjà inscrit ? <a href="<?= url('client/connexion') ?>">Se connecter</a></p>
</form>