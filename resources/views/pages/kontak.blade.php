<?php

use Livewire\Component;

new class extends Component
{
    public function with(): array
    {
        return [];
    }
};

?>

<div class="-mx-4 -mt-10 space-y-10 sm:-mx-6 lg:-mx-8">

    {{-- HERO --}}
<div class="relative overflow-hidden bg-[rgb(var(--c-surface))] py-20 sm:py-28">
    <style>
        @keyframes kontak-fence-move {
            0% { background-position: 0 0; }
            100% { background-position: 56px 56px; }
        }

        .kontak-fence-bg {
            background-image:
                linear-gradient(rgb(var(--c-accent) / 1) 1px, transparent 1px),
                linear-gradient(90deg, rgb(var(--c-accent) / 1) 1px, transparent 1px);
            background-size: 56px 56px;
            animation: kontak-fence-move 6s linear infinite;
        }
    </style>

    <div class="kontak-fence-bg pointer-events-none absolute inset-0 opacity-[0.15]"></div>

    <div class="relative mx-auto max-w-3xl px-4 text-center sm:px-6 lg:px-8">
        <span class="font-mono text-xs uppercase tracking-widest text-[rgb(var(--c-accent))]">
            Kontak Kami
        </span>

        <h1 class="mt-3 font-display text-3xl font-semibold leading-tight text-[rgb(var(--c-tittle))] sm:text-4xl lg:text-5xl">
            Ada pertanyaan? Hubungi kami
        </h1>

        <p class="mt-4 text-sm leading-relaxed text-[rgb(var(--c-muted))] sm:text-base">
            Kami siap membantu menjawab pertanyaan seputar layanan informasi dan aktivitas OPD di lingkungan Pemerintah Provinsi Kepulauan Bangka Belitung.
        </p>
    </div>
</div>

    <div class="mx-auto max-w-6xl space-y-10 px-4 sm:px-6 lg:px-8">

        {{-- MAPS --}}
        <div class="overflow-hidden rounded-2xl border border-[rgb(var(--c-muted)/0.15)]">
            <iframe
                src="https://www.google.com/maps?q=-2.1631861,106.1642731&z=17&output=embed"
                width="100%"
                height="380"
                style="border:0;"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                class="grayscale-[0.3] contrast-[1.05]"
            ></iframe>
        </div>

        {{-- 4 BOX INFO --}}
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">

            <div class="rounded-xl border border-[#D4A853]/70 bg-[rgb(var(--c-surface))] p-5">
            <div class="mb-3 flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-[#D4A853]/12">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.5"
                        class="h-5 w-5 text-[#D4A853]">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                    </svg>
                </div>

                <h3 class="font-display text-sm font-semibold text-[#D4A853]">
                    Alamat Kantor
                </h3>
            </div>

            <p class="text-xs leading-relaxed text-[rgb(var(--c-muted))]">
                Dinas Komunikasi dan Informatika Provinsi Kepulauan Bangka Belitung, Pangkalpinang
            </p>
        </div>

            <div class="rounded-xl border border-[#D4A853]/70 bg-[rgb(var(--c-surface))] p-5">
            <div class="mb-3 flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-[#D4A853]/12">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.5"
                        class="h-5 w-5 text-[#D4A853]">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.362-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                    </svg>
                </div>

                <h3 class="font-display text-sm font-semibold text-[#D4A853]">
                    Telepon & Fax
                </h3>
            </div>

            <p class="text-xs leading-relaxed text-[rgb(var(--c-muted))]">
                Telp. +62 (0717) 4262141<br>
                Fax. +62 (0717) 4262141
            </p>
        </div>

            <div class="rounded-xl border border-[#D4A853]/70 bg-[rgb(var(--c-surface))] p-5">
            <div class="mb-3 flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-[#D4A853]/12">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.5"
                        class="h-5 w-5 text-[#D4A853]">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                    </svg>
                </div>

                <h3 class="font-display text-sm font-semibold text-[#D4A853]">
                    Email
                </h3>
            </div>

            <a href="mailto:kominfo@babelprov.go.id"
                class="text-xs leading-relaxed text-[rgb(var(--c-muted))] transition hover:text-[#D4A853]">
                kominfo@babelprov.go.id
            </a>
        </div>

            <div class="rounded-xl border border-[#D4A853]/70 bg-[rgb(var(--c-surface))] p-5">
            <div class="mb-3 flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-[#D4A853]/12">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.5"
                        class="h-5 w-5 text-[#D4A853]">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z" />
                    </svg>
                </div>

                <h3 class="font-display text-sm font-semibold text-[#D4A853]">
                    Media Sosial
                </h3>
            </div>

            <div class="flex items-center gap-3">
                <a href="https://www.instagram.com/diskominfo_babel/"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="flex h-8 w-8 items-center justify-center rounded-full border border-[rgb(var(--c-muted)/0.3)] text-[rgb(var(--c-muted))] transition hover:border-[#D4A853] hover:text-[#D4A853]"
                    aria-label="Instagram">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.5"
                        class="h-4 w-4">
                        <rect x="3" y="3" width="18" height="18" rx="5" />
                        <circle cx="12" cy="12" r="4" />
                        <circle cx="17.2" cy="6.8" r="0.8"
                            fill="currentColor"
                            stroke="none" />
                    </svg>
                </a>

                <a href="https://www.youtube.com/channel/UCh6HstgGZcDf7J_oVd2A_EQ"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="flex h-8 w-8 items-center justify-center rounded-full border border-[rgb(var(--c-muted)/0.3)] text-[rgb(var(--c-muted))] transition hover:border-[#D4A853] hover:text-[#D4A853]"
                    aria-label="YouTube">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.5"
                        class="h-4 w-4">
                        <rect x="2.5" y="6" width="19" height="12" rx="4" />
                        <path d="M10.5 9.5v5l4.5-2.5-4.5-2.5Z"
                            fill="currentColor"
                            stroke="none" />
                    </svg>
                </a>
            </div>
        </div>

                </div>

        {{-- JAM & HARI KERJA --}}
        <div class="rounded-xl border border-[#D4A853]/70 bg-[rgb(var(--c-surface))] p-5 sm:p-6">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

                {{-- Judul --}}
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-[#D4A853]/12">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.5"
                            class="h-5 w-5 text-[#D4A853]">
                            <circle cx="12" cy="12" r="8.5" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 7v5l3.25 2" />
                        </svg>
                    </div>

                    <div>
                        <h3 class="font-display text-sm font-semibold text-[#D4A853]">
                            Jam & Hari Kerja
                        </h3>
                        <p class="mt-0.5 text-xs text-[rgb(var(--c-muted))]">
                            Waktu pelayanan kantor
                        </p>
                    </div>
                </div>

                {{-- Jadwal --}}
                <div class="grid grid-cols-1 sm:grid-cols-3">

                    {{-- Senin - Kamis --}}
                    <div class="border-b border-[#D4A853]/50 pb-3 sm:border-b-0 sm:border-r sm:px-6 sm:py-1">
                        <div class="flex items-center justify-between gap-4 sm:block">
                            <span class="text-sm text-[rgb(var(--c-tittle))]">
                                Senin – Kamis
                            </span>

                            <span class="text-xs text-[rgb(var(--c-muted))] sm:mt-1 sm:block">
                                07.30 – 16.00 WIB
                            </span>
                        </div>
                    </div>

                    {{-- Jumat --}}
                    <div class="border-b border-[#D4A853]/50 py-3 sm:border-b-0 sm:border-r sm:px-6 sm:py-1">
                        <div class="flex items-center justify-between gap-4 sm:block">
                            <span class="text-sm text-[rgb(var(--c-tittle))]">
                                Jumat
                            </span>

                            <span class="text-xs text-[rgb(var(--c-muted))] sm:mt-1 sm:block">
                                07.30 – 16.30 WIB
                            </span>
                        </div>
                    </div>

                    {{-- Sabtu - Minggu --}}
                    <div class="pt-3 sm:px-6 sm:py-1">
                        <div class="flex items-center justify-between gap-4 sm:block">
                            <span class="text-sm text-[rgb(var(--c-tittle))]">
                                Sabtu – Minggu
                            </span>

                            <span class="text-xs text-[#D4A853] sm:mt-1 sm:block">
                                Tutup
                            </span>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>

</div>