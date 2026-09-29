<x-filament-widgets::widget wire:poll.30s>
    <div
        style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:16px;"
    >
        @foreach ($this->getProcessCards() as $card)
            <x-filament::section>
                <div
                    data-target="{{ $card['next_run']->toIso8601String() }}"
                    x-data="{
                        target: null,
                        targetString: '',
                        interval: null,
                        refreshing: false,
                        time: '00:00:00',

                        syncTarget() {
                            const newTargetString = this.$el.dataset.target;

                            // Kalau target dari Livewire berubah,
                            // gunakan target yang baru.
                            if (newTargetString !== this.targetString) {
                                this.targetString = newTargetString;
                                this.target = new Date(newTargetString);

                                // Izinkan refresh berikutnya
                                this.refreshing = false;

                                this.tick();
                            }
                        },

                        tick() {
                            if (!this.target) {
                                return;
                            }

                            // Pastikan target selalu mengikuti
                            // data-target terbaru dari Livewire.
                            this.syncTarget();

                            const diff = this.target - new Date();

                            // Countdown sudah habis
                            if (diff <= 0) {
                                this.time = '00:00:00';

                                // Minta Livewire mengambil data terbaru
                                if (!this.refreshing) {
                                    this.refreshing = true;

                                    this.$wire.$refresh();
                                }

                                return;
                            }

                            const totalSeconds = Math.floor(diff / 1000);

                            const hours = Math.floor(totalSeconds / 3600);
                            const minutes = Math.floor((totalSeconds % 3600) / 60);
                            const seconds = totalSeconds % 60;

                            this.time =
                                String(hours).padStart(2, '0') + ':' +
                                String(minutes).padStart(2, '0') + ':' +
                                String(seconds).padStart(2, '0');
                        },

                        start() {
                            // Ambil target pertama kali
                            this.syncTarget();

                            this.tick();

                            // Update countdown setiap detik
                            this.interval = setInterval(() => {
                                this.tick();
                            }, 1000);
                        },

                        destroy() {
                            clearInterval(this.interval);
                        },
                    }"
                    x-init="start()"
                    style="
                        display:flex;
                        min-height:190px;
                        flex-direction:column;
                        justify-content:space-between;
                        gap:18px;
                    "
                >
                    <div>
                        <div
                            style="
                                display:flex;
                                align-items:center;
                                justify-content:space-between;
                                gap:12px;
                            "
                        >
                            <div
                                style="
                                    display:flex;
                                    align-items:center;
                                    gap:10px;
                                    min-width:0;
                                "
                            >
                                <span
                                    style="
                                        width:10px;
                                        height:10px;
                                        border-radius:9999px;
                                        background-color:{{ $card['accent'] }};
                                        display:inline-block;
                                        flex-shrink:0;
                                    "
                                ></span>

                                <h3
                                    style="
                                        font-size:15px;
                                        font-weight:700;
                                        line-height:1.25;
                                        margin:0;
                                    "
                                >
                                    {{ $card['title'] }}
                                </h3>
                            </div>

                            <span
                                style="
                                    font-size:11px;
                                    font-weight:600;
                                    line-height:1.2;
                                    padding:4px 8px;
                                    border-radius:9999px;
                                    background-color:color-mix(
                                        in srgb,
                                        {{ $card['accent'] }} 12%,
                                        transparent
                                    );
                                    color:{{ $card['accent'] }};
                                    white-space:nowrap;
                                "
                            >
                                {{ $card['batch'] }}
                            </span>
                        </div>

                        <div style="margin-top:14px;">
                            <div
                                style="
                                    font-size:12px;
                                    opacity:0.65;
                                "
                            >
                                Jadwal berikutnya
                            </div>

                            <div
                                style="
                                    font-size:18px;
                                    font-weight:700;
                                    line-height:1.35;
                                "
                            >
                                {{ $card['next_run']->translatedFormat('l, d M Y H:i') }}
                                WIB
                            </div>
                        </div>
                    </div>

                    <div>
                        <div
                            x-text="time"
                            style="
                                font-variant-numeric:tabular-nums;
                                font-size:34px;
                                font-weight:800;
                                line-height:1;
                                color:{{ $card['accent'] }};
                            "
                        ></div>

                        <div
                            style="
                                margin-top:14px;
                                display:flex;
                                justify-content:space-between;
                                gap:12px;
                                align-items:flex-end;
                            "
                        >
                            <div>
                                <div
                                    style="
                                        font-size:20px;
                                        font-weight:700;
                                        line-height:1;
                                    "
                                >
                                    {{ number_format($card['metric'], 0, ',', '.') }}
                                </div>

                                <div
                                    style="
                                        font-size:12px;
                                        opacity:0.65;
                                        line-height:1.35;
                                    "
                                >
                                    {{ $card['metric_label'] }}
                                </div>
                            </div>

                            <div
                                style="
                                    font-size:11px;
                                    opacity:0.55;
                                    text-align:right;
                                    line-height:1.35;
                                "
                            >
                                Terakhir<br>

                                {{ $card['last_run'] ? $card['last_run']->diffForHumans() : '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            </x-filament::section>
        @endforeach
    </div>
</x-filament-widgets::widget>