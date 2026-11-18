<?php
class HomeController extends Controller
{
    public function index(): void
    {
        $this->view('home/index', [
            'titre'   => 'Accueil',
            'accueil' => true,   // sert à styler la page d'accueil différemment
        ]);
    }
}