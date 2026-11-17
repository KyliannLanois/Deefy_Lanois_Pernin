<?php

namespace deefy\action;

use deefy\repository\DeefyRepo;

class DisplayAllPlaylistAction extends Action
{

    public function execute(): string
    {
        $repo = DeefyRepo::getInstance();







        return $this->renderForm();
    }

    public function renderForm(): string{
        $html = " ";





        return $html;
    }
}