<h1>Merci pour votre commande ! 🎉</h1>

<p class="message">
    Votre commande <strong>n°<?= (int) $commande['id_commande'] ?></strong> a bien été enregistrée.
</p>

<table class="tableau-panier">
    <thead>
        <tr><th>Produit</th><th>Prix</th><th>Qté</th><th>Sous-total</th></tr>
    </thead>
    <tbody>
    <?php
    $sousTotal = 0;
    foreach ($lignes as $l):
        $ligneTotal = $l['prix_unitaire'] * $l['quantite'];
        $sousTotal += $ligneTotal;
    ?>
        <tr>
            <td>
                <?= e($l['nom_produit']) ?>
                <?php if ($l['options_choisies']): ?>
                    <br><small><?= e($l['options_choisies']) ?></small>
                <?php endif; ?>
            </td>
            <td><?= formatPrix((float) $l['prix_unitaire']) ?></td>
            <td><?= (int) $l['quantite'] ?></td>
            <td><?= formatPrix((float) $ligneTotal) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php
$frais  = $livraison ? (float) $livraison['frais'] : 0;
$remise = $sousTotal + $frais - (float) $commande['total'];
?>
<div class="recap">
    <p>Sous-total : <?= formatPrix((float) $sousTotal) ?></p>
    <?php if ($remise > 0): ?>
        <p>Réduction (<?= e($commande['code_promo']) ?>) : − <?= formatPrix($remise) ?></p>
    <?php endif; ?>
    <?php if ($livraison): ?>
        <p>Livraison : <?= formatPrix($frais) ?></p>
    <?php endif; ?>
    <p class="total">Total : <span class="prix"><?= formatPrix((float) $commande['total']) ?></span></p>
</div>

<div class="profil-carte">
    <p><strong>Statut :</strong> <span class="statut"><?= e(libelleStatut($commande['statut'])) ?></span></p>
    <p><strong>Récupération :</strong>
        <?= $commande['mode_recuperation'] === 'livraison' ? 'Livraison' : 'À emporter' ?></p>
    <?php if ($livraison): ?>
        <p><strong>Adresse :</strong>
            <?= e($livraison['quartier']) ?>
            <?= $livraison['rue'] ? ', ' . e($livraison['rue']) : '' ?>
            <?= $livraison['repere'] ? ' (' . e($livraison['repere']) . ')' : '' ?>
        </p>
    <?php endif; ?>
    <?php if ($paiement): ?>
        <p><strong>Paiement :</strong> <?= e(libellePaiement($paiement['mode_paiement'])) ?>
            (<?= e(libelleStatut($paiement['statut'])) ?>)</p>
    <?php endif; ?>
</div>

<a class="btn" href="<?= url('produit') ?>">Retour à la carte</a>