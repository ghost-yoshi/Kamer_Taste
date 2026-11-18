<h1>Mes commandes</h1>

<?php if (!$commandes): ?>
    <div class="vide">
        <p>Vous n'avez pas encore passé de commande.</p>
        <a class="btn" href="<?= url('produit') ?>">Voir la carte</a>
    </div>
<?php else: ?>
<div class="tableau-scroll">
<table class="tableau-panier">
    <thead>
        <tr><th>N°</th><th>Date</th><th>Articles</th><th>Mode</th><th>Total</th><th>Statut</th><th></th></tr>
    </thead>
    <tbody>
    <?php foreach ($commandes as $c): ?>
        <tr>
            <td>#<?= (int) $c['id_commande'] ?></td>
            <td><?= date('d/m/Y H:i', strtotime($c['date_commande'])) ?></td>
            <td><?= (int) $c['nb_articles'] ?></td>
            <td><?= $c['mode_recuperation'] === 'livraison' ? 'Livraison' : 'À emporter' ?></td>
            <td><?= formatPrix((float) $c['total']) ?></td>
            <td><span class="statut statut-<?= e($c['statut']) ?>"><?= e(libelleStatut($c['statut'])) ?></span></td>
            <td><a class="btn btn-petit" href="<?= url('commande/voir/' . $c['id_commande']) ?>">Détails</a></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php endif; ?>