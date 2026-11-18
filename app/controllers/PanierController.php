<?php
class PanierController extends Controller
{
    // /panier
    public function index(): void
    {
        $detail  = Panier::detail();
        $message = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $this->view('panier/index', [
            'titre'   => 'Mon panier',
            'lignes'  => $detail['lignes'],
            'total'   => $detail['total'],
            'message' => $message,
        ]);
    }

    // /panier/ajouter (formulaire de la fiche produit)
    public function ajouter(): void
    {
        $this->exigerPost();

        $id        = (int) ($_POST['id_produit'] ?? 0);
        $quantite  = max(1, min(20, (int) ($_POST['quantite'] ?? 1)));
        $demandees = array_map('intval', (array) ($_POST['options'] ?? []));

        $modele  = $this->model('Produit');
        $produit = $modele->find($id);

        if (!$produit || !$produit['disponible']) {
            $this->notFound();
            return;
        }

        $options = array_map('intval', array_column($modele->optionsByIds($id, $demandees), 'id_option'));
        Panier::ajouter($id, $options, $quantite);

        $_SESSION['flash'] = '« ' . $produit['nom_produit'] . ' » a été ajouté au panier.';
        $this->redirect('panier');
    }

    // /panier/modifier
    public function modifier(): void
    {
        $this->exigerPost();
        Panier::modifier((string) ($_POST['cle'] ?? ''), (int) ($_POST['quantite'] ?? 1));
        $this->redirect('panier');
    }

    // /panier/supprimer
    public function supprimer(): void
    {
        $this->exigerPost();
        Panier::supprimer((string) ($_POST['cle'] ?? ''));
        $_SESSION['flash'] = 'Article retiré du panier.';
        $this->redirect('panier');
    }

    // /panier/vider
    public function vider(): void
    {
        $this->exigerPost();
        Panier::vider();
        $_SESSION['flash'] = 'Votre panier a été vidé.';
        $this->redirect('panier');
    }

    // Ces actions ne se déclenchent que depuis un formulaire valide, pas en tapant l'URL
    private function exigerPost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('panier');
        }
        if (!csrfValide()) {
            $_SESSION['flash'] = 'Action refusée (session expirée). Veuillez réessayer.';
            $this->redirect('panier');
        }
    }
}
