<?php

namespace deefy\render;

use deefy\objet\Playlist;

class PlaylistRenderer implements Renderer
{
    private Playlist $playlist;

    public function __construct($playlist)
    {
        $this->playlist = $playlist;
    }

    public function render(int $selector = 1): string {

        $nom = htmlspecialchars($this->playlist->getName());
        $html = "<section class='playlist'><h2>{$nom}</h2>";

        foreach ($this->playlist->getTracks() as $track) {
            // Assemblage dans la balise <details>
            $html .= "<details>";
            $renderer = new TrackRenderer($track);
            $html .= "<summary>" . $renderer->render(Renderer::COMPACT) . "</summary>";
            $html .= "<div class='details-content'>" . $renderer->render(Renderer::LONG) . "</div>";
            $html .= "</details>";
        }

        $html .= "</section>";
        return $html;
    }





}