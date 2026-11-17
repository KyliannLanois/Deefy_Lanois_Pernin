<?php

namespace deefy\render;

use ErrorException;


class TrackRenderer extends AudioRenderer
{



    public function renderCompact(): string
    {
        $titre = htmlspecialchars($this->track->getTitre());
        $duree = htmlspecialchars($this->track->getDuree());
        $filename = htmlspecialchars($this->track->getFilename());

        return <<<HTML
        <span><strong>{$titre} - {$duree} secondes</strong> </span>
        <audio controls preload="none" src={$filename}></audio>
        <span class="plus">+ d'infos sur la musique</span>
HTML;
    }

    public function renderLong(): string
    {
        $auteur  = htmlspecialchars($this->track->getAuteurPodcast()??0) ;
        $date = htmlspecialchars($this->track->getDatePodcast()??0);
        $artiste= htmlspecialchars($this->track->getArtisteAlbum()??0);
        $album  = htmlspecialchars($this->track->getTitreAlbum()??0);
        $annee = htmlspecialchars($this->track->getAnneeAlbum()??0);


        $htmlP = <<<HTML
            <p>Auteur : {$auteur} | Date De publication : {$date}</p>
        HTML;

        $htmlM = <<<HTML
            <p>Artiste : {$artiste} | Album : {$album} | Année de sortie : {$annee}</p>
        HTML;


        return match ($this->track->getType()) {
            "A" => $htmlM,
            "P" => $htmlP,
            default => "<p>Type d'audio inconnu</p>",
        };

    }
}