<?php

namespace deefy\objet;

class Playlist
{
    private int $id;
    private string $name;
    private array $tracks;

    public function __construct(int $id,string $name, array $tracks){
        $this->id = $id;
        $this->name = $name;
        $this->tracks = $tracks;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTracks(): array
    {
        return $this->tracks;
    }

    public function getTracksCount(): int
    {
        return count($this->tracks);
    }


}