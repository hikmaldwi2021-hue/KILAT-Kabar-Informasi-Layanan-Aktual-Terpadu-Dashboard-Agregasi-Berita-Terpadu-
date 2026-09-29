<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BeritaController extends Controller
{
    /**
 * Mengambil daftar berita terbaru dengan filter.
 *
 * Endpoint ini digunakan untuk mengambil daftar berita yang
 * telah memiliki ringkasan AI dari seluruh OPD.
 *
 * Filter yang tersedia:
 * - opd_id: filter berdasarkan OPD
 * - kategori_id: filter berdasarkan kategori
 * - search: pencarian berdasarkan judul atau ringkasan
 * - per_page: jumlah data per halaman, maksimal 100
 *
 * Authentication:
 * Gunakan Bearer Token pada header Authorization.
 *
 * Contoh:
 * Authorization: Bearer {TOKEN}
 *
 * @queryParam opd_id integer ID OPD yang ingin difilter. Example: 19
 * @queryParam kategori_id integer ID kategori yang ingin difilter. Example: 4
 * @queryParam search string Kata kunci pencarian pada judul atau ringkasan. Example: ekspor
 * @queryParam per_page integer Jumlah berita per halaman (1-100). Example: 10
 */
public function index(Request $request): JsonResponse
    {
        $perPage = min(
            max((int) $request->input('per_page', 10), 1),
            100
        );

        $query = Berita::query()
            ->with([
                'opd:id,nama',
                'kategori:id,nama',
            ])
            ->whereNotNull('ringkasan');

        /*
        |--------------------------------------------------------------------------
        | Filter OPD
        |--------------------------------------------------------------------------
        */
        if ($request->filled('opd_id')) {
            $query->where('opd_id', $request->input('opd_id'));
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Kategori
        |--------------------------------------------------------------------------
        */
        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->input('kategori_id'));
        }

        /*
        |--------------------------------------------------------------------------
        | Pencarian
        |--------------------------------------------------------------------------
        */
        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('ringkasan', 'like', "%{$search}%");
            });
        }

        $berita = $query
            ->latest('tanggal_publish')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Data berita berhasil diambil.',
            'data' => $berita->getCollection()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'judul' => $item->judul,
                    'ringkasan' => $item->ringkasan,
                    'tanggal_publish' => $item->tanggal_publish?->format('Y-m-d'),
                    'thumbnail' => $item->thumbnail,
                    'link_asal' => $item->link_asal,

                    'opd' => $item->opd ? [
                        'id' => $item->opd->id,
                        'nama' => $item->opd->nama,
                    ] : null,

                    'kategori' => $item->kategori ? [
                        'id' => $item->kategori->id,
                        'nama' => $item->kategori->nama,
                    ] : null,
                ];
            }),

            'meta' => [
                'current_page' => $berita->currentPage(),
                'per_page' => $berita->perPage(),
                'total' => $berita->total(),
                'last_page' => $berita->lastPage(),
            ],
        ]);
    }

    /**
 * Mengambil detail satu berita.
 */
    public function show(int $id): JsonResponse
    {
        $berita = Berita::query()
            ->with([
                'opd:id,nama',
                'kategori:id,nama',
            ])
            ->whereNotNull('ringkasan')
            ->find($id);

        if (! $berita) {
            return response()->json([
                'success' => false,
                'message' => 'Berita tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail berita berhasil diambil.',
            'data' => [
                'id' => $berita->id,
                'judul' => $berita->judul,
                'isi' => $berita->isi,
                'ringkasan' => $berita->ringkasan,
                'tanggal_publish' => $berita->tanggal_publish?->format('Y-m-d'),
                'thumbnail' => $berita->thumbnail,
                'galeri' => $berita->galeri,
                'penulis' => $berita->penulis,
                'fotografer' => $berita->fotografer,
                'editor' => $berita->editor,
                'sumber' => $berita->sumber,
                'link_asal' => $berita->link_asal,

                'opd' => $berita->opd ? [
                    'id' => $berita->opd->id,
                    'nama' => $berita->opd->nama,
                ] : null,

                'kategori' => $berita->kategori ? [
                    'id' => $berita->kategori->id,
                    'nama' => $berita->kategori->nama,
                ] : null,
            ],
        ]);
    }
}