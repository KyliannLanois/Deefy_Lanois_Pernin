<?php

namespace deefy\action;

use deefy\objet\Track;
use deefy\repository\DeefyRepo;
use finfo;

class AddTrackAction extends Action
{


    public function execute(): string
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $repo = DeefyRepo::getInstance();


            $playlist = unserialize($_SESSION['playlist']);


            $titre = trim($_POST['titre'] ?? '');
            $genre = trim($_POST['genre'] ?? '');
            $duree = (int) ($_POST['duree'] ?? 0);
            $type  = $_POST['type'] ?? 'A';


            $artiste_album  = !empty($_POST['artiste_album'])  ? trim($_POST['artiste_album'])  : null;
            $titre_album    = !empty($_POST['titre_album'])    ? trim($_POST['titre_album'])    : null;
            $annee_album    = !empty($_POST['annee_album'])    ? (int) $_POST['annee_album']    : null;
            $numero_album   = !empty($_POST['numero_album'])   ? (int) $_POST['numero_album']   : null;
            $auteur_podcast = !empty($_POST['auteur_podcast']) ? trim($_POST['auteur_podcast']) : null;
            $date_podcast   = !empty($_POST['date_podcast'])   ? $_POST['date_podcast']         : null;


            if (!isset($_FILES['audio']) || $_FILES['audio']['error'] === UPLOAD_ERR_NO_FILE) {
                return "<p>Veuillez sélectionner un fichier audio MP3.</p>";
            }

            $file = $_FILES['audio'];
            if($file['error'] !== UPLOAD_ERR_OK){
                return "<p class='text-sm dark:text-gray-400'> Erreur dans l'envoi de l'audio</p>";
            }
            $maxSize = 150 * 1024 * 1024;


            if($file['size'] > $maxSize) {
                return "<p class='text-sm dark:text-gray-400'>L'audio doit faire moins de 150Mo</p>";
            }

            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $formatsOk = [
                'audio/mp3' => 'mp3',
                'audio/mpeg' => 'mp3'
            ];
            $typeFichier = $finfo->file($file['tmp_name']);
            if(!isset($formatsOk[$typeFichier])) {
                return "<p>Format non admis ! mp3 seulement </p>";
            }



            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newFileName = uniqid() . "." . $extension;

            if(!move_uploaded_file($file['tmp_name'], "audio/" . $newFileName)){
                return "<p>Erreur lors de l'envoie de la photo</p>";
            }

            $trackId = $repo->insertTrack(
                $titre,
                $genre,
                $duree,
                $newFileName,
                $type,
                $artiste_album,
                $titre_album,
                $annee_album,
                $numero_album,
                $auteur_podcast,
                $date_podcast
            );
            $track = new Track($trackId,$titre,
                $genre,
                $duree,
                $newFileName,
                $type,
                $artiste_album,
                $titre_album,
                $annee_album,
                $numero_album,
                $auteur_podcast,
                $date_podcast);
            $repo->InsertTrack2Playlist($playlist, $track);
            $playlist->addTrack($track);
            $_SESSION['playlist'] = serialize($playlist);
            return $this->renderForm("Votre Musique / Podcast a été ajouté");
        }


        return $this->renderForm("Ajouter une musique ou un podcast");
    }


    public function renderForm(string $texte = ''): string
    {
        return <<<HTML
    <p>{$texte}</p>
    <form action="?action=add-track" method="post" enctype="multipart/form-data">
        <fieldset>
            <legend>Ajouter Une Pistes - Informations générales</legend>

            <label for="titre">Titre :</label>
            <input type="text" id="titre" name="titre" required><br><br>

            <label for="genre">Genre :</label>
            <input type="text" id="genre" name="genre" required><br><br>

            <label for="duree">Durée (en secondes) :</label>
            <input type="number" id="duree" name="duree" min="1" required><br><br>

            <label for="audio">Fichier audio (.mp3) :</label>
            <input type="file" id="audio" name="audio" accept=".mp3,audio/mpeg" required><br><br>

            <span>Type de piste :</span>
            <input type="radio" id="type_a" name="type" value="A" checked onchange="toggleTypeFields()">
            <label for="type_a">Musique (M)</label>

            <input type="radio" id="type_p" name="type" value="P" onchange="toggleTypeFields()">
            <label for="type_p">Podcast (P)</label>
        </fieldset>

        <br>

        <!-- Champs spécifiques à un Album (Type A) -->
        <fieldset id="fields_album">
            <legend>Détails de l'Album</legend>

            <label for="artiste_album">Artiste de l'album :</label>
            <input type="text" id="artiste_album" name="artiste_album"><br><br>

            <label for="titre_album">Titre de l'album :</label>
            <input type="text" id="titre_album" name="titre_album"><br><br>

            <label for="annee_album">Année de l'album :</label>
            <input type="number" id="annee_album" name="annee_album" min="1900" max="2100"><br><br>

            <label for="numero_album">Numéro de piste dans l'album :</label>
            <input type="number" id="numero_album" name="numero_album" min="1">
        </fieldset>

        <!-- Champs spécifiques à un Podcast (Type P) -->
        <fieldset id="fields_podcast" hidden>
            <legend>Détails du Podcast</legend>

            <label for="auteur_podcast">Auteur du podcast :</label>
            <input type="text" id="auteur_podcast" name="auteur_podcast"><br><br>

            <label for="date_podcast">Date du podcast :</label>
            <input type="date" id="date_podcast" name="date_podcast">
        </fieldset>

        <br>
        <button type="submit">Ajouter la piste</button>
    </form>

    <script>
        function toggleTypeFields() {
            const isAlbum = document.getElementById('type_a').checked;
            document.getElementById('fields_album').hidden = !isAlbum;
            document.getElementById('fields_podcast').hidden = isAlbum;
        }
    </script>
HTML;
    }

}