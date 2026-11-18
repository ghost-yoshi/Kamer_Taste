<h1>Commandes</h1>

<nav class="filtres">
    <a href="<?= url('admin/commande') ?>" class="<?= $filtre === null ? 'actif' : '' ?>">Toutes</a>
    <?php foreach (CommandeAdminController::STATUTS as $s): ?>
        <a href="<?= url('admin/commande?statut=' . $s) ?>" class="<?= $filtre === $s ? 'actif' : '' ?>">
            <?= e(libelleStatut($s)) ?>
        </a>
    <?php endforeach; ?>
</nav>

<?php if (!$commandes): ?>
    <p>Aucune commande dans cette catégorie.</p>
<?php else: ?>
<div class="tableau-scroll">
<table class="tableau-panier">
    <thead>
        <tr><th>N°</th><th>Date</th><th>Client</th><th>Mode</th><th>Total</th><th>Statut</th><th></th></tr>
    </thead>
    <tbody>
    <?php foreach ($commandes as $c): ?>
        <tr>
            <td>#<?= (int) $c['id_commande'] ?></td>
            <td><?= date('d/m/Y H:i', strtotime($c['date_commande'])) ?></td>
            <td><?= e($c['prenom'] . ' ' . $c['nom']) ?><br><small><?= e($c['telephone']) ?></small></td>
            <td><?= $c['mode_recuperation'] === 'livraison' ? 'Livraison' : 'À emporter' ?></td>
            <td><?= formatPrix((float) $c['total']) ?></td>
            <td><span class="statut statut-<?= e($c['statut']) ?>"><?= e(libelleStatut($c['statut'])) ?></span></td>
            <td><a class="btn btn-petit" href="<?= url('admin/commande/show/' . $c['id_commande']) ?>">Voir</a></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php endif; ?>