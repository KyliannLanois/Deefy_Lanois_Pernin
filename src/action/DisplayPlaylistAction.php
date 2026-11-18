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
        $html = $renderer->render();

        $html .= <<<HTML
            <a href="?action=addTrack">
                <div class="bg-gray-800 rounded-full p-2 m-1 shadow-md ">
                    <h2 class="text-center text-lg font-bold bg-gradient-to-r from-[#39F5A5] to-[#00E676] bg-clip-text text-transparent">Ajouter un track</h2>
</div>
</a>

HTML;

    return $html;
    }

}