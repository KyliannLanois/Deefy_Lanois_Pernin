<?php

namespace deefy\action;


use deefy\repository\DeefyRepo;

class DisplayAllPlaylistAction extends Action
{

    public function execute(): string
    {
        $repo = DeefyRepo::getInstance();

        $playlists = $repo->getPlaylists();

        $html = "<div class='flex flex-col justify-center items-center'>";
        foreach($playlists as $playlist){
            $html .= <<<HTML
                    <a href="?action=DisplayPlaylistAction&id={$playlist->getId()}">
                    <div class='flex flex-col justify-center items-center bg-gradient-to-r from-[#39F5A5] to-[#00E676] rounded-lg shadow-md p-4  w-100 mb-20'>
                    <h1 class='text-2xl font-bold text-gray-700'>{$playlist->getName()}</h1>
                    <p class='text-lg text-gray-600'>{$playlist->getTracksCount()} tracks</p>
                    </div>
                    </a>
HTML;

        }
        $html .= "</div>";

        return $html;
    }

}