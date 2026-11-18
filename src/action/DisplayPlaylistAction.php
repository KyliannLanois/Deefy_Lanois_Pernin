<?php

namespace deefy\action;

use deefy\render\PlaylistRenderer;
use deefy\repository\DeefyRepo;

class DisplayPlaylistAction extends Action
{
    public function execute(): string
    {
        $repo = DeefyRepo::getInstance();
        $playlist = $repo->getPlaylist(htmlspecialchars($_GET['id']));

        $_SESSION['playlist'] = $playlist;

        $renderer = new PlaylistRenderer($playlist);
        return $renderer->render();

    }

}