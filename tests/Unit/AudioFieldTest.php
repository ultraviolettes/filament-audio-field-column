<?php

declare(strict_types=1);

namespace Ultraviolettes\FilamentAudio\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Ultraviolettes\FilamentAudio\Forms\Components\AudioField;
use Ultraviolettes\FilamentAudio\Tests\TestCase;

final class AudioFieldTest extends TestCase
{
    #[Test]
    public function it_can_be_instantiated(): void
    {
        $field = AudioField::make('audio_url');

        $this->assertInstanceOf(AudioField::class, $field);
    }

    #[Test]
    public function it_has_default_values(): void
    {
        $field = AudioField::make('audio_url');

        $this->assertFalse($field->getShowVolume());
        $this->assertTrue($field->getShowDuration()); // AudioField defaults to true
        $this->assertEquals(48, $field->getSize()); // AudioField defaults to 48
        $this->assertEquals('#00bfff', $field->getProgressColor());
    }

    #[Test]
    public function it_can_set_audio_url(): void
    {
        $field = AudioField::make('audio_url')
            ->audioUrl('https://example.com/audio.mp3');

        $this->assertEquals('https://example.com/audio.mp3', $field->getAudioUrl());
    }

    #[Test]
    public function it_can_show_volume_control(): void
    {
        $field = AudioField::make('audio_url')->showVolume();

        $this->assertTrue($field->getShowVolume());
    }

    #[Test]
    public function it_can_hide_duration(): void
    {
        $field = AudioField::make('audio_url')->showDuration(false);

        $this->assertFalse($field->getShowDuration());
    }

    #[Test]
    public function it_can_set_custom_size(): void
    {
        $field = AudioField::make('audio_url')->size(64);

        $this->assertEquals(64, $field->getSize());
    }

    #[Test]
    public function it_can_set_custom_progress_color(): void
    {
        $field = AudioField::make('audio_url')->progressColor('#ff0000');

        $this->assertEquals('#ff0000', $field->getProgressColor());
    }

    #[Test]
    public function it_generates_unique_id(): void
    {
        $field = AudioField::make('audio_url')
            ->audioUrl('https://example.com/audio.mp3');

        $uniqueId = $field->getUniqueId();

        $this->assertStringStartsWith('audio-', $uniqueId);
    }
}
