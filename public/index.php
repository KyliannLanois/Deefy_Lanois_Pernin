<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';


try {
    DeefyRepo::setConfig(__DIR__ . "/../config/db.config.ini");

    $dispatcher = new Dispatcher();

    $dispatcher->addRoute('default', HomeAction::class);
    $dispatcher->addRoute('home', HomeAction::class);
    $dispatcher->addRoute('add-touite', AddTouitesAction::class);
    $dispatcher->addRoute('display-touite', DisplayTouiteAction::class);
    $dispatcher->addRoute('DisplayTouiteOfUser', DisplayTouitesOfUserAction::class);
    $dispatcher->addRoute('connexion', ConnexionAction::class);
    $dispatcher->addRoute('profil', ProfilAction::class);
    $dispatcher->addRoute('like', LikeAction::class);
    $dispatcher->addRoute('dislike', DislikeAction::class);
    $dispatcher->addRoute('deconnexion', DeconnecterAction::class);
    $dispatcher->addRoute('tagPage', DisplayTagPage::class);
    $dispatcher->addRoute('abonnementUser', AbonnerUserAction::class);
    $dispatcher->addRoute('abonnementTag', AbonnerTagAction::class);
    $dispatcher->addRoute('pagePerso', PagePersoAction::class);
    $dispatcher->addRoute('connexionAdmin', ConnectionAdminAction::class);
    $dispatcher->addRoute('adminPage', adminPageAction::class);
    $dispatcher->addRoute('afficherAbonnee', AfficherAbonneeAction::class);
    $dispatcher->addRoute('afficherTendances', TendanceAction::class);
    $dispatcher->run();


} catch (Exception $e) {
    echo "<h1>Erreur critique</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}