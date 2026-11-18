<?php
declare(strict_types=1);
namespace deefy\render;

use deefy\objet\Track;

abstract class AudioRenderer implements Renderer
{

    protected Track $track;

    /**
     * Constructeur de AudioRenderer
     * @param Track $track
     */
    public function __construct(Track $track)
    {
        $this->track = $track;
    }


    public function render(int $selector = 1): string
    {
        return match ($selector) {
            self::LONG => $this->renderLong(),
            default => $this->renderCompact(),
        };
    }

    public abstract function renderCompact(): string;

    public abstract function renderLong(): string;


}