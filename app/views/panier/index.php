<h1>Mon panier</h1>

<?php if ($message): ?>
    <p class="message"><?= e($message) ?></p>
<?php endif; ?>

<?php if (empty($lignes)): ?>
    <div class="vide">
        <p>Votre panier est vide.</p>
        <a class="btn" href="<?= url('produit') ?>">Voir la carte</a>
    </div>
<?php else: ?>

<div class="tableau-scroll">
<table class="tableau-panier">
    <thead>
        <tr>
            <th>Produit</th>
            <th>Prix</th>
            <th>Quantité</th>
            <th>Sous-total</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($lignes as $l): ?>
        <tr>
            <td>
                <strong><?= e($l['produit']['nom_produit']) ?></strong>
                <?php if ($l['options']): ?>
                    <br><small>
                        <?= e(implode(', ', array_column($l['options'], 'libelle'))) ?>
                    </small>
                <?php endif; ?>
            </td>
            <td><?= formatPrix($l['unitaire']) ?></td>
            <td>
                <form method="post" action="<?= url('panier/modifier') ?>">
                    <?= csrfChamp() ?>
                    <input type="hidden" name="cle" value="<?= e($l['cle']) ?>">
                    <input type="number" name="quantite" value="<?= (int) $l['quantite'] ?>"
                           min="1" max="20" onchange="this.form.submit()">
                </form>
            </td>
            <td><strong><?= formatPrix($l['sous_total']) ?></strong></td>
            <td>
                <form method="post" action="<?= url('panier/supprimer') ?>">
                    <?= csrfChamp() ?>
                    <input type="hidden" name="cle" value="<?= e($l['cle']) ?>">
                    <button type="submit" class="btn-supprimer" title="Retirer">&times;</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>

<div class="recap">
    <p class="total">Total : <span class="prix"><?= formatPrix($total) ?></span></p>
    <div class="actions">
        <a class="btn btn-clair" href="<?= url('produit') ?>">Continuer mes achats</a>
        <form method="post" action="<?= url('panier/vider') ?>">
            <?= csrfChamp() ?>
            <button type="submit" class="btn btn-clair">Vider le panier</button>
        </form>
        <a class="btn" href="<?= url('commande') ?>">Passer la commande</a>
    </div>
</div>

<?php endif; ?>
