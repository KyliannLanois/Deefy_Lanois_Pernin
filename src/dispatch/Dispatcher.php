<?php

namespace touiteur\dispatch;

use touiteur\exception\RouterException;
use src\action\Action;


/**
 * Répartiteur central chargé de sélectionner et d’exécuter une action applicative
 * à partir du paramètre de requête correspondant.
 *
 * Cette classe associe des noms d’actions à des classes exécutables, instancie
 * l’action demandée, récupère le contenu produit par celle-ci puis l’intègre dans
 * la page principale de l’application.
 *
 * Elle assure également la construction de l’enveloppe HTML commune, incluant la
 * navigation, les icônes SVG et les liens adaptés à l’état de session de
 * l’utilisateur.
 *
 * Une exception est levée lorsqu’aucune route enregistrée ne correspond à
 * l’action demandée.
 */
class Dispatcher
{
    protected ?string $action = null;
    protected $actions = [];

    public function __construct()
    {
        $this->action = $_GET['action'] ?? 'default';
    }

    public function addRoute(string $action, string $action_class): Dispatcher
    {
        $this->actions[$action] = $action_class;
        return $this;
    }

    public function run(): void {
        if (!isset($this->actions[$this->action])) {
            throw new RouterException("Action '{$this->action}' not found"); //TODO routerException
        }

        $class  = $this->actions[$this->action];
        $action = new $class();
        $main   = $action->execute();   // on récupère le HTML

        $this->renderPage($main);       // c'est renderPage qui fait le echo
    }



    /**
     * Génère et affiche la page complète de l’application avec la navigation, le contenu principal et le pied de page.
     * Recupère le rendu des actions et l'intègre pour l'affichage.
     *
     * @param string $main Contenu principal à intégrer dans la page.
     * @return void
     */
    private function renderPage(string $main): void
    {
        if (isset($_SESSION['user'])){
            $user = $_SESSION['user'];
        } else {
            $user = 'Connexion';
        }
        $html = <<<HTML
        <!DOCTYPE html>
        <html lang="fr">
        <head>
        <meta charset="UTF-8">
        <title>Touiteur</title>
        </head>
        <body>
        <div>
        

            <header>
            <div>
            <a href="?action=default" <span>Deefy App</span> </a>
            <nav>
            
            <a href="?action=afficherAllPlaylist" Mes Playlist</a>
            <a href="?action=creePlaylist" Crée Playlist</a>
            <a href="?action=afficherPlaylist" Afficher ma playlist en cours</a>
     
           </nav>
</div>
           </header>
           <main >
           <div>
           
           $main
</div>
           </main>
           </div>
           <footer>
            </footer>
           </body>
HTML;
        echo $html;
    }

}