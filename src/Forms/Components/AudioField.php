<?php

declare(strict_types=1);

namespace Croustibat\FilamentAudio\Forms\Components;

use Closure;
use Filament\Forms\Components\Field;

class AudioField extends Field
{
    protected string $view = 'filament-audio::audio-field';

    protected string|Closure|null $audioUrl = null;

    protected bool $showVolume = false;

    protected bool $showDuration = true;

    protected int $size = 48;

    protected string $progressColor = '#00bfff';

    /**
     * Set the audio URL directly or as a closure.
     * This is used when you want to display an existing audio file.
     */
    public function audioUrl(string|Closure|null $url): static
    {
        $this->audioUrl = $url;

        return $this;
    }

    /**
     * Show volume control.
     */
    public function showVolume(bool $show = true): static
    {
        $this->showVolume = $show;

        return $this;
    }

    /**
     * Show duration display.
     */
    public function showDuration(bool $show = true): static
    {
        $this->showDuration = $show;

        return $this;
    }

    /**
     * Set the size of the player (in pixels).
     */
    public function size(int $size): static
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Set the progress circle color.
     */
    public function progressColor(string $color): static
    {
        $this->progressColor = $color;

        return $this;
    }

    public function getAudioUrl(): ?string
    {
        $url = $this->audioUrl;

        if ($url === null) {
            $url = $this->getState();
        }

        if ($url instanceof Closure) {
            $url = $this->evaluate($url);
        }

        return $url;
    }

    public function getShowVolume(): bool
    {
        return $this->showVolume;
    }

    public function getShowDuration(): bool
    {
        return $this->showDuration;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getProgressColor(): string
    {
        return $this->progressColor;
    }

    public function getUniqueId(): string
    {
        return 'audio-'.md5($this->getAudioUrl() ?? uniqid());
    }
}
