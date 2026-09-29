<?php

use App\Models\Berita;
use Livewire\Component;

new class extends Component
{
    public Berita $berita;

    public array $paletteKategori = [
        ['bg' => 'bg-[rgb(var(--cat-1)/0.15)]', 'text' => 'text-[rgb(var(--cat-1))]'],
        ['bg' => 'bg-[rgb(var(--cat-2)/0.15)]', 'text' => 'text-[rgb(var(--cat-2))]'],
        ['bg' => 'bg-[rgb(var(--cat-3)/0.15)]', 'text' => 'text-[rgb(var(--cat-3))]'],
        ['bg' => 'bg-[rgb(var(--cat-4)/0.15)]', 'text' => 'text-[rgb(var(--cat-4))]'],
        ['bg' => 'bg-[rgb(var(--cat-5)/0.15)]', 'text' => 'text-[rgb(var(--cat-5))]'],
        ['bg' => 'bg-[rgb(var(--cat-6)/0.15)]', 'text' => 'text-[rgb(var(--cat-6))]'],
        ['bg' => 'bg-[rgb(var(--cat-7)/0.15)]', 'text' => 'text-[rgb(var(--cat-7))]'],
        ['bg' => 'bg-[rgb(var(--cat-8)/0.15)]', 'text' => 'text-[rgb(var(--cat-8))]'],
        ['bg' => 'bg-[rgb(var(--cat-9)/0.15)]', 'text' => 'text-[rgb(var(--cat-9))]'],
    ];

    public function mount(Berita $berita): void
    {
        abort_unless($berita->ringkasan, 404);

        $this->berita = $berita->load(['opd', 'kategori']);
        $berita->increment('dilihat');
    }

    public function warnaKategori(?int $kategoriId): array
    {
        if (! $kategoriId) {
            return $this->paletteKategori[8];
        }

        return $this->paletteKategori[$kategoriId % count($this->paletteKategori)];
    }

    public function with(): array
    {
        $terbaru = Berita::with('opd')
            ->whereNotNull('ringkasan')
            ->where('id', '!=', $this->berita->id)
            ->latest('tanggal_publish')
            ->take(6)
            ->get();

        $terkait = collect();
        if ($this->berita->kategori_id) {
            $terkait = Berita::with(['opd', 'kategori'])
                ->whereNotNull('ringkasan')
                ->where('kategori_id', $this->berita->kategori_id)
                ->where('id', '!=', $this->berita->id)
                ->latest('tanggal_publish')
                ->take(4)
                ->get();
        }

        return [
            'terbaru' => $terbaru,
            'terkait' => $terkait,
        ];
    }
};

?>

<div>

  <div class="mx-auto max-w-7xl px-4 pt-14 pb-16 sm:px-6 lg:px-8">
    {{-- BREADCRUMB --}}
<div class="mb-7 flex items-center gap-2 font-mono text-xs text-[rgb(var(--c-muted))]">
    <a
        href="{{ route('berita') }}"
        wire:navigate
        class="inline-flex items-center gap-1.5 transition hover:text-[rgb(var(--c-accent))]"
    >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-3.5 w-3.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
        </svg>
        Berita
    </a>

    <span class="text-[rgb(var(--c-muted)/0.5)]">/</span>

    <a
        href="{{ route('berita', ['kategori' => $berita->kategori_id]) }}"
        wire:navigate
        class="transition hover:text-[rgb(var(--c-accent))]"
    >
        {{ $berita->kategori->nama ?? 'Umum' }}
    </a>
</div>

    <div class="grid grid-cols-1 items-start gap-10 lg:grid-cols-3 lg:gap-12">

        {{-- KIRI: DETAIL BERITA --}}
        <article class="min-w-0 lg:col-span-2">

            {{-- TAG --}}
            @php $warna = $this->warnaKategori($berita->kategori_id); @endphp
            <div class="mb-4 flex flex-wrap items-center gap-2">
                <span class="rounded-full border border-[rgb(var(--c-muted)/0.3)] px-3 py-1 font-mono text-[11px] uppercase tracking-wide text-[rgb(var(--c-muted))]">
                    {{ $berita->opd->nama ?? '-' }}
                </span>
                <span class="rounded-full {{ $warna['bg'] }} {{ $warna['text'] }} px-3 py-1 text-xs font-medium">
                    {{ $berita->kategori->nama ?? 'Umum' }}
                </span>
            </div>

            {{-- JUDUL --}}
            <h1 class="font-display text-2xl font-semibold leading-tight text-[rgb(var(--c-tittle))] sm:text-3xl">
                {{ $berita->judul }}
            </h1>

            {{-- META --}}
            <div class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2 font-mono text-xs text-[rgb(var(--c-muted))]">
                <span class="flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-3.5 w-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    {{ $berita->penulis ?: ($berita->sumber ?: ($berita->opd->nama ?? 'Admin')) }}
                </span>
                <span class="flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-3.5 w-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3.75 9h16.5M5.25 5.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25A2.25 2.25 0 0 1 18.75 21H5.25A2.25 2.25 0 0 1 3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25Z" />
                    </svg>
                    {{ $berita->tanggal_publish->translatedFormat('d M Y') }}
                </span>
                <span class="flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-3.5 w-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    {{ number_format($berita->dilihat) }} dilihat
                </span>
            </div>

            <hr class="my-6 border-[rgb(var(--c-muted)/0.15)]">

            {{-- THUMBNAIL --}}
            @if ($berita->thumbnail)
                <img src="{{ $berita->thumbnail }}" alt="{{ $berita->judul }}" class="mb-6 h-[280px] w-full rounded-2xl object-cover sm:h-[380px]">
            @endif

            {{-- DESKRIPSI --}}
            <div
                class="text-[15px] leading-8 text-[rgb(var(--c-body))] sm:text-base sm:leading-[2rem]"
                style="text-align: justify;"
            >
                {!! nl2br(e($berita->isi)) !!}
            </div>

            <hr class="my-6 border-[rgb(var(--c-muted)/0.15)]">

            {{-- BAGIKAN --}}
            <div class="flex flex-wrap items-center gap-3">

                <span class="font-mono text-xs uppercase tracking-wide text-[rgb(var(--c-muted))]">
                    Bagikan:
                </span>

                {{-- Salin Link --}}
                <button
                    x-data="{ copied: false }"
                    @click="
                        navigator.clipboard.writeText(window.location.href);
                        copied = true;
                        setTimeout(() => copied = false, 2000);
                    "
                    type="button"
                    class="relative flex h-9 w-9 items-center justify-center rounded-full border border-[rgb(var(--c-muted)/0.3)] text-[rgb(var(--c-muted))] transition hover:border-[rgb(var(--c-accent))] hover:text-[rgb(var(--c-accent))]"
                    aria-label="Salin link"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244"
                        />
                    </svg>

                    <span
                        x-show="copied"
                        x-transition
                        class="absolute -top-8 whitespace-nowrap rounded bg-[rgb(var(--c-tittle))] px-2 py-1 font-mono text-[10px] text-[rgb(var(--c-bg))]"
                    >
                        Tersalin!
                    </span>
                </button>

                {{-- WhatsApp --}}
                <a
                    href="https://wa.me/?text={{ urlencode($berita->judul . ' - ' . request()->url()) }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="flex h-9 w-9 items-center justify-center rounded-full border border-[rgb(var(--c-muted)/0.3)] text-[rgb(var(--c-muted))] transition hover:border-[rgb(var(--c-accent))] hover:text-[rgb(var(--c-accent))]"
                    aria-label="Bagikan ke WhatsApp"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                        <path d="M12 2a10 10 0 0 0-8.66 15L2 22l5.14-1.32A10 10 0 1 0 12 2Zm5.85 14.24c-.25.7-1.45 1.34-2 1.42-.53.08-1.14.11-1.84-.12a12.7 12.7 0 0 1-5.8-4.06 6.9 6.9 0 0 1-1.44-3.63c0-.99.5-1.47.68-1.67.18-.2.4-.25.53-.25l.38.01c.12 0 .29-.02.44.35.18.44.6 1.53.65 1.64.05.11.08.24.02.38-.06.14-.1.23-.2.35l-.29.34c-.1.1-.2.2-.09.4.11.2.5.86 1.09 1.4.75.7 1.4.93 1.6 1.03.2.1.32.09.45-.05l.6-.7c.15-.19.3-.16.5-.1l1.5.72c.18.09.3.14.34.22.05.08.05.44-.2 1.14Z"/>
                    </svg>
                </a>

                {{-- Instagram --}}
                <button
                    @click="navigator.clipboard.writeText(window.location.href); window.open('https://instagram.com', '_blank')"
                    type="button"
                    class="flex h-9 w-9 items-center justify-center rounded-full border border-[rgb(var(--c-muted)/0.3)] text-[rgb(var(--c-muted))] transition hover:border-[rgb(var(--c-accent))] hover:text-[rgb(var(--c-accent))]"
                    aria-label="Bagikan ke Instagram"
                    title="Link disalin, tempel di Instagram Story kamu"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-4 w-4">
                        <rect x="3" y="3" width="18" height="18" rx="5" />
                        <circle cx="12" cy="12" r="4" />
                        <circle cx="17.2" cy="6.8" r="0.8" fill="currentColor" stroke="none" />
                    </svg>
                </button>

                {{-- PEMBATAS --}}
                <span class="mx-1 h-6 w-px bg-[rgb(var(--c-muted)/0.2)]"></span>

                {{-- Kunjungi Asal Berita --}}
                @if ($berita->link_asal)
                    <a
                        href="{{ $berita->link_asal }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex h-9 items-center gap-2 rounded-full border border-[rgb(var(--c-accent)/0.4)] px-4 font-mono text-xs font-medium text-[rgb(var(--c-accent))] transition hover:border-[rgb(var(--c-accent))] hover:bg-[rgb(var(--c-accent)/0.1)]"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.5"
                            class="h-4 w-4"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M13.5 6H18m0 0v4.5M18 6l-7.5 7.5"
                            />
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M17.25 13.5v4.125A2.375 2.375 0 0 1 14.875 20H6.375A2.375 2.375 0 0 1 4 17.625v-8.5A2.375 2.375 0 0 1 6.375 6.75H10.5"
                            />
                        </svg>

                        Kunjungi Asal Berita
                    </a>
                @endif

            </div>



        </article>

        {{-- KANAN: BERITA TERBARU (TIDAK STICKY) --}}
        <aside class="min-w-0 lg:sticky lg:top-24">
            <div class="rounded-xl border border-[rgb(var(--c-muted)/0.15)] bg-[rgb(var(--c-surface))] p-5">
                <h3 class="mb-4 font-display text-sm font-semibold text-[rgb(var(--c-tittle))]">Berita Terbaru</h3>
                <div class="space-y-4">
                    @foreach ($terbaru as $item)
                        <a href="{{ route('berita.detail', $item) }}" wire:navigate class="group flex gap-3">
                            @if ($item->thumbnail)
                                <img src="{{ $item->thumbnail }}" alt="" class="h-14 w-14 flex-none rounded-lg object-cover">
                            @endif
                            <div class="min-w-0">
                                <p class="line-clamp-2 text-xs font-medium leading-snug text-[rgb(var(--c-tittle))] group-hover:text-[rgb(var(--c-accent))]">
                                    {{ $item->judul }}
                                </p>
                                <p class="mt-1 font-mono text-[10px] text-[rgb(var(--c-muted))]">
                                    {{ $item->tanggal_publish->format('d M Y') }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </aside>

    </div>

    {{-- BERITA LAIN KATEGORI SAMA --}}
    @if ($terkait->isNotEmpty())
        <div class="mt-12">
            <h2 class="mb-5 font-display text-lg font-semibold text-[rgb(var(--c-tittle))]">
                Berita Lainnya di Kategori {{ $berita->kategori->nama ?? 'Ini' }}
            </h2>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($terkait as $item)
                    @php $warnaTerkait = $this->warnaKategori($item->kategori_id); @endphp
                    <a href="{{ route('berita.detail', $item) }}" wire:navigate class="group flex flex-col overflow-hidden rounded-xl border border-[rgb(var(--c-muted)/0.15)] bg-[rgb(var(--c-surface))] transition hover:border-[rgb(var(--c-accent)/0.4)]">
                        @if ($item->thumbnail)
                            <img src="{{ $item->thumbnail }}" alt="" class="h-32 w-full object-cover">
                        @endif
                        <div class="flex flex-1 flex-col p-4">
                            <span class="mb-2 w-fit rounded-full {{ $warnaTerkait['bg'] }} {{ $warnaTerkait['text'] }} px-2 py-0.5 text-[10px] font-medium">
                                {{ $item->kategori->nama ?? 'Umum' }}
                            </span>
                            <h3 class="mb-2 line-clamp-2 font-display text-sm font-semibold leading-snug text-[rgb(var(--c-tittle))] group-hover:text-[rgb(var(--c-accent))]">
                                {{ $item->judul }}
                            </h3>
                            <span class="mt-auto font-mono text-[10px] text-[rgb(var(--c-muted))]">
                                {{ $item->tanggal_publish->format('d M Y') }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

</div>
</div>