<?php
class ProduitAdminController extends AdminBaseController
{
    private const MAX_IMAGE = 2097152;   // 2 Mo

    // /admin/produit
    public function index(): void
    {
        $this->viewAdmin('produits/index', [
            'titre'     => 'Produits',
            'menuActif' => 'produits',
            'produits'  => $this->model('Produit')->allAdmin(),
        ]);
    }

    // /admin/produit/creer
    public function creer(): void
    {
        $this->formulaire(null);
    }

    // /admin/produit/modifier/5
    public function modifier(string $id = '0'): void
    {
        $produit = $this->model('Produit')->find((int) $id);
        if (!$produit) {
            $this->notFound();
            return;
        }
        $this->formulaire($produit);
    }

    // /admin/produit/basculer/5 (POST) : disponible / indisponible
    public function basculer(string $id = '0'): void
    {
        $this->exigerPost();
        $this->model('Produit')->basculer((int) $id);
        $this->redirect('admin/produit');
    }

    // /admin/produit/supprimer/5 (POST)
    public function supprimer(string $id = '0'): void
    {
        $this->exigerPost();
        $modele  = $this->model('Produit');
        $produit = $modele->find((int) $id);
        if (!$produit) {
            $this->notFound();
            return;
        }

        if ($modele->supprimer((int) $id)) {
            $_SESSION['flash'] = '« ' . $produit['nom_produit'] . ' » a été supprimé.';
        } else {
            // Il figure dans des commandes : on garde l'historique et on le masque
            $modele->desactiver((int) $id);
            $this->erreurFlash('« ' . $produit['nom_produit'] . ' » figure dans des commandes ou des menus : '
                . 'il ne peut pas être supprimé. Il a été rendu indisponible à la place.');
        }
        $this->redirect('admin/produit');
    }

    // ----- Méthodes privées (non accessibles par l'URL) -----

    private function formulaire(?array $produit): void
    {
        $modele     = $this->model('Produit');
        $categories = $this->model('Categorie')->all();
        $options    = $this->model('OptionProduit')->all();
        $erreurs    = [];

        $old = $produit ?? [
            'nom_produit' => '', 'description' => '', 'prix' => '', 'stock' => '',
            'id_categorie' => 0, 'disponible' => 1, 'image' => null,
        ];
        if ($produit) {
            $old['prix']        = (string) (float) $produit['prix'];
            $old['stock']       = $produit['stock'] === null ? '' : (string) $produit['stock'];
            $old['description'] = $produit['description'] ?? '';
        }
        $cochees  = $produit ? $modele->optionsIds((int) $produit['id_produit']) : [];
        $nouvelle = ['libelle' => '', 'prix' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old['nom_produit']  = trim($_POST['nom_produit'] ?? '');
            $old['description']  = trim($_POST['description'] ?? '');
            $old['prix']         = trim($_POST['prix'] ?? '');
            $old['stock']        = trim($_POST['stock'] ?? '');
            $old['id_categorie'] = (int) ($_POST['id_categorie'] ?? 0);
            $old['disponible']   = isset($_POST['disponible']) ? 1 : 0;
            $cochees  = array_map('intval', (array) ($_POST['options'] ?? []));
            $nouvelle = [
                'libelle' => trim($_POST['nouvelle_option'] ?? ''),
                'prix'    => trim($_POST['nouvelle_option_prix'] ?? ''),
            ];

            if (!csrfValide()) {
                $erreurs[] = 'Session expirée, veuillez réessayer.';
            }
            if ($old['nom_produit'] === '' || mb_strlen($old['nom_produit']) > 150) {
                $erreurs[] = 'Le nom est obligatoire (150 caractères maximum).';
            }

            $prix = str_replace(',', '.', $old['prix']);
            if (!is_numeric($prix) || (float) $prix < 0 || (float) $prix > 9999999) {
                $erreurs[] = 'Prix invalide.';
            }

            $stock = null;
            if ($old['stock'] !== '') {
                if (!ctype_digit($old['stock'])) {
                    $erreurs[] = 'Le stock doit être un nombre entier (ou vide pour un stock illimité).';
                } else {
                    $stock = (int) $old['stock'];
                }
            }

            $idsCategories = array_map('intval', array_column($categories, 'id_categorie'));
            if (!in_array($old['id_categorie'], $idsCategories, true)) {
                $erreurs[] = 'Choisissez une catégorie.';
            }

            $prixOption = 0.0;
            if ($nouvelle['libelle'] !== '') {
                $pOpt = str_replace(',', '.', $nouvelle['prix'] === '' ? '0' : $nouvelle['prix']);
                if (mb_strlen($nouvelle['libelle']) > 100) {
                    $erreurs[] = 'Le nom de la nouvelle option est trop long (100 caractères maximum).';
                }
                if (!is_numeric($pOpt) || (float) $pOpt < 0) {
                    $erreurs[] = 'Le supplément de la nouvelle option est invalide.';
                } else {
                    $prixOption = (float) $pOpt;
                }
            }

            // On ne garde que des options qui existent vraiment
            $idsOptions = array_map('intval', array_column($options, 'id_option'));
            $cochees    = array_values(array_intersect($cochees, $idsOptions));

            // Photo (facultative)
            $fichier = $produit['image'] ?? null;
            $envoi   = $_FILES['image'] ?? null;
            if (!$erreurs && $envoi && $envoi['error'] !== UPLOAD_ERR_NO_FILE) {
                [$nomFichier, $erreurImage] = $this->enregistrerImage($envoi, $old['nom_produit']);
                if ($erreurImage) {
                    $erreurs[] = $erreurImage;
                } else {
                    $fichier = $nomFichier;
                }
            }

            if (!$erreurs) {
                try {
                    $data = [
                        'nom_produit'  => $old['nom_produit'],
                        'description'  => $old['description'] !== '' ? $old['description'] : null,
                        'prix'         => (float) $prix,
                        'image'        => $fichier,
                        'disponible'   => $old['disponible'],
                        'stock'        => $stock,
                        'id_categorie' => $old['id_categorie'],
                    ];

                    if ($produit) {
                        $id = (int) $produit['id_produit'];
                        $modele->modifier($id, $data);
                    } else {
                        $id = $modele->creer($data);
                    }

                    if ($nouvelle['libelle'] !== '') {
                        $cochees[] = $this->model('OptionProduit')->creer($nouvelle['libelle'], $prixOption);
                    }
                    $modele->syncOptions($id, $cochees);

                    $_SESSION['flash'] = '« ' . $old['nom_produit'] . ' » a été '
                        . ($produit ? 'modifié.' : 'ajouté à la carte.');
                    $this->redirect('admin/produit');
                } catch (PDOException $e) {
                    error_log('Produit admin : ' . $e->getMessage());
                    $erreurs[] = 'Une erreur est survenue, veuillez réessayer.';
                }
            }
        }

        $this->viewAdmin('produits/form', [
            'titre'      => $produit ? 'Modifier le produit' : 'Ajouter un produit',
            'menuActif'  => 'produits',
            'produit'    => $produit,
            'old'        => $old,
            'categories' => $categories,
            'options'    => $options,
            'cochees'    => $cochees,
            'nouvelle'   => $nouvelle,
            'erreurs'    => $erreurs,
        ]);
    }

    // Retourne [nom du fichier, message d'erreur]
    private function enregistrerImage(array $f, string $nomProduit): array
    {
        if ($f['error'] === UPLOAD_ERR_INI_SIZE || $f['error'] === UPLOAD_ERR_FORM_SIZE) {
            return [null, 'Image trop lourde (2 Mo maximum).'];
        }
        if ($f['error'] !== UPLOAD_ERR_OK) {
            return [null, 'L\'envoi de l\'image a échoué, veuillez réessayer.'];
        }
        if ($f['size'] > self::MAX_IMAGE) {
            return [null, 'Image trop lourde (2 Mo maximum).'];
        }

        // On vérifie le vrai contenu du fichier, pas seulement son extension
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
        $ext  = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$mime] ?? null;
        if ($ext === null) {
            return [null, 'Format d\'image non accepté (JPG, PNG ou WebP uniquement).'];
        }

        // Nom sûr : sans espace ni accent, avec un suffixe aléatoire
        $ascii = @iconv('UTF-8', 'ASCII//TRANSLIT', $nomProduit) ?: $nomProduit;
        $slug  = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $ascii), '-')) ?: 'produit';
        $nom   = $slug . '-' . bin2hex(random_bytes(4)) . '.' . $ext;

        $dossier = ROOT . '/public/assets/images/produits';
        if (!is_dir($dossier)) {
            mkdir($dossier, 0755, true);
        }
        if (!move_uploaded_file($f['tmp_name'], $dossier . '/' . $nom)) {
            return [null, 'Impossible d\'enregistrer l\'image sur le serveur.'];
        }
        return [$nom, null];
    }
}