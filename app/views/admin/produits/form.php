<?php $edition = !empty($produit); ?>

<p><a href="<?= url('admin/produit') ?>">&larr; Retour aux produits</a></p>
<h1><?= $edition ? 'Modifier le produit' : 'Ajouter un produit' ?></h1>

<?php if ($erreurs): ?>
    <div class="erreurs" style="margin-left:0">
        <ul>
            <?php foreach ($erreurs as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form class="formulaire formulaire-large" method="post" enctype="multipart/form-data"
      action="<?= $edition ? url('admin/produit/modifier/' . (int) $produit['id_produit']) : url('admin/produit/creer') ?>">
    <?= csrfChamp() ?>

    <div class="champ">
        <label for="nom_produit">Nom du produit</label>
        <input type="text" id="nom_produit" name="nom_produit" value="<?= e($old['nom_produit']) ?>" required>
    </div>

    <div class="champ">
        <label for="id_categorie">Catégorie</label>
        <select id="id_categorie" name="id_categorie" required>
            <option value="">-- Choisir --</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?= (int) $c['id_categorie'] ?>"
                    <?= (int) $old['id_categorie'] === (int) $c['id_categorie'] ? 'selected' : '' ?>>
                    <?= e($c['nom_categorie']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="ligne-double">
        <div class="champ">
            <label for="prix">Prix (FCFA)</label>
            <input type="number" id="prix" name="prix" step="any" min="0" value="<?= e((string) $old['prix']) ?>" required>
        </div>
        <div class="champ">
            <label for="stock">Stock <small>(vide = illimité)</small></label>
            <input type="number" id="stock" name="stock" min="0" step="1" value="<?= e((string) $old['stock']) ?>">
        </div>
    </div>

    <div class="champ">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="4"><?= e($old['description']) ?></textarea>
    </div>

    <div class="champ">
        <label for="image">Photo <small>(JPG, PNG ou WebP, 2 Mo maximum)</small></label>
        <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp">
        <?php if (!empty($old['image'])): ?>
            <img class="apercu" src="<?= url('assets/images/produits/' . rawurlencode($old['image'])) ?>" alt="Photo actuelle">
            <small>Photo actuelle : elle sera remplacée si tu en envoies une nouvelle.</small>
        <?php endif; ?>
    </div>

    <label class="choix">
        <input type="checkbox" name="disponible" value="1" <?= $old['disponible'] ? 'checked' : '' ?>>
        Disponible à la commande
    </label>

    <fieldset>
        <legend>Options proposées avec ce produit</legend>
        <?php if (!$options): ?>
            <p><small>Aucune option n'existe encore : ajoute la première ci-dessous.</small></p>
        <?php endif; ?>
        <?php foreach ($options as $o): ?>
            <label class="choix">
                <input type="checkbox" name="options[]" value="<?= (int) $o['id_option'] ?>"
                    <?= in_array((int) $o['id_option'], $cochees, true) ? 'checked' : '' ?>>
                <?= e($o['libelle']) ?>
                <?php if ($o['prix_supplement'] > 0): ?>
                    <small>(+ <?= formatPrix((float) $o['prix_supplement']) ?>)</small>
                <?php endif; ?>
            </label>
        <?php endforeach; ?>

        <div class="ligne-double">
            <div class="champ">
                <label for="nouvelle_option">Nouvelle option</label>
                <input type="text" id="nouvelle_option" name="nouvelle_option"
                       value="<?= e($nouvelle['libelle']) ?>" placeholder="Ex. : Piment fort">
            </div>
            <div class="champ">
                <label for="nouvelle_option_prix">Supplément (FCFA)</label>
                <input type="number" id="nouvelle_option_prix" name="nouvelle_option_prix"
                       min="0" step="any" value="<?= e($nouvelle['prix']) ?>" placeholder="0">
            </div>
        </div>
    </fieldset>

    <button type="submit" class="btn btn-grand"><?= $edition ? 'Enregistrer' : 'Ajouter le produit' ?></button>
    <a class="btn btn-clair" href="<?= url('admin/produit') ?>">Annuler</a>
</form>