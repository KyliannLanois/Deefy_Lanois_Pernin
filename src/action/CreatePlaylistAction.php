<?php
declare(strict_types=1);

namespace deefy\action;

use deefy\repository\DeefyRepo;

class CreatePlaylistAction extends Action
{

    public function execute(): string
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $repo = DeefyRepo::getInstance();
            $nomP = trim($_POST["nom"]);
            $playlistId = $repo->InsertPlaylist($nomP);
            if($playlistId === null){
                return $this->renderForm("La playlist que vous essayer de créer existe déjà");
            }
            $_SESSION["playlist"] = $repo->getPlaylist($playlistId);
            return $this->renderForm("Votre Playlist à été ajouté avec succès").<<<HTML
            <a href="?action=afficherPlaylist&id={$playlistId}" class=" w-fit py-3 px-6 bg-[#00E676] hover:bg-[#39F5A5] active:scale-[0.99] text-gray-900 font-bold text-lg rounded-lg shadow-md transition duration-200 cursor-pointer">Cliquer ici pour la visionner</a>
            HTML;
        }


        return $this->renderForm("Création de la playlist");
    }

    public function renderForm($texte): string {
        return <<<HTML
        <div class="w-full max-w-2xl mx-auto my-8 p-8 bg-gray-900 border border-[#00E676] rounded-xl text-gray-200 shadow-xl">
            <p class="text-center text-[#00E676] font-bold text-xl mb-6">{$texte}</p>

            <form action="?action=creerPlaylist" method="post" enctype="multipart/form-data">
                <fieldset class="border border-[#00E676]/40 rounded-lg p-6 mb-6 bg-gray-800">
                    <legend class="text-[#00E676] font-bold text-lg px-3">Information requise</legend>

                    <label for="nom" class="block mb-2 font-medium text-gray-200">Nom de la Playlist :</label>
                    <input type="text" id="nom" name="nom" placeholder="Ex : MaPlaylist Été" required class="w-full p-3 mb-5 bg-gray-900 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-[#00E676] focus:ring-2 focus:ring-[#00E676]/30 transition">
                </fieldset>
                <button type="submit" class="w-full py-3 px-6 bg-[#00E676] hover:bg-[#39F5A5] active:scale-[0.99] text-gray-900 font-bold text-lg rounded-lg shadow-md transition duration-200 cursor-pointer">Créer ma playlist !</button>
            </form>
            
         </div>

HTML;

    }
}