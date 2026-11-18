<?php

namespace deefy\dispatch;




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
            throw new RouterException("Action '{$this->action}' not found");
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
        <title>Deefy</title>
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        </head> 
        <body class="bg-gray-800">
            <header class="sticky top-0 left-0 z-50 mb-10">
                <div class="flex justify-between items-center border-b-2 border-[#00E676] bg-gray-900 ">
                    <a href="?action=default"> <img src="image/Logo.png" class="w-32 h-32 p-4 rounded-full" ></a>
                    <h1 class="text-5xl font-bold bg-gradient-to-r from-[#39F5A5] to-[#00E676] bg-clip-text text-transparent">Ecoutez musiques et podcasts !</h1>
                    <nav>
                        <a href="?action=creerPlaylist" class="bg-[#00E676] p-4 m-2 rounded-lg border-black-100 border-1 hover:bg-lime-500"> Créer Playlist</a>
                        <a href="?action=afficherPlaylistSession" class="bg-[#00E676] p-4 m-2 rounded-lg border-black-100 border-1 hover:bg-lime-500"> Afficher ma playlist en cours</a>
                   </nav>
               </div>
           </header>
           
            <main class="flex flex-col justify-center items-center">
               $main
            </main>
            
            <footer>
            </footer>
        </body>
        </html>
        HTML;
        echo $html;
    }

}