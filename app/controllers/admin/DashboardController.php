<?php
class DashboardController extends AdminBaseController
{
    // /admin
    public function index(): void
    {
        $commandes = $this->model('Commande');

        $this->viewAdmin('dashboard', [
            'titre'     => 'Tableau de bord',
            'menuActif' => 'dashboard',
            'stats'     => $commandes->stats(),
            'recentes'  => $commandes->liste(null, 10),
        ]);
    }
}