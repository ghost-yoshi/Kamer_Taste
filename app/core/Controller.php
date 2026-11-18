<?php
class Controller
{
    protected function model(string $name): Model
    {
        return new $name();
    }

    protected function view(string $view, array $data = []): void
    {
        extract($data);
        require APP . '/views/layouts/header.php';
        require APP . "/views/$view.php";
        require APP . '/views/layouts/footer.php';
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . url($path));
        exit;
    }

    protected function notFound(): void
    {
        http_response_code(404);
        $this->view('errors/404', ['titre' => 'Page introuvable']);
    }
}