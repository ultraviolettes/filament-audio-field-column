@php
    $audioUrl = $getAudioUrl();
    $uniqueId = $getUniqueId();
    $size = $getSize();
    $progressColor = $getProgressColor();
    $showVolume = $getShowVolume();
    $showDuration = $getShowDuration();
    $radius = ($size / 2) - 4;
    $circumference = 2 * M_PI * $radius;
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    @if($audioUrl)
    <div
        x-data="{
            uniqueId: '{{ $uniqueId }}',
            circumference: {{ $circumference }},
            playing: false,
            duration: 0,
            currentTime: 0,
            volume: 1,
            muted: false,
            error: false,

            get progressOffset() {
                if (this.duration === 0) return this.circumference;
                return this.circumference * (1 - this.currentTime / this.duration);
            },

            init() {
                window.addEventListener('filament-audio-playing', (event) => {
                    if (event.detail !== this.uniqueId && this.playing) {
                        this.pause();
                    }
                });
            },

            toggle() {
                if (this.playing) {
                    this.pause();
                } else {
                    this.play();
                }
            },

            play() {
                window.dispatchEvent(new CustomEvent('filament-audio-playing', {
                    detail: this.uniqueId
                }));

                const audio = this.$refs.audio;
                if (audio) {
                    audio.play().catch((err) => {
                        console.error('Audio playback failed:', err);
                        this.error = true;
                    });
                    this.playing = true;
                }
            },

            pause() {
                const audio = this.$refs.audio;
                if (audio) {
                    audio.pause();
                    this.playing = false;
                }
            },

            seek(event) {
                const progressBar = this.$refs.progressBar;
                if (progressBar && this.duration > 0) {
                    const rect = progressBar.getBoundingClientRect();
                    const percent = (event.clientX - rect.left) / rect.width;
                    const newTime = percent * this.duration;
                    this.$refs.audio.currentTime = Math.max(0, Math.min(newTime, this.duration));
                }
            },

            setVolume(value) {
                this.volume = parseFloat(value);
                if (this.$refs.audio) {
                    this.$refs.audio.volume = this.volume;
                }
                this.muted = this.volume === 0;
            },

            toggleMute() {
                this.muted = !this.muted;
                if (this.$refs.audio) {
                    this.$refs.audio.muted = this.muted;
                }
            },

            onLoadedMetadata() {
                if (this.$refs.audio) {
                    this.duration = this.$refs.audio.duration;
                }
            },

            onTimeUpdate() {
                if (this.$refs.audio) {
                    this.currentTime = this.$refs.audio.currentTime;
                }
            },

            onEnded() {
                this.playing = false;
                this.currentTime = 0;
            },

            formatTime(seconds) {
                if (isNaN(seconds) || seconds === 0) return '0:00';
                const mins = Math.floor(seconds / 60);
                const secs = Math.floor(seconds % 60);
                return mins + ':' + secs.toString().padStart(2, '0');
            }
        }"
        style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background-color: #f9fafb; border-radius: 0.5rem; border: 1px solid #e5e7eb;"
        x-cloak
    >
        <div
            style="position: relative; width: {{ $size }}px; height: {{ $size }}px; min-width: {{ $size }}px; cursor: pointer; display: flex; justify-content: center; align-items: center;"
            @click="toggle()"
        >
            {{-- Progress Ring --}}
            <svg
                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; transform: rotate(-90deg); transition: opacity 0.2s;"
                viewBox="0 0 {{ $size }} {{ $size }}"
                x-show="playing || currentTime > 0"
                x-transition
            >
                {{-- Background circle --}}
                <circle
                    stroke="#d1d5db"
                    stroke-width="3"
                    fill="transparent"
                    r="{{ $radius }}"
                    cx="{{ $size / 2 }}"
                    cy="{{ $size / 2 }}"
                />
                {{-- Progress circle --}}
                <circle
                    stroke="{{ $progressColor }}"
                    stroke-width="3"
                    fill="transparent"
                    r="{{ $radius }}"
                    cx="{{ $size / 2 }}"
                    cy="{{ $size / 2 }}"
                    stroke-linecap="round"
                    :stroke-dasharray="circumference"
                    :stroke-dashoffset="progressOffset"
                    style="transition: stroke-dashoffset 0.2s;"
                />
            </svg>

            {{-- Play Button --}}
            <button
                type="button"
                x-show="!playing"
                style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; color: #6b7280; transition: color 0.2s;"
                x-transition
            >
                <x-filament::icon
                    icon="heroicon-o-play-circle"
                    style="width: 100%; height: 100%;"
                />
            </button>

            {{-- Pause Button --}}
            <button
                type="button"
                x-show="playing"
                style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; color: var(--primary-500, #3b82f6); transition: color 0.2s;"
                x-transition
            >
                <x-filament::icon
                    icon="heroicon-o-pause-circle"
                    style="width: 100%; height: 100%;"
                />
            </button>
        </div>

        <div style="flex: 1; min-width: 0;">
            {{-- Progress Bar (clickable for seeking) --}}
            <div
                style="height: 0.5rem; background-color: #e5e7eb; border-radius: 9999px; cursor: pointer; overflow: hidden;"
                @click="seek($event)"
                x-ref="progressBar"
            >
                <div
                    style="height: 100%; border-radius: 9999px; background-color: {{ $progressColor }}; transition: width 0.1s;"
                    :style="{ width: (duration > 0 ? (currentTime / duration * 100) : 0) + '%' }"
                ></div>
            </div>

            @if($showDuration)
            {{-- Time Display --}}
            <div style="display: flex; justify-content: space-between; margin-top: 0.25rem;">
                <span
                    style="font-size: 0.75rem; color: #6b7280; font-family: monospace;"
                    x-text="formatTime(currentTime)"
                >0:00</span>
                <span
                    style="font-size: 0.75rem; color: #6b7280; font-family: monospace;"
                    x-text="formatTime(duration)"
                >0:00</span>
            </div>
            @endif
        </div>

        @if($showVolume)
        {{-- Volume Control --}}
        <div style="display: flex; align-items: center; gap: 0.25rem; flex-shrink: 0;">
            <button
                type="button"
                @click="toggleMute()"
                style="color: #6b7280; transition: color 0.2s;"
            >
                <template x-if="!muted && volume > 0">
                    <x-filament::icon
                        icon="heroicon-o-speaker-wave"
                        style="width: 1.25rem; height: 1.25rem;"
                    />
                </template>
                <template x-if="muted || volume === 0">
                    <x-filament::icon
                        icon="heroicon-o-speaker-x-mark"
                        style="width: 1.25rem; height: 1.25rem;"
                    />
                </template>
            </button>
            <input
                type="range"
                min="0"
                max="1"
                step="0.1"
                x-model="volume"
                @input="setVolume($event.target.value)"
                style="width: 5rem; height: 0.25rem; background-color: #e5e7eb; border-radius: 0.5rem; cursor: pointer;"
            />
        </div>
        @endif

        {{-- Hidden Audio Element --}}
        <audio
            x-ref="audio"
            src="{{ $audioUrl }}"
            preload="metadata"
            @loadedmetadata="onLoadedMetadata()"
            @timeupdate="onTimeUpdate()"
            @ended="onEnded()"
        ></audio>
    </div>
    @else
    <div style="padding: 1rem; background-color: #f9fafb; border-radius: 0.5rem; border: 1px dashed #d1d5db; text-align: center;">
        <x-filament::icon
            icon="heroicon-o-musical-note"
            style="width: 2rem; height: 2rem; margin: 0 auto 0.5rem; color: #9ca3af;"
        />
        <p style="font-size: 0.875rem; color: #6b7280;">
            {{ __('No audio file') }}
        </p>
    </div>
    @endif
</x-dynamic-component>
