<?php

namespace deefy\action;

use deefy\objet\Track;
use deefy\repository\DeefyRepo;
use finfo;

class AddTrackAction extends Action
{


    public function execute(): string
    {
        if(!isset($_SESSION['playlist'])){
            return "<p class= 'text-center text-[#00E676] font-bold text-xl mb-6'>Impossible d'ajouter un track vous n'avez aucune playlist en cours</p>";
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $repo = DeefyRepo::getInstance();


            $playlist = $_SESSION['playlist'];


            $titre = trim($_POST['titre'] ?? '');
            $genre = trim($_POST['genre'] ?? '');
            $duree = (int)($_POST['duree'] ?? 0);
            $type = $_POST['type'] ?? 'A';


            $artiste_album = !empty($_POST['artiste_album']) ? trim($_POST['artiste_album']) : null;
            $titre_album = !empty($_POST['titre_album']) ? trim($_POST['titre_album']) : null;
            $annee_album = !empty($_POST['annee_album']) ? (int)$_POST['annee_album'] : null;
            $numero_album = !empty($_POST['numero_album']) ? (int)$_POST['numero_album'] : null;
            $auteur_podcast = !empty($_POST['auteur_podcast']) ? trim($_POST['auteur_podcast']) : null;
            $date_podcast = !empty($_POST['date_podcast']) ? $_POST['date_podcast'] : null;


            if (!isset($_FILES['audio']) || $_FILES['audio']['error'] === UPLOAD_ERR_NO_FILE) {
                return "<p>Veuillez sélectionner un fichier audio MP3.</p>";
            }

            $file = $_FILES['audio'];
            if ($file['error'] !== UPLOAD_ERR_OK) {
                return "<p class='text-sm dark:text-gray-400'> Erreur dans l'envoi de l'audio</p>";
            }
            $maxSize = 150 * 1024 * 1024;


            if ($file['size'] > $maxSize) {
                return "<p class='text-sm dark:text-gray-400'>L'audio doit faire moins de 150Mo</p>";
            }

            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $formatsOk = [
                'audio/mp3' => 'mp3',
                'audio/mpeg' => 'mp3'
            ];
            $typeFichier = $finfo->file($file['tmp_name']);
            if (!isset($formatsOk[$typeFichier])) {
                return "<p>Format non admis ! mp3 seulement </p>";
            }


            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newFileName = uniqid() . "." . $extension;

            if (!move_uploaded_file($file['tmp_name'], "audio/" . $newFileName)) {
                return "<p>Erreur lors de l'envoie de l'audio</p>";
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
            $track = new Track($trackId, $titre,
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
            $_SESSION['playlist'] = $playlist;
            return $this->renderForm("Votre Musique / Podcast a été ajouté");
        }


        return $this->renderForm("Ajouter une musique ou un podcast");
    }


    public function renderForm(string $texte = ''): string
    {
        return <<<HTML
    <div class="w-full max-w-2xl mx-auto my-8 p-8 bg-gray-900 border border-[#00E676] rounded-xl text-gray-200 shadow-xl">
        <p class="text-center text-[#00E676] font-bold text-xl mb-6">{$texte}</p>

        <form action="?action=add-track" method="post" enctype="multipart/form-data">
            <fieldset class="border border-[#00E676]/40 rounded-lg p-6 mb-6 bg-gray-800">
                <legend class="text-[#00E676] font-bold text-lg px-3">Informations générales</legend>

                <label for="titre" class="block mb-2 font-medium text-gray-200">Titre :</label>
                <input type="text" id="titre" name="titre" placeholder="Ex : Wish You Were Here" required class="w-full p-3 mb-5 bg-gray-900 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-[#00E676] focus:ring-2 focus:ring-[#00E676]/30 transition">

                <label for="genre" class="block mb-2 font-medium text-gray-200">Genre :</label>
                <input type="text" id="genre" name="genre" placeholder="Ex : Rock, Podcast..." required class="w-full p-3 mb-5 bg-gray-900 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-[#00E676] focus:ring-2 focus:ring-[#00E676]/30 transition">

                <label for="duree" class="block mb-2 font-medium text-gray-200">Durée (en secondes) :</label>
                <input type="number" id="duree" name="duree" min="1" placeholder="Ex : 240" required class="w-full p-3 mb-5 bg-gray-900 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-[#00E676] focus:ring-2 focus:ring-[#00E676]/30 transition">

                <label for="audio" class="block mb-2 font-medium text-gray-200">Fichier audio (.mp3) :</label>
                <input type="file" id="audio" name="audio" accept=".mp3,audio/mpeg" required class="w-full p-3 mb-5 bg-gray-900 border border-gray-600 rounded-lg text-white file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-[#00E676] file:text-gray-900 file:font-bold file:cursor-pointer hover:file:bg-[#39F5A5]">

                <label class="block mb-3 font-medium text-gray-200">Type de piste :</label>
                <div class="flex flex-wrap gap-8">
                    <label for="type_a" class="flex items-center gap-2 cursor-pointer text-white"><input type="radio" id="type_a" name="type" value="A" checked onchange="toggleTypeFields()" class="accent-[#00E676] scale-125 cursor-pointer">Musique (A)</label>
                    <label for="type_p" class="flex items-center gap-2 cursor-pointer text-white"><input type="radio" id="type_p" name="type" value="P" onchange="toggleTypeFields()" class="accent-[#00E676] scale-125 cursor-pointer">Podcast (P)</label>
                </div>
            </fieldset>

            <!-- Informations de l'album -->
            <fieldset id="fields_album" class="border border-[#00E676]/40 rounded-lg p-6 mb-6 bg-gray-800">
                <legend class="text-[#00E676] font-bold text-lg px-3">Détails de l'album</legend>

                <label for="artiste_album" class="block mb-2 font-medium text-gray-200">Artiste de l'album :</label>
                <input type="text" id="artiste_album" name="artiste_album" placeholder="Ex : Pink Floyd" class="w-full p-3 mb-5 bg-gray-900 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-[#00E676] focus:ring-2 focus:ring-[#00E676]/30 transition">

                <label for="titre_album" class="block mb-2 font-medium text-gray-200">Titre de l'album :</label>
                <input type="text" id="titre_album" name="titre_album" placeholder="Ex : The Wall" class="w-full p-3 mb-5 bg-gray-900 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-[#00E676] focus:ring-2 focus:ring-[#00E676]/30 transition">

                <label for="annee_album" class="block mb-2 font-medium text-gray-200">Année de l'album :</label>
                <input type="number" id="annee_album" name="annee_album" min="1900" max="2100" placeholder="Ex : 1979" class="w-full p-3 mb-5 bg-gray-900 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-[#00E676] focus:ring-2 focus:ring-[#00E676]/30 transition">

                <label for="numero_album" class="block mb-2 font-medium text-gray-200">Numéro de piste dans l'album :</label>
                <input type="number" id="numero_album" name="numero_album" min="1" placeholder="Ex : 1" class="w-full p-3 bg-gray-900 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-[#00E676] focus:ring-2 focus:ring-[#00E676]/30 transition">
            </fieldset>

            <!-- Informations du podcast -->
            <fieldset id="fields_podcast" hidden class="border border-[#00E676]/40 rounded-lg p-6 mb-6 bg-gray-800">
                <legend class="text-[#00E676] font-bold text-lg px-3">Détails du podcast</legend>

                <label for="auteur_podcast" class="block mb-2 font-medium text-gray-200">Auteur du podcast :</label>
                <input type="text" id="auteur_podcast" name="auteur_podcast" placeholder="Ex : Hugo Décrypte" class="w-full p-3 mb-5 bg-gray-900 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-[#00E676] focus:ring-2 focus:ring-[#00E676]/30 transition">

                <label for="date_podcast" class="block mb-2 font-medium text-gray-200">Date du podcast :</label>
                <input type="date" id="date_podcast" name="date_podcast" class="w-full p-3 bg-gray-900 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-[#00E676] focus:ring-2 focus:ring-[#00E676]/30 transition">
            </fieldset>

            <button type="submit" class="w-full py-3 px-6 bg-[#00E676] hover:bg-[#39F5A5] active:scale-[0.99] text-gray-900 font-bold text-lg rounded-lg shadow-md transition duration-200 cursor-pointer">Ajouter la piste</button>
        </form>
    </div>

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