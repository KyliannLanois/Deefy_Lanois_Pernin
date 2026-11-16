<?php

namespace deefy\repository;

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
     * @return TouiteRepository
     */
    public static function getInstance() : TouiteRepository{
        if (self::$instance === null){
            self::$instance = new TouiteRepository();
        }
        return self::$instance;
    }

}