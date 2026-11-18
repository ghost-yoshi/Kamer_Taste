<h1><?= e($titre) ?></h1>

<nav class="filtres">
    <a href="<?= url('produit') ?>" class="<?= empty($categorieActive) ? 'actif' : '' ?>">Tout</a>
    <?php foreach ($categories as $c): ?>
        <a href="<?= url('categorie/show/' . $c['id_categorie']) ?>"
           class="<?= ($categorieActive['id_categorie'] ?? null) == $c['id_categorie'] ? 'actif' : '' ?>">
            <?= e($c['nom_categorie']) ?>
        </a>
    <?php endforeach; ?>
</nav>

<?php if (empty($produits)): ?>
    <p>Aucun produit disponible pour le moment.</p>
<?php endif; ?>

<div class="grille">
    <?php foreach ($produits as $p): ?>
        <article class="carte">
            <?php if (!empty($p['image'])): ?>
             <img src="<?= url('assets/images/produits/' . rawurlencode($p['image'])) ?>" alt="<?= e($p['nom_produit']) ?>">
            <?php endif; ?>
            <div class="carte-corps">
                <h3><?= e($p['nom_produit']) ?></h3>
                <p class="prix"><?= formatPrix((float) $p['prix']) ?></p>
                <a class="btn" href="<?= url('produit/show/' . $p['id_produit']) ?>">Voir le produit</a>
            </div>
        </article>
    <?php endforeach; ?>
</div>