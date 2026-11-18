<h1>Catégories</h1>

<div class="tableau-scroll">
<table class="tableau-panier">
    <thead>
        <tr><th>Nom</th><th>Description</th><th>Produits</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <?php foreach ($categories as $c): ?>
        <tr>
            <td><strong><?= e($c['nom_categorie']) ?></strong></td>
            <td><?= e($c['description'] ?? '') ?></td>
            <td><?= (int) $c['nb_produits'] ?></td>
            <td>
                <div class="actions-ligne">
                    <a class="btn btn-petit" href="<?= url('admin/categorie/modifier/' . $c['id_categorie']) ?>">Modifier</a>
                    <form method="post" action="<?= url('admin/categorie/supprimer/' . $c['id_categorie']) ?>"
                          onsubmit="return confirm('Supprimer cette catégorie ?');">
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

<h2>Ajouter une catégorie</h2>
<form class="formulaire formulaire-large" method="post" action="<?= url('admin/categorie/creer') ?>">
    <?= csrfChamp() ?>
    <div class="champ">
        <label for="nom_categorie">Nom</label>
        <input type="text" id="nom_categorie" name="nom_categorie" required>
    </div>
    <div class="champ">
        <label for="description">Description <small>(facultatif)</small></label>
        <textarea id="description" name="description" rows="2"></textarea>
    </div>
    <button type="submit" class="btn">Ajouter</button>
</form>