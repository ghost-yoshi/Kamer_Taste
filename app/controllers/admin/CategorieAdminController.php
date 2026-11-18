<?php
class CategorieAdminController extends AdminBaseController
{
    // /admin/categorie
    public function index(): void
    {
        $this->viewAdmin('categories/index', [
            'titre'      => 'Catégories',
            'menuActif'  => 'categories',
            'categories' => $this->model('Categorie')->allAvecCompte(),
        ]);
    }

    // /admin/categorie/creer (POST)
    public function creer(): void
    {
        $this->exigerPost();
        $modele      = $this->model('Categorie');
        $nom         = trim($_POST['nom_categorie'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($nom === '' || mb_strlen($nom) > 100) {
            $this->erreurFlash('Le nom de la catégorie est obligatoire (100 caractères maximum).');
        } elseif ($modele->nomExiste($nom)) {
            $this->erreurFlash('Cette catégorie existe déjà.');
        } else {
            $modele->creer($nom, $description);
            $_SESSION['flash'] = 'Catégorie « ' . $nom . ' » ajoutée.';
        }
        $this->redirect('admin/categorie');
    }

    // /admin/categorie/modifier/3
    public function modifier(string $id = '0'): void
    {
        $modele    = $this->model('Categorie');
        $categorie = $modele->find((int) $id);
        if (!$categorie) {
            $this->notFound();
            return;
        }

        $erreurs = [];
        $old = [
            'nom_categorie' => $categorie['nom_categorie'],
            'description'   => $categorie['description'] ?? '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old['nom_categorie'] = trim($_POST['nom_categorie'] ?? '');
            $old['description']   = trim($_POST['description'] ?? '');

            if (!csrfValide()) {
                $erreurs[] = 'Session expirée, veuillez réessayer.';
            }
            if ($old['nom_categorie'] === '' || mb_strlen($old['nom_categorie']) > 100) {
                $erreurs[] = 'Le nom est obligatoire (100 caractères maximum).';
            } elseif ($modele->nomExiste($old['nom_categorie'], (int) $id)) {
                $erreurs[] = 'Une autre catégorie porte déjà ce nom.';
            }

            if (!$erreurs) {
                $modele->modifier((int) $id, $old['nom_categorie'], $old['description']);
                $_SESSION['flash'] = 'Catégorie modifiée.';
                $this->redirect('admin/categorie');
            }
        }

        $this->viewAdmin('categories/modifier', [
            'titre'     => 'Modifier la catégorie',
            'menuActif' => 'categories',
            'categorie' => $categorie,
            'old'       => $old,
            'erreurs'   => $erreurs,
        ]);
    }

    // /admin/categorie/supprimer/3 (POST)
    public function supprimer(string $id = '0'): void
    {
        $this->exigerPost();
        $modele    = $this->model('Categorie');
        $categorie = $modele->find((int) $id);
        if (!$categorie) {
            $this->notFound();
            return;
        }

        $nb = $modele->nbProduits((int) $id);
        if ($nb > 0) {
            $this->erreurFlash('Impossible de supprimer « ' . $categorie['nom_categorie'] . ' » : elle contient '
                . $nb . ' produit(s). Déplacez ou supprimez-les d\'abord.');
        } else {
            $modele->supprimer((int) $id);
            $_SESSION['flash'] = 'Catégorie « ' . $categorie['nom_categorie'] . ' » supprimée.';
        }
        $this->redirect('admin/categorie');
    }
}