<x-filament-widgets::widget wire:poll.30s>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px;">
        @foreach ($this->getOpdData() as $item)
            <x-filament::section>
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                    <span style="width:8px;height:8px;border-radius:9999px;background-color:{{ $item['status_warna'] }};display:inline-block;flex-shrink:0;"></span>
                    <span style="font-weight:600;font-size:13px;line-height:1.3;">{{ $item['nama'] }}</span>
                </div>

                <div style="display:flex;justify-content:space-between;align-items:baseline;margin-bottom:8px;">
                    <span style="font-size:24px;font-weight:700;">{{ $item['jumlah'] }}</span>
                    <span style="font-size:11px;opacity:0.6;">{{ $item['status_label'] }}</span>
                </div>

                <div style="height:6px;width:100%;overflow:hidden;border-radius:9999px;background-color:rgba(120,120,120,0.15);margin-bottom:8px;">
                    <div style="height:100%;border-radius:9999px;background-color:{{ $item['status_warna'] }};width:{{ $item['persentase'] }}%;transition:width 0.3s;"></div>
                </div>

                <div style="font-size:10px;opacity:0.5;">Update: {{ $item['terakhir'] }}</div>
            </x-filament::section>
        @endforeach
    </div>
</x-filament-widgets::widget>