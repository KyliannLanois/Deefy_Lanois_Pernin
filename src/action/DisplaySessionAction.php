<?php
declare(strict_types=1);

namespace deefy\action;

use deefy\render\PlaylistRenderer;
use deefy\objet\Playlist;

class DisplaySessionAction extends Action
{
    public function execute(): string
    {
        if (isset($_SESSION['playlist'])) {
            $playlist = $_SESSION['playlist'];

            $renderer = new PlaylistRenderer($playlist);
            return $renderer->render();
        } else {
            header('Location: ?action=default');
            exit();
        }
    }


}