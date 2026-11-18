<?php
declare(strict_types=1);

namespace deefy\objet;

class Track
{

    private int $id;
    private string $titre;
    private string $genre;
    private int $duree;
    private string $filename;
    private string $type;
    private ?string $artisteAlbum;
    private ?string $titreAlbum;
    private ?int $anneeAlbum;
    private ?int $numAlbum;
    private ?string $auteurPodcast;
    private ?string $datePodcast;

    /**
     * Constructeur de ma classe Track
     * @param int $id
     * @param string $titre
     * @param string $genre
     * @param int $duree
     * @param string $filename
     * @param string $type
     * @param string|null $artisteAlbum
     * @param string|null $titreAlbum
     * @param int|null $anneeAlbum
     * @param int|null $numAlbum
     * @param string|null $auteurPodcast
     * @param string|null $datePodcast
     */
    public function __construct(int $id, string $titre, string $genre, int $duree, string $filename, string $type, ?string $artisteAlbum= null, ?string $titreAlbum = null, ?int $anneeAlbum= null, ?int $numAlbum= null, ?string $auteurPodcast= null, ?string $datePodcast= null)
    {
        $this->id = $id;
        $this->titre = $titre;
        $this->genre = $genre;
        $this->duree = $duree;
        $this->filename = $filename;
        $this->type = $type;
        $this->artisteAlbum = $artisteAlbum;
        $this->titreAlbum = $titreAlbum;
        $this->anneeAlbum = $anneeAlbum;
        $this->numAlbum = $numAlbum;
        $this->auteurPodcast = $auteurPodcast;
        $this->datePodcast = $datePodcast;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDatePodcast(): ?string
    {
        return $this->datePodcast;
    }

    public function getAuteurPodcast(): ?string
    {
        return $this->auteurPodcast;
    }

    public function getNumAlbum(): ?int
    {
        return $this->numAlbum;
    }

    public function getAnneeAlbum(): ?int
    {
        return $this->anneeAlbum;
    }

    public function getTitreAlbum(): ?string
    {
        return $this->titreAlbum;
    }

    public function getArtisteAlbum(): ?string
    {
        return $this->artisteAlbum;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function getGenre(): string
    {
        return $this->genre;
    }

    public function getDuree(): int
    {
        return $this->duree;
    }







}