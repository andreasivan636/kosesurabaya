<?php

namespace App\Http\Controllers;

use App\Models\Kos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Controller AI Customer Service.
 * Berkomunikasi dengan Ollama (TinyLlama) lokal di localhost:11434.
 * Bisa diakses oleh Guest (tanpa login).
 * TIDAK menggunakan API WhatsApp.
 */
class AIChatController extends Controller
{
    /**
     * Proses pesan chat dari pengguna dan kirim ke Ollama.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'kos_id' => 'nullable|integer|exists:kos,id',
            'conversation_history' => 'nullable|array',
        ]);

        $userMessage = $request->input('message');
        $kosId = $request->input('kos_id');
        $history = $request->input('conversation_history', []);

        // Bangun konteks AI
        $systemPrompt = $this->buildSystemPrompt($kosId);

        // Siapkan messages untuk Ollama
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        // Tambah conversation history
        foreach ($history as $msg) {
            $messages[] = [
                'role' => $msg['role'] ?? 'user',
                'content' => $msg['content'] ?? '',
            ];
        }

        $messages[] = ['role' => 'user', 'content' => $userMessage];

        try {
            $response = Http::timeout(60)->post(
                config('services.ollama.base_url', 'http://localhost:11434') . '/api/chat',
                [
                    'model' => config('services.ollama.model', 'tinyllama'),
                    'messages' => $messages,
                    'stream' => false,
                    'options' => [
                        'temperature' => 0.7,
                        'top_p' => 0.9,
                        'num_predict' => 500,
                    ],
                ]
            );

            if ($response->successful()) {
                $aiResponse = $response->json('message.content', 'Maaf, saya tidak bisa menjawab saat ini.');

                return response()->json([
                    'success' => true,
                    'response' => $aiResponse,
                ]);
            }

            Log::error('Ollama API Error: ' . $response->body());

            return response()->json([
                'success' => false,
                'response' => 'Maaf, layanan AI sedang tidak tersedia. Silakan coba lagi nanti.',
            ], 503);

        } catch (\Exception $e) {
            Log::error('Ollama Connection Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'response' => 'Maaf, layanan AI sedang offline. Pastikan Ollama sudah berjalan di localhost:11434.',
            ], 503);
        }
    }

    /**
     * Bangun system prompt dengan konteks kos (jika ada).
     */
    private function buildSystemPrompt(?int $kosId = null): string
    {
        $base = <<<PROMPT
Kamu adalah CS AI dari KoSeSurabaya, platform pencarian kos mahasiswa di Surabaya.
Kamu membantu mahasiswa menemukan kos yang tepat di sekitar kampus UNTAG, ITS, Unair, dan Unesa.

Panduan menjawab:
- Jawab dalam Bahasa Indonesia yang ramah dan informatif
- Berikan rekomendasi berdasarkan kebutuhan mahasiswa (budget, lokasi kampus, fasilitas)
- Jelaskan fitur keamanan platform (verifikasi admin, pembayaran resmi)
- Jika ditanya hal di luar topik kos/hunian, arahkan kembali ke pencarian kos
- Jangan pernah memberikan informasi pribadi pemilik kos
- Sertakan tips keamanan jika relevan (cek langsung, jangan transfer ke rekening pribadi)

Informasi kampus:
- UNTAG Surabaya: Jl. Semolowaru No.45
- ITS: Kampus ITS Sukolilo
- Unair: Kampus C Mulyorejo & Kampus B Airlangga
- Unesa: Kampus Lidah Wetan & Kampus Ketintang
PROMPT;

        // Tambah konteks kos spesifik jika ada
        if ($kosId) {
            $kos = Kos::with('tipeKamar')->find($kosId);
            if ($kos) {
                $tipeInfo = $kos->tipeKamar->map(function ($t) {
                    $fasilitas = is_array($t->fasilitas) ? implode(', ', $t->fasilitas) : '-';
                    return "- Tipe {$t->tipe} ({$t->nama_tipe}): Rp " . number_format($t->harga_bulanan) . "/bulan, Tersedia: {$t->kamar_tersedia} kamar, Fasilitas: {$fasilitas}";
                })->implode("\n");

                $base .= <<<CONTEXT

\n\nKonteks kos yang sedang dilihat pengguna:
Nama: {$kos->nama_kos}
Alamat: {$kos->alamat}
Tipe: Kos {$kos->tipe_kos}
Rating: {$kos->rating_avg}/5
Kampus Terdekat: {$kos->kampus_terdekat}

Tipe Kamar:
{$tipeInfo}

Jawab pertanyaan tentang kos ini dengan detail yang ada di atas.
CONTEXT;
            }
        }

        return $base;
    }
}
