<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';


try {
    DeefyRepo::setConfig(__DIR__ . "/../config/db.config.ini");

    $dispatcher = new Dispatcher();

    $dispatcher->addRoute('default', DisplayAllPlaylistAction::class);
    $dispatcher->addRoute('afficherAllPlaylist',DisplayAllPlaylistAction::class);
    $dispatcher->addRoute('afficherPlaylist',DisplayPlaylistAction::class);
    $dispatcher->addRoute('creerPlaylist',CreatePlaylistAction::class);
    $dispatcher->run();


} catch (Exception $e) {
    echo "<h1>Erreur critique</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}