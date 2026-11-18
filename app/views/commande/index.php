<h1>Passer la commande</h1>

<?php if ($erreurs): ?>
    <div class="erreurs">
        <ul>
            <?php foreach ($erreurs as $err): ?>
                <li><?= e($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<h2>Votre panier</h2>
<ul class="resume-panier">
    <?php foreach ($lignes as $l): ?>
        <li>
            <?= (int) $l['quantite'] ?> × <?= e($l['produit']['nom_produit']) ?>
            <?php if ($l['options']): ?>
                <small>(<?= e(implode(', ', array_column($l['options'], 'libelle'))) ?>)</small>
            <?php endif; ?>
            <span><?= formatPrix($l['sous_total']) ?></span>
        </li>
    <?php endforeach; ?>
</ul>
<p><strong>Sous-total : <?= formatPrix($total) ?></strong></p>

<form class="formulaire formulaire-large" method="post" action="<?= url('commande/valider') ?>">
    <?= csrfChamp() ?>

    <fieldset>
        <legend>Comment récupérer votre commande ?</legend>
        <label class="choix">
            <input type="radio" name="mode_recuperation" value="livraison"
                   <?= $old['mode'] === 'livraison' ? 'checked' : '' ?>>
            Livraison <small>(+ <?= formatPrix(FRAIS_LIVRAISON) ?>)</small>
        </label>
        <label class="choix">
            <input type="radio" name="mode_recuperation" value="a_emporter"
                   <?= $old['mode'] === 'a_emporter' ? 'checked' : '' ?>>
            À emporter <small>(gratuit)</small>
        </label>
    </fieldset>

    <fieldset id="bloc-livraison">
        <legend>Adresse de livraison</legend>

        <?php foreach ($adresses as $i => $a): ?>
            <?php
            $coche = $old['adresse'] === (string) $a['id_adresse']
                  || ($old['adresse'] === '' && $i === 0);
            ?>
            <label class="choix">
                <input type="radio" name="adresse" value="<?= (int) $a['id_adresse'] ?>" <?= $coche ? 'checked' : '' ?>>
                <?= e($a['quartier']) ?>
                <?= $a['rue'] ? ', ' . e($a['rue']) : '' ?>
                <?= $a['repere'] ? ' <small>(' . e($a['repere']) . ')</small>' : '' ?>
            </label>
        <?php endforeach; ?>

        <label class="choix">
            <input type="radio" name="adresse" value="nouvelle"
                   <?= (!$adresses || $old['adresse'] === 'nouvelle') ? 'checked' : '' ?>>
            Utiliser une nouvelle adresse
        </label>

        <div class="champ">
            <label for="quartier">Quartier</label>
            <input type="text" id="quartier" name="quartier" value="<?= e($old['quartier']) ?>">
        </div>
        <div class="champ">
            <label for="rue">Rue <small>(facultatif)</small></label>
            <input type="text" id="rue" name="rue" value="<?= e($old['rue']) ?>">
        </div>
        <div class="champ">
            <label for="repere">Point de repère <small>(facultatif)</small></label>
            <input type="text" id="repere" name="repere" value="<?= e($old['repere']) ?>"
                   placeholder="Ex. : derrière la pharmacie">
        </div>
    </fieldset>

    <fieldset>
        <legend>Paiement</legend>
        <label class="choix">
            <input type="radio" name="mode_paiement" value="mobile_money"
                   <?= $old['paiement'] === 'mobile_money' ? 'checked' : '' ?>>
            Mobile Money
        </label>
        <label class="choix">
            <input type="radio" name="mode_paiement" value="especes"
                   <?= $old['paiement'] === 'especes' ? 'checked' : '' ?>>
            Espèces à la livraison ou au retrait
        </label>
    </fieldset>

    <div class="champ">
        <label for="code_promo">Code promo <small>(facultatif)</small></label>
        <input type="text" id="code_promo" name="code_promo" value="<?= e($old['code']) ?>">
    </div>

    <button type="submit" class="btn btn-grand">Confirmer la commande</button>
    <a class="btn btn-clair" href="<?= url('panier') ?>">Retour au panier</a>
</form>

<script>
// Cache le bloc adresse quand « À emporter » est choisi
const blocLivraison = document.getElementById('bloc-livraison');
function majAdresse() {
    const livraison = document.querySelector('input[name="mode_recuperation"]:checked').value === 'livraison';
    blocLivraison.style.display = livraison ? '' : 'none';
}
document.querySelectorAll('input[name="mode_recuperation"]').forEach(r => r.addEventListener('change', majAdresse));
majAdresse();
</script>