<?php

namespace deefy\repository;

use deefy\objet\Playlist;
use deefy\objet\Track;

class DeefyRepo
{
    private static ?array $config= null;
    private static ?DeefyRepo $instance=null;
    private PDO $pdo;


    /***
     * Initialise la connexion à la base de données
     * @throws Exception
     */
    public function __construct (){
        if (self::$config === null){
            throw new Exception("Le fichier config n'a pas été chargée");
        }

        $driver = self::$config['driver'] ?? 'mysql';
        $host = self::$config['host'] ?? 'localhost';
        $database = self::$config['database'] ?? '';
        $user = self::$config['username'] ?? '';
        $pass = self::$config['password'] ?? '';

        $dsn = "{$driver}:host={$host};dbname={$database};charset=utf8";

        $this->pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }

    /***
     * Initialise la configuration de la base de données
     * @param String $file
     * @return void
     * @throws Exception
     */
    public static function setConfig (String $file): void{
        $conf = parse_ini_file($file);
        if ($conf === false){
            throw new Exception("Impossible de lire le fichier de configuratino");
        }
        self::$config = $conf;
    }

    /***
     * Utilisé partout dans l'application, renvoie une instance de TouiteRepository avec la configuration de la base de données et connection.
     * @return DeefyRepo
     */
    public static function getInstance() : DeefyRepo{
        if (self::$instance === null){
            self::$instance = new DeefyRepo();
        }
        return self::$instance;
    }


    public function getTracks () : array {
        $tracks = [];
        $sql = "SELECT * FROM track";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        foreach ($stmt->fetchAll() as $track){
            $tracks[] = new Track(
                (int) $track['id'],
                $track['titre'],
                $track['genre'],
                (int) $track['duree'],
                $track['filename'],
                $track['type'],
                $track['artiste_album'],
                $track['titre_album'],
                (int)$track['annee_album'],
                (int)$track['numero_album'],
                $track['auteur_podcast'],
                (int)$track['date_podcast']);
        }

        return $tracks;
    }

    public function getTrack (int $id) : Track{
        $sql = "SELECT * FROM track WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            "id" => $id
        ]);
        $track = $stmt->fetch();
        return new Track(
            (int) $track['id'],
            $track['titre'],
            $track['genre'],
            (int) $track['duree'],
            $track['filename'],
            $track['type'],
            $track['artiste_album'],
            $track['titre_album'],
            (int)$track['annee_album'],
            (int)$track['numero_album'],
            $track['auteur_podcast'],
            (int)$track['date_podcast']);
    }

    public function buildPlaylistTrack(int $id_pl) : array {
        $tracksId = [];
        $sql = "SELECT * FROM playlist2track 
         WHERE id_pl = :id_pl ORDER BY no_piste_dans_liste ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            "id_pl" => $id_pl
        ]);
        foreach ($stmt->fetchAll() as $track){
            $tracksId[] = $track['id_track'];
        }
        $trackList = [];
        foreach ($tracksId as $trackId){
            $trackList[] = $this->getTrack($trackId);
        }

        return $trackList;
    }

    public function getPlaylists () : array{
        $playlists = [];
        $sql = "SELECT * FROM playlist";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        foreach ($stmt->fetchAll() as $playlist){
            $playlists[] = new Playlist(
                $playlist['id'],
                $playlist['nom'],
                $this->buildPlaylistTrack($playlist['id'])
            );
        }
        return $playlists;
    }

    public function getPlaylist (int $id) : Playlist{
        $sql = "SELECT * FROM playlist WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            "id" => $id
        ]);
        $playlist = $stmt->fetch();
        return new Playlist(
            $playlist['id'],
            $playlist['nom'],
            $this->buildPlaylistTrack($playlist['id'])
        );

    }



}