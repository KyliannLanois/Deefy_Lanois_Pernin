<?php

namespace deefy\render;

abstract class PlaylistRendererDefault implements Renderer
{


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