<?php
class CommandeAdminController extends AdminBaseController
{
    public const STATUTS    = ['en_attente', 'confirmee', 'en_preparation', 'prete', 'en_livraison', 'livree', 'annulee'];
    public const PAIEMENTS  = ['en_attente', 'reussi', 'echoue'];

    // /admin/commande?statut=en_attente
    public function index(): void
    {
        $filtre = $_GET['statut'] ?? '';
        $filtre = in_array($filtre, self::STATUTS, true) ? $filtre : null;

        $this->viewAdmin('commandes/index', [
            'titre'     => 'Commandes',
            'menuActif' => 'commandes',
            'commandes' => $this->model('Commande')->liste($filtre),
            'filtre'    => $filtre,
        ]);
    }

    // /admin/commande/show/5
    public function show(string $id = '0'): void
    {
        $modele   = $this->model('Commande');
        $commande = $modele->findAdmin((int) $id);

        if (!$commande) {
            $this->notFound();
            return;
        }

        $this->viewAdmin('commandes/show', [
            'titre'     => 'Commande n°' . (int) $id,
            'menuActif' => 'commandes',
            'commande'  => $commande,
            'lignes'    => $modele->lignes((int) $id),
            'paiement'  => $modele->paiement((int) $id),
            'livraison' => $modele->livraison((int) $id),
            'final'     => in_array($commande['statut'], ['livree', 'annulee'], true),
        ]);
    }

    // /admin/commande/maj/5 (POST)
    public function maj(string $id = '0'): void
    {
        $this->exigerPost();

        $id       = (int) $id;
        $modele   = $this->model('Commande');
        $commande = $modele->findAdmin($id);
        if (!$commande) {
            $this->notFound();
            return;
        }

        $messages = [];

        $statut = $_POST['statut'] ?? $commande['statut'];
        if (in_array($statut, self::STATUTS, true) && $statut !== $commande['statut']) {
            if ($modele->changerStatut($id, $statut)) {
                $messages[] = 'Statut mis à jour : ' . libelleStatut($statut) . '.';
            } else {
                $messages[] = 'Cette commande est terminée : son statut ne peut plus changer.';
            }
        }

        $paiement = $_POST['statut_paiement'] ?? '';
        if (in_array($paiement, self::PAIEMENTS, true)) {
            $modele->majPaiement($id, $paiement);
        }

        if ($commande['mode_recuperation'] === 'livraison') {
            $modele->majLivreur($id, mb_substr(trim($_POST['livreur'] ?? ''), 0, 100));
        }

        $_SESSION['flash'] = $messages ? implode(' ', $messages) : 'Modifications enregistrées.';
        $this->redirect('admin/commande/show/' . $id);
    }
}