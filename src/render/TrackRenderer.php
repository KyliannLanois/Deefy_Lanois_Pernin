<?php
declare(strict_types=1);
namespace deefy\render;

use ErrorException;


class TrackRenderer extends AudioRenderer
{



    public function renderCompact(): string
    {
        $titre = htmlspecialchars($this->track->getTitre());
        $duree = $this->track->getDuree();
        $filename = htmlspecialchars($this->track->getFilename());

        return <<<HTML
        <span><strong>{$titre} - {$duree} secondes -  cliquez pour + d'infos </strong></span>
        
        <audio controls preload="none" src="audio/{$filename}"></audio>
        
HTML;
    }

    public function renderLong(): string
    {
        $auteur  = htmlspecialchars($this->track->getAuteurPodcast()??"") ;
        $date = htmlspecialchars($this->track->getDatePodcast()??"");
        $artiste= htmlspecialchars($this->track->getArtisteAlbum()??"");
        $album  = htmlspecialchars($this->track->getTitreAlbum()??"");
        $annee = $this->track->getAnneeAlbum()??0;


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