<p><a href="<?= url('admin/categorie') ?>">&larr; Retour aux catégories</a></p>
<h1>Modifier la catégorie</h1>

<?php if ($erreurs): ?>
    <div class="erreurs" style="margin-left:0">
        <ul>
            <?php foreach ($erreurs as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form class="formulaire formulaire-large" method="post"
      action="<?= url('admin/categorie/modifier/' . (int) $categorie['id_categorie']) ?>">
    <?= csrfChamp() ?>
    <div class="champ">
        <label for="nom_categorie">Nom</label>
        <input type="text" id="nom_categorie" name="nom_categorie" value="<?= e($old['nom_categorie']) ?>" required>
    </div>
    <div class="champ">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="3"><?= e($old['description']) ?></textarea>
    </div>
    <button type="submit" class="btn">Enregistrer</button>
    <a class="btn btn-clair" href="<?= url('admin/categorie') ?>">Annuler</a>
</form>