<?php
// Adapte BASE_URL à ton environnement (sans slash final)
// Serveur PHP intégré : http://localhost:8000
// XAMPP/WAMP        : http://localhost:8080/restaurant-ecommerce/public
   define('BASE_URL', 'http://localhost:8080/restaurant-ecommerce/public');
define('APP_NAME', 'Kamer Taste');

// Affichage des erreurs (à désactiver en production)
define('APP_DEBUG', true);   // true en local, FALSE une fois en ligne
ini_set('display_errors', APP_DEBUG ? '1' : '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);
define('FRAIS_LIVRAISON', 1000);   // en FCFA
date_default_timezone_set('Africa/Douala');