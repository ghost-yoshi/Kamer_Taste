<?php
class CategorieController extends Controller
{
    // /categorie/show/2
    public function show(string $id = '0'): void
    {
        $categorie = $this->model('Categorie')->find((int) $id);

        if (!$categorie) {
            $this->notFound();
            return;
        }

        $this->view('produits/index', [
            'titre'           => $categorie['nom_categorie'],
            'produits'        => $this->model('Produit')->byCategorie((int) $id),
            'categories'      => $this->model('Categorie')->all(),
            'categorieActive' => $categorie,
        ]);
    }
}
