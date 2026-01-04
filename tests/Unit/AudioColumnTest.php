<?php

declare(strict_types=1);

namespace Ultraviolettes\FilamentAudio\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Ultraviolettes\FilamentAudio\Tables\Columns\AudioColumn;
use Ultraviolettes\FilamentAudio\Tests\TestCase;

final class AudioColumnTest extends TestCase
{
    #[Test]
    public function it_can_be_instantiated(): void
    {
        $column = AudioColumn::make('audio_url');

        $this->assertInstanceOf(AudioColumn::class, $column);
    }

    #[Test]
    public function it_has_default_values(): void
    {
        $column = AudioColumn::make('audio_url');

        $this->assertFalse($column->getShowVolume());
        $this->assertFalse($column->getShowDuration());
        $this->assertEquals(32, $column->getSize());
        $this->assertEquals('#00bfff', $column->getProgressColor());
    }

    #[Test]
    public function it_can_set_audio_url(): void
    {
        $column = AudioColumn::make('audio_url')
            ->audioUrl('https://example.com/audio.mp3');

        $this->assertEquals('https://example.com/audio.mp3', $column->getAudioUrl());
    }

    #[Test]
    public function it_can_show_volume_control(): void
    {
        $column = AudioColumn::make('audio_url')->showVolume();

        $this->assertTrue($column->getShowVolume());
    }

    #[Test]
    public function it_can_show_duration(): void
    {
        $column = AudioColumn::make('audio_url')->showDuration();

        $this->assertTrue($column->getShowDuration());
    }

    #[Test]
    public function it_can_set_custom_size(): void
    {
        $column = AudioColumn::make('audio_url')->size(48);

        $this->assertEquals(48, $column->getSize());
    }

    #[Test]
    public function it_can_set_custom_progress_color(): void
    {
        $column = AudioColumn::make('audio_url')->progressColor('#ff0000');

        $this->assertEquals('#ff0000', $column->getProgressColor());
    }

    #[Test]
    public function it_generates_unique_id(): void
    {
        $column = AudioColumn::make('audio_url')
            ->audioUrl('https://example.com/audio.mp3');

        $uniqueId = $column->getUniqueId();

        $this->assertStringStartsWith('audio-', $uniqueId);
    }

    #[Test]
    public function it_is_fluent(): void
    {
        $column = AudioColumn::make('audio_url')
            ->audioUrl('https://example.com/audio.mp3')
            ->showVolume()
            ->showDuration()
            ->size(64)
            ->progressColor('#10b981');

        $this->assertEquals('https://example.com/audio.mp3', $column->getAudioUrl());
        $this->assertTrue($column->getShowVolume());
        $this->assertTrue($column->getShowDuration());
        $this->assertEquals(64, $column->getSize());
        $this->assertEquals('#10b981', $column->getProgressColor());
    }
}
