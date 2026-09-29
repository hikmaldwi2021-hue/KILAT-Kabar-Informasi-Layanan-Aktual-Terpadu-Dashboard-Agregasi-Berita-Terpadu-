<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RingkasanPeriodik;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RingkasanController extends Controller
{
    /**
     * Mengambil ringkasan periodik terbaru.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min(
            max((int) $request->input('per_page', 5), 1),
            20
        );

        $ringkasan = RingkasanPeriodik::query()
            ->latest('periode_akhir')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Data ringkasan periodik berhasil diambil.',

            'data' => $ringkasan->getCollection()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'tipe' => $item->tipe,
                    'periode_mulai' => $item->periode_mulai?->format('Y-m-d'),
                    'periode_akhir' => $item->periode_akhir?->format('Y-m-d'),
                    'narasi' => $item->narasi,
                    'data_agregat' => $item->data_agregat,
                ];
            }),

            'meta' => [
                'current_page' => $ringkasan->currentPage(),
                'per_page' => $ringkasan->perPage(),
                'total' => $ringkasan->total(),
                'last_page' => $ringkasan->lastPage(),
            ],
        ]);
    }
}