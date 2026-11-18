<div class="barre-titre">
    <h1>Produits</h1>
    <a class="btn" href="<?= url('admin/produit/creer') ?>">+ Ajouter un produit</a>
</div>

<?php if (!$produits): ?>
    <p>Aucun produit pour le moment.</p>
<?php else: ?>
<div class="tableau-scroll">
<table class="tableau-panier">
    <thead>
        <tr><th></th><th>Produit</th><th>Catégorie</th><th>Prix</th><th>Stock</th><th>Statut</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <?php foreach ($produits as $p): ?>
        <tr>
            <td>
                <?php if (!empty($p['image'])): ?>
                    <img class="miniature" src="<?= url('assets/images/produits/' . rawurlencode($p['image'])) ?>" alt="">
                <?php else: ?>
                    <div class="miniature-vide">Photo</div>
                <?php endif; ?>
            </td>
            <td><strong><?= e($p['nom_produit']) ?></strong></td>
            <td><?= e($p['nom_categorie']) ?></td>
            <td><?= formatPrix((float) $p['prix']) ?></td>
            <td><?= $p['stock'] === null ? 'Illimité' : (int) $p['stock'] ?></td>
            <td>
                <?php if ($p['disponible']): ?>
                    <span class="statut statut-livree">Disponible</span>
                <?php else: ?>
                    <span class="statut statut-annulee">Indisponible</span>
                <?php endif; ?>
            </td>
            <td>
                <div class="actions-ligne">
                    <a class="btn btn-petit" href="<?= url('admin/produit/modifier/' . $p['id_produit']) ?>">Modifier</a>

                    <form method="post" action="<?= url('admin/produit/basculer/' . $p['id_produit']) ?>">
                        <?= csrfChamp() ?>
                        <button type="submit" class="btn btn-petit btn-clair">
                            <?= $p['disponible'] ? 'Masquer' : 'Afficher' ?>
                        </button>
                    </form>

                    <form method="post" action="<?= url('admin/produit/supprimer/' . $p['id_produit']) ?>"
                          onsubmit="return confirm('Supprimer ce produit ?');">
                        <?= csrfChamp() ?>
                        <button type="submit" class="btn btn-petit btn-danger">Supprimer</button>
                    </form>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php endif; ?>