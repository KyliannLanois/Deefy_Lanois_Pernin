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
        $html = "<div class='p-1 rounded-xl bg-gradient-to-r from-[#39F5A5] to-[#00E676]'>";
        $html .= "<section class='w-150 flex flex-col justify-center items-center '>";
        $html .= "<h2 class='w-full text-2xl font-bold bg-gradient-to-r from-[#37F5A9] to-[#00E579] rounded-lg shadow-md p-4 text-gray-800 text-center mb-5'>{$nom}</h2>";

        foreach ($this->playlist->getTracks() as $track) {
            // Assemblage dans la balise <details>
            $html .= "<details class='flex flex-col justify-center items-center mb-3'>";
            $renderer = new TrackRenderer($track);
            $html .= "<summary>" . $renderer->render(Renderer::COMPACT) . "</summary>";
            $html .= "<div class='details-content'>" . $renderer->render(Renderer::LONG) . "</div>";
            $html .= "</details>";
        }

        $html .= "</section>";
        return $html;
    }
}