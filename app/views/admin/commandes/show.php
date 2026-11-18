<p><a href="<?= url('admin/commande') ?>">&larr; Retour aux commandes</a></p>

<h1>Commande n°<?= (int) $commande['id_commande'] ?>
    <span class="statut statut-<?= e($commande['statut']) ?>"><?= e(libelleStatut($commande['statut'])) ?></span>
</h1>

<div class="admin-grille">
    <div class="profil-carte">
        <h3>Client</h3>
        <p><strong><?= e($commande['prenom'] . ' ' . $commande['nom']) ?></strong></p>
        <p>Tél : <a href="tel:<?= e($commande['telephone']) ?>"><?= e($commande['telephone']) ?></a></p>
        <?php if ($commande['email']): ?>
            <p>Email : <?= e($commande['email']) ?></p>
        <?php endif; ?>
        <p>Commandé le <?= date('d/m/Y à H:i', strtotime($commande['date_commande'])) ?></p>
    </div>

    <div class="profil-carte">
        <h3>Récupération</h3>
        <p><strong><?= $commande['mode_recuperation'] === 'livraison' ? 'Livraison' : 'À emporter' ?></strong></p>
        <?php if ($livraison): ?>
            <p>
                <?= e($livraison['quartier']) ?>
                <?= $livraison['rue'] ? ', ' . e($livraison['rue']) : '' ?>
                <?= $livraison['repere'] ? '<br><small>Repère : ' . e($livraison['repere']) . '</small>' : '' ?>
            </p>
            <p>Livreur : <?= e($livraison['livreur'] ?: 'non assigné') ?></p>
        <?php endif; ?>
        <?php if ($paiement): ?>
            <p>Paiement : <?= e(libellePaiement($paiement['mode_paiement'])) ?>
                (<?= e(libelleStatut($paiement['statut'])) ?>)</p>
        <?php endif; ?>
    </div>
</div>

<h2>Articles</h2>
<div class="tableau-scroll">
<table class="tableau-panier">
    <thead><tr><th>Produit</th><th>Prix</th><th>Qté</th><th>Sous-total</th></tr></thead>
    <tbody>
    <?php
    $sousTotal = 0;
    foreach ($lignes as $l):
        $t = $l['prix_unitaire'] * $l['quantite'];
        $sousTotal += $t;
    ?>
        <tr>
            <td>
                <?= e($l['nom_produit']) ?>
                <?php if ($l['options_choisies']): ?><br><small><?= e($l['options_choisies']) ?></small><?php endif; ?>
            </td>
            <td><?= formatPrix((float) $l['prix_unitaire']) ?></td>
            <td><?= (int) $l['quantite'] ?></td>
            <td><?= formatPrix((float) $t) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>

<?php
$frais  = $livraison ? (float) $livraison['frais'] : 0;
$remise = $sousTotal + $frais - (float) $commande['total'];
?>
<div class="recap">
    <p>Sous-total : <?= formatPrix((float) $sousTotal) ?></p>
    <?php if ($remise > 0): ?>
        <p>Réduction (<?= e($commande['code_promo']) ?>) : − <?= formatPrix($remise) ?></p>
    <?php endif; ?>
    <?php if ($livraison): ?><p>Livraison : <?= formatPrix($frais) ?></p><?php endif; ?>
    <p class="total">Total : <span class="prix"><?= formatPrix((float) $commande['total']) ?></span></p>
</div>

<h2>Traitement de la commande</h2>
<?php if ($final): ?>
    <p><em>Cette commande est terminée : son statut ne peut plus être modifié.</em></p>
<?php endif; ?>

<form class="formulaire formulaire-large" method="post"
      action="<?= url('admin/commande/maj/' . (int) $commande['id_commande']) ?>">
    <?= csrfChamp() ?>

    <div class="champ">
        <label for="statut">Statut de la commande</label>
        <select id="statut" name="statut" <?= $final ? 'disabled' : '' ?>>
            <?php foreach (CommandeAdminController::STATUTS as $s): ?>
                <option value="<?= e($s) ?>" <?= $s === $commande['statut'] ? 'selected' : '' ?>>
                    <?= e(libelleStatut($s)) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <?php if ($paiement): ?>
    <div class="champ">
        <label for="statut_paiement">Statut du paiement</label>
        <select id="statut_paiement" name="statut_paiement">
            <?php foreach (CommandeAdminController::PAIEMENTS as $s): ?>
                <option value="<?= e($s) ?>" <?= $s === $paiement['statut'] ? 'selected' : '' ?>>
                    <?= e(libelleStatut($s)) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php endif; ?>

    <?php if ($livraison): ?>
    <div class="champ">
        <label for="livreur">Livreur</label>
        <input type="text" id="livreur" name="livreur" value="<?= e($livraison['livreur'] ?? '') ?>"
               placeholder="Nom du livreur">
    </div>
    <?php endif; ?>

    <button type="submit" class="btn">Enregistrer</button>
</form>