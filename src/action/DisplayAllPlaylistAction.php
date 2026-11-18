<?php
declare(strict_types=1);

namespace deefy\action;


use deefy\repository\DeefyRepo;

class DisplayAllPlaylistAction extends Action
{

    public function execute(): string
    {
        $repo = DeefyRepo::getInstance();

        $playlists = $repo->getPlaylists();

        $html = "<h1 class='text-4xl font-bold text-center bg-gradient-to-r from-[#39F5A5] to-[#00E676] bg-clip-text text-transparent mb-20'>Liste des playlists enregistrées</h1>";
        $html .= "<div class='flex flex-col justify-center items-center'>";
        foreach($playlists as $playlist){
            $id = $playlist->getId();
            $name = htmlspecialchars($playlist->getName());
            $count = $playlist->getTracksCount();
            $html .= <<<HTML
                    <a href="?action=afficherPlaylist&id={$id}">
                    <div class='flex flex-col justify-center items-center bg-gradient-to-r from-[#39F5A5] to-[#00E676] rounded-lg shadow-md p-4  w-full mb-20'>
                    <h1 class='text-2xl font-bold text-gray-700'>{$name}</h1>
                    <p class='text-lg text-gray-600'>{$count} tracks</p>
                    </div>
                    </a>
HTML;

        }
        $html .= "</div>";

        return $html;
    }

}