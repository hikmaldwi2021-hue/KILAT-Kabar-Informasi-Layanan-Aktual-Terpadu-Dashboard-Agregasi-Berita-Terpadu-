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

    public string $cariInput = '';

    #[Url]
    public string $cari = '';

    #[Url]
    public string $opdId = '';

    #[Url(as: 'kategori')]
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

    public function updatedOpdId()
    {
        $this->resetPage();
    }

    public function updatedKategoriId()
    {
        $this->resetPage();
    }

    public function terapkanFilter()
    {
        $this->cari = trim($this->cariInput);
        $this->resetPage();
    }

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
        $query = Berita::with(['opd', 'kategori'])
            ->whereNotNull('ringkasan')
            ->latest('tanggal_publish');

        if ($this->opdId) {
            $query->where('opd_id', $this->opdId);
        }

        if ($this->kategoriId) {
            $query->where('kategori_id', $this->kategoriId);
        }

        if ($this->cari) {
            $keyword = $this->cari;

            $query->where(function ($q) use ($keyword) {
                $q->where('judul', 'like', "%{$keyword}%")
                    ->orWhere('ringkasan', 'like', "%{$keyword}%");
            });
        }

        return [
            'berita' => $query->paginate(9),
            'opdList' => Opd::orderBy('nama')->get(),
            'kategoriList' => Kategori::orderBy('nama')->get(),
        ];
    }
};

?>

<div class="mt-10 grid grid-cols-1 items-start gap-8 lg:grid-cols-3">

    {{-- KIRI: LIST BERITA --}}
    <div class="min-w-0 lg:col-span-2" wire:loading.class="opacity-40">

        {{-- SEARCH DAN FILTER KHUSUS MOBILE --}}
        <div class="mb-6 space-y-4 lg:hidden">

            {{-- SEARCH MOBILE --}}
            <div class="rounded-xl border border-[rgb(var(--c-muted)/0.15)] bg-[rgb(var(--c-surface))] p-4">

                <h3 class="mb-3 font-display text-sm font-semibold text-[rgb(var(--c-tittle))]">
                    Cari Berita
                </h3>

                <form wire:submit="terapkanFilter">

                    <div class="relative">
                        <input
                            type="text"
                            wire:model="cariInput"
                            placeholder="Ketik kata kunci..."
                            class="w-full rounded-lg border border-[rgb(var(--c-muted)/0.30)] bg-transparent px-3 py-2.5 pr-10 text-sm text-[rgb(var(--c-tittle))] outline-none placeholder:text-[rgb(var(--c-muted))] focus:border-[rgb(var(--c-accent))] focus:ring-1 focus:ring-[rgb(var(--c-accent))]"
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


            {{-- FILTER MOBILE --}}
            <div class="rounded-xl border border-[rgb(var(--c-muted)/0.15)] bg-[rgb(var(--c-surface))] p-4">

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
                            class="w-full rounded-lg border border-[rgb(var(--c-muted)/0.30)] bg-[rgb(var(--c-surface))] px-3 py-2.5 text-sm text-[rgb(var(--c-tittle))] outline-none focus:border-[rgb(var(--c-accent))] focus:ring-1 focus:ring-[rgb(var(--c-accent))]"
                        >
                            <option value="">Semua Kategori</option>

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
                            class="w-full rounded-lg border border-[rgb(var(--c-muted)/0.30)] bg-[rgb(var(--c-surface))] px-3 py-2.5 text-sm text-[rgb(var(--c-tittle))] outline-none focus:border-[rgb(var(--c-accent))] focus:ring-1 focus:ring-[rgb(var(--c-accent))]"
                        >
                            <option value="">Semua OPD</option>

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


        {{-- HEADER --}}
        <div class="ml-5 flex items-center justify-between">

            <h1 class="font-display text-xl font-semibold text-[rgb(var(--c-tittle))]">
                Daftar Berita
            </h1>

            <span class="font-mono text-xs text-[rgb(var(--c-muted))]">
                Menampilkan total {{ number_format($berita->total()) }} berita
            </span>

        </div>


        @if ($berita->isEmpty())

            <div class="rounded-xl border border-dashed border-[rgb(var(--c-muted)/0.30)] bg-[rgb(var(--c-surface)/0.5)] py-20 text-center">

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

                    <article class="flex gap-4 rounded-xl border border-[rgb(var(--c-muted)/0.15)] bg-[rgb(var(--c-surface))] p-4 transition hover:border-[rgb(var(--c-accent)/0.4)]">

                        @if ($item->thumbnail)

                            <img
                                src="{{ $item->thumbnail }}"
                                alt=""
                                loading="lazy"
                                class="h-28 w-28 flex-none rounded-lg object-cover sm:h-32 sm:w-40"
                            >

                        @endif

                        <div class="flex min-w-0 flex-1 flex-col">

                            <div class="mb-2 flex flex-wrap items-center gap-2">

                                <span class="rounded-full {{ $warna['bg'] }} {{ $warna['text'] }} px-2.5 py-0.5 text-xs font-medium">
                                    {{ $item->kategori->nama ?? 'Umum' }}
                                </span>

                                <span class="font-mono text-[11px] text-[rgb(var(--c-muted))]">
                                    {{ $item->tanggal_publish->format('d M Y') }}
                                </span>

                            </div>

                            <h3 class="mb-1 font-display text-base font-semibold leading-snug text-[rgb(var(--c-tittle))] line-clamp-2">
                                {{ $item->judul }}
                            </h3>

                            <p class="mb-2 text-sm leading-relaxed text-[rgb(var(--c-muted))] line-clamp-2">
                                {{ $item->ringkasan }}
                            </p>

                            <div class="mt-auto flex min-w-0 items-center justify-between gap-2 border-t border-[rgb(var(--c-muted)/0.10)] pt-2">
                                <span class="min-w-0 flex-1 truncate font-mono text-[10px] uppercase tracking-wide text-[rgb(var(--c-muted))] sm:text-[11px]">
                                    {{ $item->opd->nama ?? '-' }}
                                </span>

                                <a
                                    href="{{ route('berita.detail', $item) }}"
                                    wire:navigate
                                    class="flex-none whitespace-nowrap text-xs font-semibold text-[rgb(var(--c-accent))] hover:underline sm:text-sm"
                                >
                                    Baca →
                                </a>

                            </div>

                        </div>

                    </article>

                @endforeach

            </div>


            @if ($berita->hasPages())

                <div class="mt-8 flex items-center justify-center">

                    <div class="flex items-center gap-2">

                        @if ($berita->onFirstPage())

                            <span class="flex h-9 w-9 items-center justify-center rounded-lg border border-[rgb(var(--c-accent)/0.1)] bg-[rgb(var(--c-surface))] text-[rgb(var(--c-accent)/0.25)]">

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M15 19l-7-7 7-7"
                                    />
                                </svg>

                            </span>

                        @else

                            <button
                                type="button"
                                wire:click="previousPage"
                                wire:loading.attr="disabled"
                                class="flex h-9 w-9 items-center justify-center rounded-lg border border-[rgb(var(--c-accent)/0.15)] bg-[rgb(var(--c-surface))] text-[rgb(var(--c-accent))] transition-all duration-200 hover:border-[rgb(var(--c-accent)/0.4)] hover:bg-[rgb(var(--c-accent))] hover:text-[rgb(var(--c-accent-contrast))]"
                            >

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M15 19l-7-7 7-7"
                                    />
                                </svg>

                            </button>

                        @endif


                        @foreach ($berita->getUrlRange(max(1, $berita->currentPage() - 2), min($berita->lastPage(), $berita->currentPage() + 2)) as $page => $url)

                            @if ($page == $berita->currentPage())

                                <span class="flex h-9 min-w-9 items-center justify-center rounded-lg bg-[rgb(var(--c-accent))] px-3 text-sm font-semibold text-[rgb(var(--c-accent-contrast))]">
                                    {{ $page }}
                                </span>

                            @else

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


                        @if ($berita->hasMorePages())

                            <button
                                type="button"
                                wire:click="nextPage"
                                wire:loading.attr="disabled"
                                class="flex h-9 w-9 items-center justify-center rounded-lg border border-[rgb(var(--c-accent)/0.15)] bg-[rgb(var(--c-surface))] text-[rgb(var(--c-accent))] transition-all duration-200 hover:border-[rgb(var(--c-accent)/0.4)] hover:bg-[rgb(var(--c-accent))] hover:text-[rgb(var(--c-accent-contrast))]"
                            >

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M9 5l7 7-7 7"
                                    />
                                </svg>

                            </button>

                        @else

                            <span class="flex h-9 w-9 items-center justify-center rounded-lg border border-[rgb(var(--c-accent)/0.1)] bg-[rgb(var(--c-surface))] text-[rgb(var(--c-accent)/0.25)]">

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M9 5l7 7-7 7"
                                    />
                                </svg>

                            </span>

                        @endif

                    </div>

                </div>

            @endif

        @endif

    </div>


    {{-- KANAN: SIDEBAR --}}
    <aside class="hidden lg:sticky lg:top-24 lg:block">

        <div class="space-y-6">

            {{-- SEARCH --}}
            <div class="rounded-xl border border-[rgb(var(--c-muted)/0.15)] bg-[rgb(var(--c-surface))] p-5">

                <h3 class="mb-3 font-display text-sm font-semibold text-[rgb(var(--c-tittle))]">
                    Cari Berita
                </h3>

                <form wire:submit="terapkanFilter">

                    <div class="relative">

                        <input
                            type="text"
                            wire:model="cariInput"
                            placeholder="Ketik kata kunci..."
                            class="w-full rounded-lg border border-[rgb(var(--c-muted)/0.30)] bg-transparent px-3 py-2.5 pr-10 text-sm text-[rgb(var(--c-tittle))] outline-none placeholder:text-[rgb(var(--c-muted))] focus:border-[rgb(var(--c-accent))] focus:ring-1 focus:ring-[rgb(var(--c-accent))]"
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


            {{-- FILTER --}}
            <div class="rounded-xl border border-[rgb(var(--c-muted)/0.15)] bg-[rgb(var(--c-surface))] p-5">

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

                    <div>

                        <label class="mb-1.5 block text-xs text-[rgb(var(--c-muted))]">
                            Kategori
                        </label>

                        <select
                            wire:model.live="kategoriId"
                            class="w-full rounded-lg border border-[rgb(var(--c-muted)/0.30)] bg-[rgb(var(--c-surface))] px-3 py-2.5 text-sm text-[rgb(var(--c-tittle))] outline-none focus:border-[rgb(var(--c-accent))] focus:ring-1 focus:ring-[rgb(var(--c-accent))]"
                        >
                            <option value="">Semua Kategori</option>

                            @foreach ($kategoriList as $kategori)
                                <option value="{{ $kategori->id }}">
                                    {{ $kategori->nama }}
                                </option>
                            @endforeach

                        </select>

                    </div>


                    <div>

                        <label class="mb-1.5 block text-xs text-[rgb(var(--c-muted))]">
                            OPD
                        </label>

                        <select
                            wire:model.live="opdId"
                            class="w-full rounded-lg border border-[rgb(var(--c-muted)/0.30)] bg-[rgb(var(--c-surface))] px-3 py-2.5 text-sm text-[rgb(var(--c-tittle))] outline-none focus:border-[rgb(var(--c-accent))] focus:ring-1 focus:ring-[rgb(var(--c-accent))]"
                        >
                            <option value="">Semua OPD</option>

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

    </aside>

</div>