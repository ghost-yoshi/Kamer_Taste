<?php
class ClientController extends Controller
{
    // /client
    public function index(): void
    {
        $this->redirect('client/profil');
    }

    // /client/inscription
    public function inscription(): void
    {
        if (estConnecte()) {
            $this->redirect('client/profil');
        }

        $erreurs = [];
        $old = ['nom' => '', 'prenom' => '', 'telephone' => '', 'email' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($old as $champ => $_) {
                $old[$champ] = trim($_POST[$champ] ?? '');
            }
            $mdp          = $_POST['mot_de_passe'] ?? '';
            $confirmation = $_POST['confirmation'] ?? '';
            $telephone    = preg_replace('/[\s.\-]/', '', $old['telephone']);
            $modele       = $this->model('Client');

            if (!csrfValide()) {
                $erreurs[] = 'Session expirée, veuillez réessayer.';
            }
            if ($old['nom'] === '' || $old['prenom'] === '') {
                $erreurs[] = 'Le nom et le prénom sont obligatoires.';
            }
            if (!preg_match('/^\+?[0-9]{8,15}$/', $telephone)) {
                $erreurs[] = 'Numéro de téléphone invalide.';
            } elseif ($modele->telephoneExiste($telephone)) {
                $erreurs[] = 'Ce numéro de téléphone est déjà utilisé.';
            }
            if ($old['email'] !== '') {
                if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
                    $erreurs[] = 'Adresse email invalide.';
                } elseif ($modele->emailExiste($old['email'])) {
                    $erreurs[] = 'Cette adresse email est déjà utilisée.';
                }
            }
            if (strlen($mdp) < 8) {
                $erreurs[] = 'Le mot de passe doit contenir au moins 8 caractères.';
            }
            if ($mdp !== $confirmation) {
                $erreurs[] = 'Les deux mots de passe ne correspondent pas.';
            }

            if (!$erreurs) {
                $id = $modele->creer([
                    'nom'          => $old['nom'],
                    'prenom'       => $old['prenom'],
                    'telephone'    => $telephone,
                    'email'        => $old['email'],
                    'mot_de_passe' => password_hash($mdp, PASSWORD_DEFAULT),
                ]);

                $this->connecter(['id_client' => $id, 'nom' => $old['nom'], 'prenom' => $old['prenom']]);
                $_SESSION['flash'] = 'Bienvenue ' . $old['prenom'] . ', votre compte a été créé.';
                $this->redirect($this->destination());
            }
        }

        $this->view('client/inscription', [
            'titre'   => 'Inscription',
            'erreurs' => $erreurs,
            'old'     => $old,
        ]);
    }

    // /client/connexion
    public function connexion(): void
    {
        if (estConnecte()) {
            $this->redirect('client/profil');
        }

        $message = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        $erreurs = [];
        $login   = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $login = trim($_POST['login'] ?? '');
            $mdp   = $_POST['mot_de_passe'] ?? '';

            if (!csrfValide()) {
                $erreurs[] = 'Session expirée, veuillez réessayer.';
            } elseif (Throttle::bloque('client')) {
                $erreurs[] = 'Trop de tentatives échouées. Réessayez dans ' . Throttle::FENETRE . ' minutes.';
            } else {
                // Un numéro peut être saisi avec des espaces ou des tirets
                $identifiant = str_contains($login, '@') ? $login : preg_replace('/[\s.\-]/', '', $login);
                $client = $this->model('Client')->findByLogin($identifiant);

                if ($client && password_verify($mdp, $client['mot_de_passe'])) {
                    Throttle::reinitialiser('client');
                    $this->connecter($client);
                    $this->redirect($this->destination());
                }

                Throttle::enregistrerEchec('client', $login);
                // Même message dans tous les cas : on ne révèle pas quel champ est faux
                $erreurs[] = 'Identifiants incorrects.';
            }
        }

        $this->view('client/connexion', [
            'titre'   => 'Connexion',
            'erreurs' => $erreurs,
            'login'   => $login,
            'message' => $message,
        ]);
    }

    // /client/profil
    public function profil(): void
    {
        if (!estConnecte()) {
            $_SESSION['apres_connexion'] = 'client/profil';
            $this->redirect('client/connexion');
        }

        $message = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $this->view('client/profil', [
            'titre'   => 'Mon compte',
            'client'  => $this->model('Client')->find((int) clientConnecte()['id_client']),
            'message' => $message,
        ]);
    }

    // /client/deconnexion (bouton en POST)
    public function deconnexion(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrfValide()) {
            unset($_SESSION['client']);
            session_regenerate_id(true);
        }
        $this->redirect('');
    }

    // Ouvre la session du client (méthode privée : non accessible par l'URL)
    private function connecter(array $client): void
    {
        session_regenerate_id(true);   // évite le vol de session
        $_SESSION['client'] = [
            'id_client' => (int) $client['id_client'],
            'nom'       => $client['nom'],
            'prenom'    => $client['prenom'],
        ];
    }

    // Page vers laquelle renvoyer après connexion (ex. la commande)
    private function destination(): string
    {
        $page = $_SESSION['apres_connexion'] ?? 'client/profil';
        unset($_SESSION['apres_connexion']);
        return $page;
    }
}
