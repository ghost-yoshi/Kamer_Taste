<?php
class ProduitController extends Controller
{
    // /produit
    public function index(): void
    {
        $this->view('produits/index', [
            'titre'           => 'Notre carte',
            'produits'        => $this->model('Produit')->all(),
            'categories'      => $this->model('Categorie')->all(),
            'categorieActive' => null,
        ]);
    }

    // /produit/show/5
    public function show(string $id = '0'): void
    {
        $modele  = $this->model('Produit');
        $produit = $modele->find((int) $id);

        if (!$produit) {
            $this->notFound();
            return;
        }

        $this->view('produits/show', [
            'titre'   => $produit['nom_produit'],
            'produit' => $produit,
            'options' => $modele->options((int) $id),
        ]);
    }
}