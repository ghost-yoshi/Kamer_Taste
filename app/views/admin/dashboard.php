<h1>Tableau de bord</h1>

<div class="stats">
    <div class="stat <?= $stats['en_attente'] > 0 ? 'stat-alerte' : '' ?>">
        <span class="stat-nombre"><?= $stats['en_attente'] ?></span>
        <span>Commandes en attente</span>
    </div>
    <div class="stat">
        <span class="stat-nombre"><?= $stats['aujourdhui'] ?></span>
        <span>Commandes aujourd'hui</span>
    </div>
    <div class="stat">
        <span class="stat-nombre"><?= formatPrix($stats['ca_jour']) ?></span>
        <span>Chiffre d'affaires du jour</span>
    </div>
    <div class="stat">
        <span class="stat-nombre"><?= $stats['produits'] ?></span>
        <span>Produits</span>
    </div>
    <div class="stat">
        <span class="stat-nombre"><?= $stats['clients'] ?></span>
        <span>Clients</span>
    </div>
</div>

<h2>Dernières commandes</h2>

<?php if (!$recentes): ?>
    <p>Aucune commande pour le moment.</p>
<?php else: ?>
<div class="tableau-scroll">
<table class="tableau-panier">
    <thead>
        <tr><th>N°</th><th>Date</th><th>Client</th><th>Mode</th><th>Total</th><th>Statut</th><th></th></tr>
    </thead>
    <tbody>
    <?php foreach ($recentes as $c): ?>
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
<p><a href="<?= url('admin/commande') ?>">Voir toutes les commandes &rarr;</a></p>
<?php endif; ?>