<?php
declare(strict_types=1);
namespace deefy\repository;

use deefy\objet\Playlist;
use deefy\objet\Track;
use Exception;
use PDO;

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
            $track['date_podcast']);
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

    public function insertTrack (string $titre,string $genre,int $duree,string $filename,
                                 string $type,?string $artiste_album =null,?string $titre_album =null,
                                 ?int $annee_album= null,?int $numero_album= null,?string $auteur_podcast= null,?int $date_podcast = null) : int
    {
        $sql = "INSERT INTO track (titre, genre, duree, filename, type, artiste_album, titre_album, annee_album, numero_album, auteur_podcast, date_podcast) VALUES (:titre, :genre, :duree, :filename, :type, :artiste_album, :titre_album, :annee_album, :numero_album, :auteur_podcast, :date_podcast)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            "titre" => $titre,
            "genre" => $genre,
            "duree" => $duree,
            "filename" => $filename,
            "type" => $type,
            "artiste_album" => $artiste_album,
            "titre_album" => $titre_album,
            "annee_album" => $annee_album,
            "numero_album" => $numero_album,
            "auteur_podcast" => $auteur_podcast,
            "date_podcast" => $date_podcast
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function InsertTrack2Playlist(Playlist $playlist,Track $track) : bool{

        $sqlExist = "SELECT count(*) FROM playlist2track WHERE id_pl = :id_playlist AND id_track = :id_track";
        $stmt = $this->pdo->prepare($sqlExist);
        $stmt->execute([
            "id_playlist" => $playlist->getId(),
            "id_track" => $track->getId()
        ]);
        if((int)$stmt->fetchColumn() > 0){
            return false;
        } else{
            $idPlaylist = $playlist->getId();
            $idTrack = $track->getId();
            $sqlCount = "SELECT COUNT(*) FROM playlist2track WHERE id_pl = $idPlaylist";
            $nbPiste = $this->pdo->query($sqlCount)->fetchColumn();


            $sql = "INSERT INTO playlist2track VALUES (:idPlaylist, :idTrack, :no_piste_dans_liste)";
            $stmt = $this->pdo->prepare($sql);
            $noPiste = $nbPiste+1;
            $stmt->execute([
                "idPlaylist" => $idPlaylist,
                "idTrack" => $idTrack,
                "no_piste_dans_liste" => $noPiste
            ]);



            return true;
        }


    }
    public function TrackExists(string $titre, int $duree, string $artiste, int $annee_album): ?Track
    {
        $sql = "SELECT * FROM track WHERE titre = :titre AND duree = :duree AND artiste_album = :artiste_album AND annee_album = :annee_album";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            "titre" => $titre,
            "duree" => $duree,
            "artiste_album" => $artiste,
            "annee_album" => $annee_album
        ]);

        $rs = $stmt->fetch();

        if ($rs === false) {
            return null;
        }

        return $this->getTrack($rs['id']);
    }

    public function InsertPlaylist(string $nom) : ?int
    {
        $sqlExist = "SELECT COUNT(*) FROM playlist WHERE nom = :nom";
        $stmt = $this->pdo->prepare($sqlExist);
        $stmt->execute([
            "nom" => $nom
        ]);
        if((int)$stmt->fetchColumn() > 0){
            return null;
        }
        $sql = "INSERT INTO playlist (nom) VALUES (:nom)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            "nom" => $nom
        ]);
        return (int) $this->pdo->lastInsertId();
    }


}