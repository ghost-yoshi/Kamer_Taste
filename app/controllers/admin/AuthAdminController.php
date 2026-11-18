<?php
class AuthAdminController extends Controller
{
    // /admin/auth/connexion
    public function connexion(): void
    {
        if (!empty($_SESSION['admin'])) {
            $this->redirect('admin');
        }
        $modele = $this->model('Administrateur');
        if ($modele->compter() === 0) {
            $this->redirect('admin/auth/installation');
        }

        $message = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        $erreurs = [];
        $email   = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $mdp   = $_POST['mot_de_passe'] ?? '';

            if (!csrfValide()) {
                $erreurs[] = 'Session expirée, veuillez réessayer.';
            } elseif (Throttle::bloque('admin')) {
                $erreurs[] = 'Trop de tentatives échouées. Réessayez dans ' . Throttle::FENETRE . ' minutes.';
            } else {
                $admin = $modele->findByEmail($email);
                if ($admin && password_verify($mdp, $admin['mot_de_passe'])) {
                    Throttle::reinitialiser('admin');
                    session_regenerate_id(true);
                    $_SESSION['admin'] = [
                        'id_admin' => (int) $admin['id_admin'],
                        'nom'      => $admin['nom'],
                        'role'     => $admin['role'],
                    ];
                    $this->redirect('admin');
                }

                Throttle::enregistrerEchec('admin', $email);
                $erreurs[] = 'Identifiants incorrects.';
            }
        }

        $this->view('admin/auth/connexion', [
            'titre'   => 'Connexion administrateur',
            'erreurs' => $erreurs,
            'email'   => $email,
            'message' => $message,
        ]);
    }

    // /admin/auth/installation : création du premier administrateur.
    // Se ferme toute seule dès qu'un administrateur existe.
    public function installation(): void
    {
        $modele = $this->model('Administrateur');
        if ($modele->compter() > 0) {
            $this->redirect('admin/auth/connexion');
        }

        $erreurs = [];
        $old     = ['nom' => '', 'email' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old['nom']   = trim($_POST['nom'] ?? '');
            $old['email'] = trim($_POST['email'] ?? '');
            $mdp          = $_POST['mot_de_passe'] ?? '';
            $confirmation = $_POST['confirmation'] ?? '';

            if (!csrfValide()) {
                $erreurs[] = 'Session expirée, veuillez réessayer.';
            }
            if ($old['nom'] === '') {
                $erreurs[] = 'Le nom est obligatoire.';
            }
            if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
                $erreurs[] = 'Adresse email invalide.';
            }
            if (strlen($mdp) < 10) {
                $erreurs[] = 'Le mot de passe doit contenir au moins 10 caractères.';
            }
            if ($mdp !== $confirmation) {
                $erreurs[] = 'Les deux mots de passe ne correspondent pas.';
            }

            if (!$erreurs) {
                $modele->creer($old['nom'], $old['email'], password_hash($mdp, PASSWORD_DEFAULT), 'admin');
                $_SESSION['flash'] = 'Compte administrateur créé. Vous pouvez vous connecter.';
                $this->redirect('admin/auth/connexion');
            }
        }

        $this->view('admin/auth/installation', [
            'titre'   => 'Créer l\'administrateur',
            'erreurs' => $erreurs,
            'old'     => $old,
        ]);
    }

    // /admin/auth/deconnexion (bouton en POST)
    public function deconnexion(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrfValide()) {
            unset($_SESSION['admin']);
            session_regenerate_id(true);
        }
        $this->redirect('admin/auth/connexion');
    }
}
