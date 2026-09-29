<nav
    x-data="{ mobileOpen: false }"
    class="sticky top-0 z-50 w-full bg-[rgb(var(--c-surface))] transition-colors duration-300"
>
    <div class="mx-auto flex h-16 w-full max-w-[1400px] items-center px-5 sm:px-8 lg:px-10">

    {{-- Logo + wordmark --}}
    <div class="shrink-0">
        <a href="{{ route('beranda') }}" class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-[rgb(var(--c-accent))]">
                <svg viewBox="0 0 320 512" class="h-5 w-5 fill-[rgb(var(--c-accent-contrast))]">
                    <path d="M296 160H180.6l42.6-129.8C227.2 15 215.7 0 200 0H72C60 0 49.7 8.5 47.5 20.3l-32 176C13 218.5 25 232 40 232h116.6l-45.4 220a15.99 15.99 0 0 0 28.9 12.7l160-224c14.5-20.4-.3-46.7-25.1-46.7z"/>
                </svg>
            </span>

            <div class="leading-tight">
                <div class="font-display text-base font-semibold tracking-wide text-[rgb(var(--c-accent))]">
                    KILAT
                </div>

                <div class="hidden font-mono text-[10px] uppercase tracking-wide text-[rgb(var(--c-muted))] sm:block">
                    Kabar Informasi Layanan Aktual Terpadu
                </div>
            </div>
        </a>
    </div>


    {{-- Spacer --}}
    <div class="flex-1"></div>


    {{-- Menu (desktop) --}}
    <div class="hidden items-center gap-8 md:flex">

        @php
            $menu = [
                ['label' => 'Beranda', 'route' => 'beranda'],
                ['label' => 'Berita', 'route' => 'berita'],
                ['label' => 'Kontak', 'route' => 'kontak'],
            ];
        @endphp

        @foreach ($menu as $item)

            @php
                $isActive =
                    \Illuminate\Support\Facades\Route::has($item['route'])
                    && request()->routeIs($item['route']);
            @endphp

            <a href="{{ \Illuminate\Support\Facades\Route::has($item['route']) ? route($item['route']) : '#' }}"
                class="group relative py-2 text-sm font-medium transition-colors
                    {{ $isActive
                        ? 'text-[rgb(var(--c-accent))]'
                        : 'text-[rgb(var(--c-muted))] hover:text-[rgb(var(--c-accent))]' }}"
            >
                {{ $item['label'] }}

                <span
                    class="absolute inset-x-0 -bottom-0.5 h-0.5 origin-left bg-[rgb(var(--c-accent))] transition-transform duration-300
                        {{ $isActive
                            ? 'scale-x-100'
                            : 'scale-x-0 group-hover:scale-x-100' }}"
                ></span>
            </a>

        @endforeach

    </div>


    {{-- Jarak antara menu dan kontrol kanan --}}
    <div class="hidden w-10 lg:block"></div>


    {{-- Divider + toggle tema + tanggal --}}
    <div
        x-data="{
            theme: localStorage.getItem('kilat-theme') || 'dark',
            setTheme(t) {
                this.theme = t;
                localStorage.setItem('kilat-theme', t);
                document.documentElement.classList.toggle('light', t === 'light');
            }
        }"
        class="flex shrink-0 items-center gap-4"
    >

        {{-- Divider --}}
        <span class="hidden h-6 w-px bg-[rgb(var(--c-muted)/0.4)] sm:block"></span>


        {{-- Theme Toggle --}}
        <div class="flex items-center gap-1 rounded-full border border-[rgb(var(--c-muted)/0.4)] p-1">

            {{-- Mode terang --}}
            <button
                type="button"
                @click="setTheme('light')"
                aria-label="Mode terang"
                :class="theme === 'light' ? 'bg-[rgb(var(--c-accent)/0.15)] text-[rgb(var(--c-accent))]' : 'text-[rgb(var(--c-muted))] opacity-50'"
                class="flex h-6 w-6 items-center justify-center rounded-full transition hover:text-[rgb(var(--c-accent))]"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.5"
                    class="h-3.5 w-3.5"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"
                    />
                </svg>
            </button>


            {{-- Mode gelap --}}
            <button
                type="button"
                @click="setTheme('dark')"
                aria-label="Mode gelap"
                :class="theme === 'dark' ? 'bg-[rgb(var(--c-accent)/0.15)] text-[rgb(var(--c-accent))]' : 'text-[rgb(var(--c-muted))] opacity-50'"
                class="flex h-6 w-6 items-center justify-center rounded-full transition"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.5"
                    class="h-3.5 w-3.5"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z"
                    />
                </svg>
            </button>

        </div>


        {{-- Tanggal --}}
        <div class="hidden rounded-md border border-[rgb(var(--c-muted)/0.4)] px-3 py-1.5 font-mono text-xs text-[rgb(var(--c-muted))] sm:block">
            {{
            [
                'Sunday' => 'Minggu',
                'Monday' => 'Senin',
                'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu',
                'Thursday' => 'Kamis',
                'Friday' => 'Jumat',
                'Saturday' => 'Sabtu',
            ][now()->format('l')]
        }},
        {{
            [
                'January' => 'Januari',
                'February' => 'Februari',
                'March' => 'Maret',
                'April' => 'April',
                'May' => 'Mei',
                'June' => 'Juni',
                'July' => 'Juli',
                'August' => 'Agustus',
                'September' => 'September',
                'October' => 'Oktober',
                'November' => 'November',
                'December' => 'Desember',
            ][now()->format('F')]
        }}
        {{ now()->format('d Y') }}
        </div>

    </div>


    {{-- Hamburger (mobile) --}}
    <button
        type="button"
        @click="mobileOpen = !mobileOpen"
        class="ml-4 flex h-9 w-9 items-center justify-center rounded-lg border border-[rgb(var(--c-muted)/0.4)] text-[rgb(var(--c-muted))] transition hover:text-[rgb(var(--c-accent))] md:hidden"
        :aria-expanded="mobileOpen"
        aria-label="Buka menu"
    >
        <svg x-show="!mobileOpen" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
        </svg>
        <svg x-show="mobileOpen" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" style="display: none;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
        </svg>
    </button>

</div>

{{-- Panel menu mobile --}}
<div
    x-show="mobileOpen"
    x-collapse
    @click.outside="mobileOpen = false"
    class="border-t border-[rgb(var(--c-muted)/0.15)] bg-[rgb(var(--c-surface))] md:hidden"
    style="display: none;"
>
    <div class="flex flex-col gap-1 px-5 py-4 sm:px-8">
        @foreach ($menu as $item)
            @php
                $isActive =
                    \Illuminate\Support\Facades\Route::has($item['route'])
                    && request()->routeIs($item['route']);
            @endphp

            
                <a href="{{ \Illuminate\Support\Facades\Route::has($item['route']) ? route($item['route']) : '#' }}"
                @click="mobileOpen = false"
                class="rounded-lg px-3 py-2.5 text-sm font-medium transition-colors
                    {{ $isActive
                        ? 'bg-[rgb(var(--c-accent)/0.12)] text-[rgb(var(--c-accent))]'
                        : 'text-[rgb(var(--c-muted))] hover:bg-[rgb(var(--c-accent)/0.08)] hover:text-[rgb(var(--c-accent))]' }}"
            >
                {{ $item['label'] }}
            </a>
        @endforeach

        <div class="mt-2 flex items-center justify-between border-t border-[rgb(var(--c-muted)/0.15)] px-3 pt-3 font-mono text-xs text-[rgb(var(--c-muted))] sm:hidden">
            {{
            [
                'Sunday' => 'Minggu',
                'Monday' => 'Senin',
                'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu',
                'Thursday' => 'Kamis',
                'Friday' => 'Jumat',
                'Saturday' => 'Sabtu',
            ][now()->format('l')]
        }},
        {{
            [
                'January' => 'Januari',
                'February' => 'Februari',
                'March' => 'Maret',
                'April' => 'April',
                'May' => 'Mei',
                'June' => 'Juni',
                'July' => 'Juli',
                'August' => 'Agustus',
                'September' => 'September',
                'October' => 'Oktober',
                'November' => 'November',
                'December' => 'Desember',
            ][now()->format('F')]
        }}
        {{ now()->format('d Y') }}

        </div>
    </div>
</div>

</nav>