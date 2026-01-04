<?php

declare(strict_types=1);

namespace Ultraviolettes\FilamentAudio\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Ultraviolettes\FilamentAudio\Infolists\Components\AudioEntry;
use Ultraviolettes\FilamentAudio\Tests\TestCase;

final class AudioEntryTest extends TestCase
{
    #[Test]
    public function it_can_be_instantiated(): void
    {
        $entry = AudioEntry::make('audio_url');

        $this->assertInstanceOf(AudioEntry::class, $entry);
    }

    #[Test]
    public function it_has_default_values(): void
    {
        $entry = AudioEntry::make('audio_url');

        $this->assertFalse($entry->getShowVolume());
        $this->assertFalse($entry->getShowDuration());
        $this->assertEquals(32, $entry->getSize());
        $this->assertEquals('#00bfff', $entry->getProgressColor());
    }

    #[Test]
    public function it_can_set_audio_url(): void
    {
        $entry = AudioEntry::make('audio_url')
            ->audioUrl('https://example.com/audio.mp3');

        $this->assertEquals('https://example.com/audio.mp3', $entry->getAudioUrl());
    }

    #[Test]
    public function it_can_show_volume_control(): void
    {
        $entry = AudioEntry::make('audio_url')->showVolume();

        $this->assertTrue($entry->getShowVolume());
    }

    #[Test]
    public function it_can_show_duration(): void
    {
        $entry = AudioEntry::make('audio_url')->showDuration();

        $this->assertTrue($entry->getShowDuration());
    }

    #[Test]
    public function it_can_set_custom_size(): void
    {
        $entry = AudioEntry::make('audio_url')->size(48);

        $this->assertEquals(48, $entry->getSize());
    }

    #[Test]
    public function it_can_set_custom_progress_color(): void
    {
        $entry = AudioEntry::make('audio_url')->progressColor('#ff0000');

        $this->assertEquals('#ff0000', $entry->getProgressColor());
    }

    #[Test]
    public function it_generates_unique_id(): void
    {
        $entry = AudioEntry::make('audio_url')
            ->audioUrl('https://example.com/audio.mp3');

        $uniqueId = $entry->getUniqueId();

        $this->assertStringStartsWith('audio-', $uniqueId);
    }
}
