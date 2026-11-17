<?php

namespace deefy\action;

use deefy\render\PlaylistRenderer;
use deefy\repository\DeefyRepo;

class DisplayAllPlaylistAction extends Action
{

    public function execute(): string
    {
        $repo = DeefyRepo::getInstance();

        $playlists = $repo->getPlaylists();

        $html = "";
        foreach($playlists as $playlist){
            $renderer = new PlaylistRenderer($playlist);
            $html .= $renderer->render();
        }

        return $html;
    }

}