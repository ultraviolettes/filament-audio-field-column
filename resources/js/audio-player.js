export default function audioPlayer(uniqueId, circumference) {
    return {
        uniqueId: uniqueId,
        circumference: circumference,
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
            // Listen for other audio players starting
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
            // Dispatch event to stop other players
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

        stop() {
            const audio = this.$refs.audio;
            if (audio) {
                audio.pause();
                audio.currentTime = 0;
                this.playing = false;
                this.currentTime = 0;
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
            const audio = this.$refs.audio;
            if (audio) {
                this.duration = audio.duration;
            }
        },

        onTimeUpdate() {
            const audio = this.$refs.audio;
            if (audio) {
                this.currentTime = audio.currentTime;
            }
        },

        onEnded() {
            this.playing = false;
            this.currentTime = 0;
        },

        onError() {
            this.error = true;
            this.playing = false;
            console.error('Audio loading error for:', this.uniqueId);
        },

        formatTime(seconds) {
            if (isNaN(seconds) || seconds === 0) return '0:00';
            const mins = Math.floor(seconds / 60);
            const secs = Math.floor(seconds % 60);
            return `${mins}:${secs.toString().padStart(2, '0')}`;
        }
    };
}
