<p><a href="<?= url('produit') ?>">&larr; Retour à la carte</a></p>

<div class="fiche">
    <?php if (!empty($produit['image'])): ?>
       <img src="<?= url('assets/images/produits/' . rawurlencode($produit['image'])) ?>" alt="<?= e($produit['nom_produit']) ?>">
    <?php endif; ?>

    <div>
        <p class="etiquette"><?= e($produit['nom_categorie']) ?></p>
        <h1><?= e($produit['nom_produit']) ?></h1>
        <p class="prix"><?= formatPrix((float) $produit['prix']) ?></p>
        <p><?= nl2br(e($produit['description'])) ?></p>

        <?php if (!$produit['disponible']): ?>
            <p><strong>Ce produit est indisponible pour le moment.</strong></p>
        <?php else: ?>
            <form method="post" action="<?= url('panier/ajouter') ?>">
              <?= csrfChamp() ?>

                <input type="hidden" name="id_produit" value="<?= (int) $produit['id_produit'] ?>">

                <?php if ($options): ?>
                    <fieldset>
                        <legend>Options</legend>
                        <?php foreach ($options as $o): ?>
                            <label>
                                <input type="checkbox" name="options[]" value="<?= (int) $o['id_option'] ?>">
                                <?= e($o['libelle']) ?>
                                <?php if ($o['prix_supplement'] > 0): ?>
                                    (+<?= formatPrix((float) $o['prix_supplement']) ?>)
                                <?php endif; ?>
                            </label><br>
                        <?php endforeach; ?>
                    </fieldset>
                <?php endif; ?>

                <label>Quantité
                    <input type="number" name="quantite" value="1" min="1" max="20">
                </label>
                <button class="btn" type="submit">Ajouter au panier</button>
            </form>
        <?php endif; ?>
    </div>
</div>