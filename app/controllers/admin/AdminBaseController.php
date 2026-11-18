<?php
abstract class AdminBaseController extends Controller
{
    protected array $admin = [];

    public function __construct()
    {
        if (empty($_SESSION['admin'])) {
            $this->redirect('admin/auth/connexion');
        }
        $this->admin = $_SESSION['admin'];
    }
   
    protected function viewAdmin(string $view, array $data = []): void
{
    $message       = $_SESSION['flash'] ?? null;
    $messageErreur = $_SESSION['flash_erreur'] ?? null;
    unset($_SESSION['flash'], $_SESSION['flash_erreur']);
    $admin = $this->admin;

    extract($data);
    require APP . '/views/layouts/admin_header.php';
    require APP . "/views/admin/$view.php";
    require APP . '/views/layouts/admin_footer.php';
}

// Message d'erreur affiché en rouge à la page suivante
protected function erreurFlash(string $texte): void
{
    $_SESSION['flash_erreur'] = $texte;
}
    
    // Actions qui modifient des données : POST + jeton CSRF obligatoires
    protected function exigerPost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrfValide()) {
            $_SESSION['flash'] = 'Action refusée (session expirée). Veuillez réessayer.';
            $this->redirect('admin');
        }
    }
    
}