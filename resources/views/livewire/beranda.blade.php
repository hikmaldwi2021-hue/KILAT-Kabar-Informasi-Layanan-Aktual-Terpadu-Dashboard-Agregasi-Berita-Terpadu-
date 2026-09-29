<?php

use App\Models\Berita;
use App\Models\Kategori;
use App\Models\Opd;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    | $cariInput = isi input search
    | $cari      = keyword yang benar-benar digunakan untuk query
    |--------------------------------------------------------------------------
    */

    public string $cariInput = '';

    #[Url]
    public string $cari = '';

    #[Url]
    public string $opdId = '';

    #[Url]
    public string $kategoriId = '';

    protected array $paletteKategori = [
        ['bg' => 'bg-[rgb(var(--cat-1)/0.15)]', 'text' => 'text-[rgb(var(--cat-1))]'],
        ['bg' => 'bg-[rgb(var(--cat-2)/0.15)]', 'text' => 'text-[rgb(var(--cat-2))]'],
        ['bg' => 'bg-[rgb(var(--cat-3)/0.15)]', 'text' => 'text-[rgb(var(--cat-3))]'],
        ['bg' => 'bg-[rgb(var(--cat-4)/0.15)]', 'text' => 'text-[rgb(var(--cat-4))]'],
        ['bg' => 'bg-[rgb(var(--cat-5)/0.15)]', 'text' => 'text-[rgb(var(--cat-5))]'],
        ['bg' => 'bg-[rgb(var(--cat-6)/0.15)]', 'text' => 'text-[rgb(var(--cat-6))]'],
        ['bg' => 'bg-[rgb(var(--cat-7)/0.15)]', 'text' => 'text-[rgb(var(--cat-7))]'],
        ['bg' => 'bg-[rgb(var(--cat-8)/0.15)]', 'text' => 'text-[rgb(var(--cat-8))]'],
        ['bg' => 'bg-[rgb(var(--cat-9)/0.15)]', 'text' => 'text-[rgb(var(--cat-9))]'],
        ['bg' => 'bg-[rgb(var(--cat-10)/0.15)]', 'text' => 'text-[rgb(var(--cat-10))]'],
        ['bg' => 'bg-[rgb(var(--cat-11)/0.15)]', 'text' => 'text-[rgb(var(--cat-11))]'],
    ];

    public function warnaKategori(?int $kategoriId): array
    {
        if (! $kategoriId) {
            return $this->paletteKategori[8];
        }

        return $this->paletteKategori[
            $kategoriId % count($this->paletteKategori)
        ];
    }

    protected function getHeroList()
    {
        return Berita::with(['opd', 'kategori'])
            ->whereNotNull('thumbnail')
            ->orderByRaw('ringkasan IS NULL')
            ->orderByDesc('tanggal_publish')
            ->take(5)
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Filter langsung aktif
    |--------------------------------------------------------------------------
    */

    public function updatedOpdId()
    {
        $this->resetPage();
    }

    public function updatedKategoriId()
    {
        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | Search hanya aktif ketika tombol Cari ditekan
    |--------------------------------------------------------------------------
    */

    public function terapkanFilter()
    {
        $this->cari = trim($this->cariInput);

        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | Reset semua filter
    |--------------------------------------------------------------------------
    */

    public function resetFilter()
    {
        $this->reset([
            'cariInput',
            'cari',
            'opdId',
            'kategoriId',
        ]);

        $this->resetPage();
    }

    public function with(): array
    {
        $heroList = $this->getHeroList();

        $query = Berita::with(['opd', 'kategori'])
            ->whereNotNull('ringkasan')
            ->whereNotIn('id', $heroList->pluck('id'))
            ->latest('tanggal_publish');

        /*
        |--------------------------------------------------------------------------
        | Filter OPD
        |--------------------------------------------------------------------------
        */

        if ($this->opdId) {
            $query->where('opd_id', $this->opdId);
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Kategori
        |--------------------------------------------------------------------------
        */

        if ($this->kategoriId) {
            $query->where('kategori_id', $this->kategoriId);
        }

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($this->cari) {
            $keyword = $this->cari;

            $query->where(function ($q) use ($keyword) {
                $q->where('judul', 'like', "%{$keyword}%")
                    ->orWhere('ringkasan', 'like', "%{$keyword}%");
            });
        }

        return [
            'heroList' => $heroList,

            'berita' => $query->paginate(9),

            'opdList' => Opd::orderBy('nama')->get(),

            'kategoriList' => Kategori::orderBy('nama')->get(),

            'ringkasanMingguan' =>
                \App\Models\RingkasanPeriodik::where('tipe', 'mingguan')
                    ->latest('created_at')
                    ->first(),
        ];
    }
};

?>

<div class="space-y-10">

        {{-- HERO --}}
        @if ($heroList->isNotEmpty())
            <div
                x-data="{
                index: 0,
                total: {{ $heroList->count() }},
                dragging: false,
                startX: 0,
                currentX: 0,
                timer: null,

                init() {
                    this.$nextTick(() => {
                        this.startAutoPlay();
                    });
                },

                startAutoPlay() {
                    clearInterval(this.timer);

                    this.timer = setInterval(() => {
                        this.next();
                    }, 10000);
                },

                resetAutoPlay() {
                    clearInterval(this.timer);
                    this.startAutoPlay();
                },

                go(i) {
                    this.index = i;
                    this.currentX = 0;
                    this.resetAutoPlay();
                },

                next() {
                    this.index = this.index < this.total - 1
                        ? this.index + 1
                        : 0;

                    this.currentX = 0;
                },

                prev() {
                    this.index = this.index > 0
                        ? this.index - 1
                        : this.total - 1;

                    this.currentX = 0;
                },

                dragStart(e) {
                    if (e.target.closest('a, button')) return;

                    this.dragging = true;
                    this.startX = e.touches
                        ? e.touches[0].pageX
                        : e.pageX;

                    this.currentX = 0;
                },

                dragMove(e) {
                    if (!this.dragging) return;

                    const x = e.touches
                        ? e.touches[0].pageX
                        : e.pageX;

                    this.currentX = x - this.startX;
                },

                dragEnd() {
                    if (!this.dragging) return;

                    this.dragging = false;

                    const width = this.$refs.viewport.clientWidth;
                    const threshold = width * 0.15;

                    if (this.currentX < -threshold) {
                        this.next();
                        this.resetAutoPlay();
                    }
                    else if (this.currentX > threshold) {
                        this.prev();
                        this.resetAutoPlay();
                    }

                    this.currentX = 0;
                },

                get offset() {
                    const width = this.$refs.viewport
                        ? this.$refs.viewport.clientWidth
                        : 0;

                    return -(this.index * width) + this.currentX;
                },

                destroy() {
                    clearInterval(this.timer);
                }
            }"
            x-init="init()"
             
            >
                <div
                    x-ref="viewport"
                    class="relative select-none overflow-hidden"
                    :class="dragging ? 'cursor-grabbing' : 'cursor-grab'"
                    @mousedown="dragStart($event)"
                    @mousemove.prevent="dragMove($event)"
                    @mouseup="dragEnd()"
                    @mouseleave="dragEnd()"
                    @touchstart="dragStart($event)"
                    @touchmove="dragMove($event)"
                    @touchend="dragEnd()"
                >
                    <div
                        class="flex"
                        :style="`transform: translateX(${offset}px); transition: ${dragging ? 'none' : 'transform 0.45s cubic-bezier(0.22,1,0.36,1)'}`"
                    >
                        @foreach ($heroList as $hero)
                            @php $warnaHero = $this->warnaKategori($hero->kategori_id); @endphp
                            <div class="w-full flex-none">
                                <div class="relative">
                                    <img src="{{ $hero->thumbnail }}" alt=""  class="h-[300px] w-full object-cover pointer-events-none sm:h-[400px] lg:h-[490px]" draggable="false">

                                    <div class="absolute inset-0 bg-gradient-to-t from-[#0F0D0B] via-[#0F0D0B]/60 to-transparent"></div>
                                        <div class="absolute inset-x-0 bottom-0 p-4 sm:p-7 lg:p-10">
                                        <div class="mb-3 flex flex-wrap items-center gap-2">

                                            {{-- OPD --}}
                                            <span
                                                class="rounded-full border border-[#D4A853]/70 bg-[#211E19]/90 px-3 py-1 font-mono text-[11px] uppercase tracking-wide text-[rgb(var(--c-accent))] shadow-lg backdrop-blur-sm"
                                            >
                                                {{ $hero->opd->nama ?? '-' }}
                                            </span>

                                            {{-- KATEGORI --}}
                                            <span
                                                class="rounded-full border {{ $warnaHero['text'] }} bg-[#211E19]/90 px-3 py-1 text-xs font-medium shadow-lg backdrop-blur-sm"
                                            >
                                                {{ $hero->kategori->nama ?? 'Umum' }}
                                            </span>

                                        </div>

                                        <h1 class="max-w-3xl font-display text-xl font-semibold leading-tight text-[rgb(var(--c-heading))] sm:text-2xl lg:text-4xl">
                                            {{ $hero->judul }}
                                        </h1>

                                        @if ($hero->ringkasan)
                                            <p class="mt-2 max-w-2xl text-xs leading-relaxed text-white/70 sm:mt-3 sm:text-base">
                                                {{ Str::limit($hero->ringkasan, 160) }}
                                            </p>
                                        @endif

                                        <div class="mt-4 flex flex-wrap items-center gap-x-3 gap-y-2 sm:mt-5 sm:gap-4">
                                            
                                            <a href="{{ route('berita.detail', $hero) }}"
                                                wire:navigate
                                                class="rounded-lg bg-[rgb(var(--c-accent))] px-3.5 py-2 text-xs font-semibold text-[rgb(var(--c-accent-contrast))] transition hover:bg-[rgb(var(--c-accent-hover))] sm:px-4 sm:text-sm"
                                            >
                                                Baca Selengkapnya
                                            </a>

                                            <span class="font-mono text-xs text-white/50">
                                                {{ $hero->tanggal_publish->translatedFormat('d M Y') }}
                                            </span>

                                            <span class="flex items-center gap-1.5 font-mono text-xs text-white/50">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-3.5 w-3.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                </svg>
                                                {{ number_format($hero->dilihat) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if ($heroList->count() > 1)
                    <div class="mt-4 flex items-center justify-center gap-3">
                        <button
                            @click="prev(); resetAutoPlay()"
                            class="flex h-8 w-8 items-center justify-center rounded-full border border-[#D4A853] text-[rgb(var(--c-accent))] transition hover:bg-[rgb(var(--c-accent))] hover:text-[rgb(var(--c-accent-contrast))]"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                            </svg>
                        </button>

                        <div class="flex items-center gap-2">
                            @foreach ($heroList as $i => $hero)
                                <button
                                    @click="go({{ $i }})"
                                    :class="index === {{ $i }} ? 'w-8 bg-[rgb(var(--c-accent))]' : 'w-2.5 bg-[rgb(var(--c-surface))] border border-[#D4A853]/40'"
                                    class="h-2.5 rounded-full transition-all duration-300"
                                ></button>
                            @endforeach
                        </div>

                        <button
                            @click="next(); resetAutoPlay()"
                            class="flex h-8 w-8 items-center justify-center rounded-full border border-[#D4A853] text-[rgb(var(--c-accent))] transition hover:bg-[rgb(var(--c-accent))] hover:text-[rgb(var(--c-accent-contrast))]"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </button>
                    </div>
                @endif
            </div>
        @endif
                
                
               {{-- =========================================================
                    WEEKLY BRIEF
                ========================================================== --}}
                <div class="mb-8">
                    <div
                        class="group relative overflow-hidden rounded-2xl border border-[#D4A853]/20 bg-[rgb(var(--c-surface))]"
                    >

                        {{-- Ambient Glow --}}
                        <div
                            class="pointer-events-none absolute -right-32 -top-32 h-72 w-72 rounded-full bg-[rgb(var(--c-accent))]/[0.06] blur-3xl transition duration-700 group-hover:bg-[rgb(var(--c-accent))]/[0.09]"
                        ></div>

                        <div
                            class="pointer-events-none absolute -bottom-32 -left-32 h-64 w-64 rounded-full bg-[rgb(var(--c-accent))]/[0.03] blur-3xl"
                        ></div>

                        {{-- Decorative Line --}}
                        <div
                            class="pointer-events-none absolute left-0 top-0 h-full w-[2px] bg-gradient-to-b from-[#D4A853] via-[#D4A853]/30 to-transparent"
                        ></div>

                        <div class="relative p-4 sm:p-6 lg:p-8">

                            {{-- HEADER --}}
                            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">

                                <div class="flex items-start gap-4">

                                    {{-- Icon --}}
                                    <div
                                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-[#D4A853]/15 bg-[rgb(var(--c-accent))]/10"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.4"
                                            stroke="currentColor"
                                            class="h-5 w-5 text-[rgb(var(--c-accent))]"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.847a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.847.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.456-2.456L14.25 6l1.035-.259a3.375 3.375 0 0 0 2.456-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z"
                                            />
                                        </svg>
                                    </div>

                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="font-mono text-[10px] font-semibold uppercase tracking-[0.22em] text-[rgb(var(--c-accent))]"
                                            >
                                                Weekly Brief
                                            </span>

                                            <span class="h-1 w-1 rounded-full bg-[rgb(var(--c-accent))]/50"></span>

                                            <span class="font-mono text-[10px] uppercase tracking-wider text-[rgb(var(--c-muted))]/60">
                                                Insight
                                            </span>
                                        </div>

                                        <h2
                                            class="mt-1 font-display text-lg font-semibold tracking-tight text-[rgb(var(--c-tittle))] sm:text-xl lg:text-2xl"
                                            Ringkasan Berita Pemerintahan
                                        </h2>

                                        <p class="mt-1 text-xs text-[rgb(var(--c-muted))]">
                                            Ikhtisar perkembangan berita OPD minggu ini
                                        </p>
                                    </div>

                                </div>

                                {{-- UPDATED --}}
                                @if ($ringkasanMingguan)
                                    <div class="shrink-0">
                                        <span class="font-mono text-[10px] uppercase tracking-wider text-[rgb(var(--c-muted))]/60">
                                            Diperbarui
                                        </span>

                                        <p class="mt-0.5 font-mono text-[11px] text-[rgb(var(--c-tittle))]">
                                            {{ $ringkasanMingguan->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                @endif

                            </div>


                            {{-- CONTENT --}}
                            @if ($ringkasanMingguan)

                                <div class="mt-7">

                                    {{-- Editorial Quote --}}
                                    <div class="relative border-l border-[#D4A853]/40 pl-5 sm:pl-6">

                                        <div
                                            class="absolute -left-[5px] top-0 h-2.5 w-2.5 rounded-full bg-[rgb(var(--c-accent))] shadow-[0_0_12px_rgba(212,168,83,0.35)]"
                                        ></div>

                                        <p
                                            class="max-w-5xl text-sm leading-7 text-[rgb(var(--c-tittle))] sm:text-[15px] sm:leading-8"
                                        >
                                            {{ $ringkasanMingguan->narasi }}
                                        </p>

                                    </div>

                                </div>


                                {{-- FOOTER META --}}
                                <div
                                    class="mt-7 flex flex-wrap items-center gap-x-8 gap-y-4 border-t border-[rgb(var(--c-muted)/0.10)] pt-5"
                                >

                                    {{-- Status --}}
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="h-1.5 w-1.5 rounded-full bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.5)]"
                                        ></span>

                                        <span class="font-mono text-[10px] uppercase tracking-wider text-[rgb(var(--c-muted))]">
                                            AI Generated Brief
                                        </span>
                                    </div>

                                    {{-- Separator --}}
                                    <div class="hidden h-3 w-px bg-[#9A8F7E]/15 sm:block"></div>

                                    {{-- Period --}}
                                    <div class="flex items-center gap-2">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                            class="h-3.5 w-3.5 text-[rgb(var(--c-accent))]/70"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M6.75 3v2.25M17.25 3v2.25M3.75 9h16.5M5.25 5.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25A2.25 2.25 0 0 1 18.75 21H5.25A2.25 2.25 0 0 1 3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25Z"
                                            />
                                        </svg>

                                        <span class="font-mono text-[10px] uppercase tracking-wider text-[rgb(var(--c-muted))]">
                                            Ringkasan Mingguan
                                        </span>
                                    </div>

                                </div>

                            @else

                                {{-- EMPTY STATE --}}
                                <div
                                    class="mt-7 rounded-xl border border-dashed border-[rgb(var(--c-muted)/0.15)] bg-[#0F0D0B]/20 p-6"
                                >
                                    <div class="flex items-center gap-3">

                                        <div
                                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#9A8F7E]/5"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke-width="1.5"
                                                stroke="currentColor"
                                                class="h-4 w-4 text-[rgb(var(--c-muted))]"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M12 6v6l4 2"
                                                />
                                                <circle
                                                    cx="12"
                                                    cy="12"
                                                    r="9"
                                                />
                                            </svg>
                                        </div>

                                        <div>
                                            <p class="text-sm font-medium text-[#B9B0A4]">
                                                Ringkasan mingguan belum tersedia
                                            </p>

                                            <p class="mt-0.5 text-xs text-[rgb(var(--c-muted))]/70">
                                                Ringkasan akan muncul setelah proses generate selesai.
                                            </p>
                                        </div>

                                    </div>
                                </div>

                            @endif

                        </div>
                    </div>
                </div>


        {{-- SEARCH & FILTER KHUSUS MOBILE --}}
            <div class="mb-6 lg:hidden space-y-4">

                {{-- =================================================
                    SEARCH
                ================================================== --}}
                <div
                    class="rounded-xl border border-[rgb(var(--c-muted)/0.15)] bg-[rgb(var(--c-surface))] p-5"
                >

                    <h3 class="mb-3 font-display text-sm font-semibold text-[rgb(var(--c-tittle))]">
                        Cari Berita
                    </h3>

                    <form wire:submit="terapkanFilter">

                        <div class="relative">

                            <input
                                type="text"
                                wire:model="cariInput"
                                placeholder="Ketik kata kunci..."
                                class="w-full rounded-lg border border-[rgb(var(--c-muted)/0.30)] bg-transparent px-3 py-2.5 pr-10 text-sm text-[rgb(var(--c-tittle))] outline-none placeholder:text-[rgb(var(--c-muted))] focus:border-[#D4A853] focus:ring-1 focus:ring-[#D4A853]"
                            >

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.5"
                                class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[rgb(var(--c-muted))]"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="m21 21-4.35-4.35m0 0A7.5 7.5 0 1 0 6.044 6.044a7.5 7.5 0 0 0 10.606 10.606Z"
                                />
                            </svg>

                        </div>

                        <button
                            type="submit"
                            class="mt-3 w-full rounded-lg bg-[rgb(var(--c-accent))] py-2.5 text-sm font-semibold text-[rgb(var(--c-accent-contrast))] transition hover:bg-[rgb(var(--c-accent-hover))]"
                        >
                            Cari Berita
                        </button>

                    </form>

                </div>


                {{-- =================================================
                    FILTER
                ================================================== --}}
                <div
                    class="rounded-xl border border-[rgb(var(--c-muted)/0.15)] bg-[rgb(var(--c-surface))] p-5"
                >

                    <div class="mb-3 flex items-center justify-between">

                        <h3 class="font-display text-sm font-semibold text-[rgb(var(--c-tittle))]">
                            Filter
                        </h3>

                        @if ($opdId || $kategoriId)
                            <button
                                wire:click="resetFilter"
                                type="button"
                                class="text-xs text-[rgb(var(--c-accent))] hover:underline"
                            >
                                Reset
                            </button>
                        @endif

                    </div>


                    <div class="space-y-3">

                        {{-- KATEGORI --}}
                        <div>

                            <label class="mb-1.5 block text-xs text-[rgb(var(--c-muted))]">
                                Kategori
                            </label>

                            <select
                                wire:model.live="kategoriId"
                                class="w-full rounded-lg border border-[rgb(var(--c-muted)/0.30)] bg-[rgb(var(--c-surface))] px-3 py-2.5 text-sm text-[rgb(var(--c-tittle))]/80 outline-none focus:border-[#D4A853] focus:ring-1 focus:ring-[#D4A853]"
                            >

                                <option value="">
                                    Semua Kategori
                                </option>

                                @foreach ($kategoriList as $kategori)

                                    <option value="{{ $kategori->id }}">
                                        {{ $kategori->nama }}
                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- OPD --}}
                        <div>

                            <label class="mb-1.5 block text-xs text-[rgb(var(--c-muted))]">
                                OPD
                            </label>

                            <select
                                wire:model.live="opdId"
                                class="w-full rounded-lg border border-[rgb(var(--c-muted)/0.30)] bg-[rgb(var(--c-surface))] px-3 py-2.5 text-sm text-[rgb(var(--c-tittle))]/80 outline-none focus:border-[#D4A853] focus:ring-1 focus:ring-[#D4A853]"
                            >

                                <option value="">
                                    Semua OPD
                                </option>

                                @foreach ($opdList as $opd)

                                    <option value="{{ $opd->id }}">
                                        {{ $opd->nama }}
                                    </option>

                                @endforeach

                            </select>

                        </div>

                    </div>

                </div>

            </div>

        {{-- KONTEN 2 KOLOM --}}
    <div class="grid grid-cols-1 items-start gap-8 lg:grid-cols-3">

        {{-- =========================================================
            KIRI: LIST BERITA
        ========================================================== --}}
        <div class="min-w-0 lg:col-span-2" wire:loading.class="opacity-40">


            {{-- =====================================================
                HASIL BERITA
            ====================================================== --}}
            @if ($berita->isEmpty())

                <div class="rounded-xl border border-dashed border-[rgb(var(--c-muted)/0.30)] bg-[rgb(var(--c-surface))]/50 py-20 text-center">

                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full border border-[rgb(var(--c-muted)/0.20)] bg-[rgb(var(--c-surface))]">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.5"
                            class="h-6 w-6 text-[rgb(var(--c-muted))]"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="m21 21-4.35-4.35m0 0A7.5 7.5 0 1 0 6.044 6.044a7.5 7.5 0 0 0 10.606 10.606Z"
                            />
                        </svg>
                    </div>

                    <p class="mt-5 font-display text-lg text-[rgb(var(--c-tittle))]">
                        Tidak ada berita ditemukan
                    </p>

                    <p class="mx-auto mt-2 max-w-md text-sm text-[rgb(var(--c-muted))]">
                        @if ($cari)
                            Tidak ditemukan berita yang sesuai dengan pencarian
                            "<span class="text-[rgb(var(--c-accent))]">{{ $cari }}</span>".
                        @else
                            Tidak ada berita yang sesuai dengan filter yang dipilih.
                        @endif
                    </p>

                    @if ($cari || $opdId || $kategoriId)
                        <button
                            wire:click="resetFilter"
                            type="button"
                            class="mt-5 rounded-lg bg-[rgb(var(--c-accent))] px-4 py-2 text-sm font-semibold text-[rgb(var(--c-accent-contrast))] transition hover:bg-[rgb(var(--c-accent-hover))]"
                        >
                            Tampilkan Semua Berita
                        </button>
                    @endif

                </div>

            @else

                <div class="space-y-4">

                    @foreach ($berita as $item)

                        @php
                            $warna = $this->warnaKategori($item->kategori_id);
                        @endphp

                        <article
                            class="flex gap-3 rounded-xl border border-[rgb(var(--c-muted)/0.15)] bg-[rgb(var(--c-surface))] p-3 transition hover:border-[#D4A853]/40 sm:gap-4 sm:p-4"
                        >

                            @if ($item->thumbnail)
                                <img
                                    src="{{ $item->thumbnail }}"
                                    alt=""
                                    loading="lazy"
                                    class="h-24 w-24 flex-none rounded-lg object-cover sm:h-32 sm:w-40"
                                >
                            @endif

                            <div class="flex min-w-0 flex-1 flex-col">

                                {{-- META --}}
                                <div class="mb-2 flex flex-wrap items-center gap-2">

                                    <span
                                        class="rounded-full {{ $warna['bg'] }} {{ $warna['text'] }} px-2.5 py-0.5 text-xs font-medium"
                                    >
                                        {{ $item->kategori->nama ?? 'Umum' }}
                                    </span>

                                    <span class="font-mono text-[11px] text-[rgb(var(--c-muted))]">
                                        {{ $item->tanggal_publish->format('d M Y') }}
                                    </span>

                                </div>


                                {{-- JUDUL --}}
                                <h3
                                    class="mb-1 line-clamp-3 font-display text-sm font-semibold leading-snug text-[rgb(var(--c-tittle))] sm:line-clamp-2 sm:text-base"
                                >
                                    {{ $item->judul }}
                                </h3>


                                {{-- FOOTER --}}
                                <div
                                    class="mt-auto flex items-center justify-between gap-3 border-t border-[rgb(var(--c-muted)/0.10)] pt-2"
                                >

                                    <span
                                        class="min-w-0 truncate font-mono text-[11px] uppercase tracking-wide text-[rgb(var(--c-muted))]"
                                    >
                                        {{ $item->opd->nama ?? '-' }}
                                    </span>

                                    <a
                                        href="{{ route('berita.detail', $item) }}"
                                        wire:navigate 
                                        class="flex-none text-xs font-medium text-[rgb(var(--c-accent))] hover:underline"
                                    >
                                        Baca →
                                    </a>

                                </div>

                            </div>

                        </article>

                    @endforeach

                </div>


               {{-- PAGINATION --}}
                @if ($berita->hasPages())
                    <div class="mt-6 flex items-center justify-center sm:mt-8 sm:justify-end">

                        <div class="flex items-center gap-2">

                            {{-- PREVIOUS --}}
                            @if ($berita->onFirstPage())
                                <span
                                    class="flex h-9 w-9 items-center justify-center rounded-lg border border-[rgb(var(--c-accent)/0.1)] bg-[rgb(var(--c-surface))] text-[rgb(var(--c-accent)/0.25)]"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </span>
                            @else
                                <button
                                    type="button"
                                    wire:click="previousPage"
                                    wire:loading.attr="disabled"
                                    class="flex h-9 w-9 items-center justify-center rounded-lg border border-[rgb(var(--c-accent)/0.15)] bg-[rgb(var(--c-surface))] text-[rgb(var(--c-accent))] transition-all duration-200 hover:border-[rgb(var(--c-accent)/0.4)] hover:bg-[rgb(var(--c-accent))] hover:text-[rgb(var(--c-accent-contrast))]"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                            @endif

                            {{-- PAGE NUMBERS --}}
                            @foreach ($berita->getUrlRange(
                                max(1, $berita->currentPage() - 2),
                                min($berita->lastPage(), $berita->currentPage() + 2)
                            ) as $page => $url)

                                @if ($page == $berita->currentPage())
                                    {{-- ACTIVE --}}
                                    <span
                                        class="flex h-9 min-w-9 items-center justify-center rounded-lg bg-[rgb(var(--c-accent))] px-3 text-sm font-semibold text-[rgb(var(--c-accent-contrast))] shadow-[0_0_15px_rgba(212,168,83,0.12)]"
                                    >
                                        {{ $page }}
                                    </span>
                                @else
                                    {{-- NORMAL --}}
                                    <button
                                        type="button"
                                        wire:click="gotoPage({{ $page }})"
                                        wire:loading.attr="disabled"
                                        class="flex h-9 min-w-9 items-center justify-center rounded-lg border border-[rgb(var(--c-accent)/0.15)] bg-[rgb(var(--c-surface))] px-3 text-sm text-[rgb(var(--c-accent))] transition-all duration-200 hover:border-[rgb(var(--c-accent)/0.4)] hover:bg-[rgb(var(--c-accent))] hover:text-[rgb(var(--c-accent-contrast))]"
                                    >
                                        {{ $page }}
                                    </button>
                                @endif

                            @endforeach

                            {{-- NEXT --}}
                            @if ($berita->hasMorePages())
                                <button
                                    type="button"
                                    wire:click="nextPage"
                                    wire:loading.attr="disabled"
                                    class="flex h-9 w-9 items-center justify-center rounded-lg border border-[rgb(var(--c-accent)/0.15)] bg-[rgb(var(--c-surface))] text-[rgb(var(--c-accent))] transition-all duration-200 hover:border-[rgb(var(--c-accent)/0.4)] hover:bg-[rgb(var(--c-accent))] hover:text-[rgb(var(--c-accent-contrast))]"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            @else
                                <span
                                    class="flex h-9 w-9 items-center justify-center rounded-lg border border-[rgb(var(--c-accent)/0.1)] bg-[rgb(var(--c-surface))] text-[rgb(var(--c-accent)/0.25)]"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </span>
                            @endif

                        </div>
                    </div>
                @endif
            @endif

        </div>


        {{-- =========================================================
            KANAN: SIDEBAR STICKY
        ========================================================== --}}
        <aside class="hidden lg:block lg:sticky lg:top-24">

            <div class="space-y-4 space-y-6">


                {{-- =================================================
                    SEARCH
                ================================================== --}}
                <div
                    class="rounded-xl border border-[rgb(var(--c-muted)/0.15)] bg-[rgb(var(--c-surface))] p-5"
                >

                    <h3 class="mb-3 font-display text-sm font-semibold text-[rgb(var(--c-tittle))]">
                        Cari Berita
                    </h3>

                    <form wire:submit="terapkanFilter">

                        <div class="relative">

                            <input
                                type="text"
                                wire:model="cariInput"
                                placeholder="Ketik kata kunci..."
                                class="w-full rounded-lg border border-[rgb(var(--c-muted)/0.30)] bg-transparent px-3 py-2.5 pr-10 text-sm text-[rgb(var(--c-tittle))] outline-none placeholder:text-[rgb(var(--c-muted))] focus:border-[#D4A853] focus:ring-1 focus:ring-[#D4A853]"
                            >

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.5"
                                class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[rgb(var(--c-muted))]"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="m21 21-4.35-4.35m0 0A7.5 7.5 0 1 0 6.044 6.044a7.5 7.5 0 0 0 10.606 10.606Z"
                                />
                            </svg>

                        </div>


                        <button
                            type="submit"
                            class="mt-3 w-full rounded-lg bg-[rgb(var(--c-accent))] py-2.5 text-sm font-semibold text-[rgb(var(--c-accent-contrast))] transition hover:bg-[rgb(var(--c-accent-hover))]"
                        >
                            Cari Berita
                        </button>

                    </form>

                </div>


                {{-- =================================================
                    FILTER
                ================================================== --}}
                <div
                    class="rounded-xl border border-[rgb(var(--c-muted)/0.15)] bg-[rgb(var(--c-surface))] p-5"
                >

                    <div class="mb-3 flex items-center justify-between">

                        <h3 class="font-display text-sm font-semibold text-[rgb(var(--c-tittle))]">
                            Filter
                        </h3>

                        @if ($opdId || $kategoriId)
                            <button
                                wire:click="resetFilter"
                                type="button"
                                class="text-xs text-[rgb(var(--c-accent))] hover:underline"
                            >
                                Reset
                            </button>
                        @endif

                    </div>


                    <div class="space-y-3">

                        {{-- KATEGORI --}}
                        <div>

                            <label class="mb-1.5 block text-xs text-[rgb(var(--c-muted))]">
                                Kategori
                            </label>

                            <select
                                wire:model.live="kategoriId"
                                class="w-full rounded-lg border border-[rgb(var(--c-muted)/0.30)] bg-[rgb(var(--c-surface))] px-3 py-2.5 text-sm text-[rgb(var(--c-tittle))]/80 outline-none focus:border-[#D4A853] focus:ring-1 focus:ring-[#D4A853] "
                            >
                                <option value="">
                                    Semua Kategori
                                </option>

                                @foreach ($kategoriList as $kategori)

                                    <option value="{{ $kategori->id }}">
                                        {{ $kategori->nama }}
                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- OPD --}}
                        <div>

                            <label class="mb-1.5 block text-xs text-[rgb(var(--c-muted))]">
                                OPD
                            </label>

                            <select
                                wire:model.live="opdId"
                                class="w-full rounded-lg border border-[rgb(var(--c-muted)/0.30)] bg-[rgb(var(--c-surface))] px-3 py-2.5 text-sm text-[rgb(var(--c-tittle))]/80 outline-none focus:border-[#D4A853] focus:ring-1 focus:ring-[#D4A853]"
                            >
                                <option value="">
                                    Semua OPD
                                </option>

                                @foreach ($opdList as $opd)

                                    <option value="{{ $opd->id }}">
                                        {{ $opd->nama }}
                                    </option>

                                @endforeach

                            </select>

                        </div>

                    </div>

                </div>


        </aside>

    </div>