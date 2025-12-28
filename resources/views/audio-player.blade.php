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

@if($audioUrl)
<div
    style="display: inline-flex; align-items: center; gap: 0.5rem;"
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
                stroke="currentColor"
                class="text-gray-200 dark:text-gray-700"
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
                class="transition-all duration-200"
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

    @if($showDuration)
    {{-- Duration Display --}}
    <div
        x-show="duration > 0"
        x-cloak
        style="font-size: 0.75rem; color: #6b7280; font-family: monospace; min-width: 60px; white-space: nowrap;"
        x-text="formatTime(currentTime) + ' / ' + formatTime(duration)"
    ></div>
    @endif

    @if($showVolume)
    {{-- Volume Control --}}
    <div class="flex items-center gap-1">
        <button
            type="button"
            @click="toggleMute()"
            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors"
        >
            <template x-if="!muted && volume > 0">
                <x-filament::icon
                    icon="heroicon-o-speaker-wave"
                    class="w-4 h-4"
                />
            </template>
            <template x-if="muted || volume === 0">
                <x-filament::icon
                    icon="heroicon-o-speaker-x-mark"
                    class="w-4 h-4"
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
            class="w-16 h-1 bg-gray-200 dark:bg-gray-700 rounded-lg appearance-none cursor-pointer"
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
<div class="text-gray-400 dark:text-gray-500 text-sm italic">
    {{ __('No audio available') }}
</div>
@endif
