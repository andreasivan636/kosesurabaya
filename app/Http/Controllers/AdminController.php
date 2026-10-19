<?php

namespace App\Http\Controllers;

use App\Models\Kos;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Controller Admin Dashboard.
 * Dilindungi middleware: auth + role:admin
 */
class AdminController extends Controller
{
    /**
     * Dashboard admin dengan statistik keseluruhan.
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_owners' => User::where('role', 'owner')->count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'total_kos' => Kos::count(),
            'kos_pending' => Kos::where('status', 'pending')->count(),
            'kos_approved' => Kos::where('status', 'approved')->count(),
            'total_transaksi' => Transaksi::where('status', 'paid')->count(),
            'total_pendapatan' => Transaksi::where('status', 'paid')->sum('jumlah_bayar'),
        ];

        $kosPending = Kos::where('status', 'pending')
            ->with('owner:id,name,email,phone')
            ->latest()
            ->take(10)
            ->get();

        $transaksiTerbaru = Transaksi::with(['user:id,name', 'kos:id,nama_kos'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'kosPending', 'transaksiTerbaru'));
    }

    /**
     * Daftar kos menunggu verifikasi.
     */
    public function pendingKos()
    {
        $kosPending = Kos::where('status', 'pending')
            ->with(['owner:id,name,email,phone', 'tipeKamar'])
            ->latest()
            ->paginate(15);

        return view('admin.kos-pending', compact('kosPending'));
    }

    /**
     * Approve kos (mengubah status jadi approved).
     */
    public function approveKos(int $id)
    {
        $kos = Kos::findOrFail($id);
        $kos->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        // Invalidate cache agar peta menampilkan kos baru
        Cache::forget("kos_detail:{$id}");
        try {
            Cache::flush();
        } catch (\Throwable $e) {}

        return back()->with('success', "Kos \"{$kos->nama_kos}\" berhasil diverifikasi.");
    }

    /**
     * Reject kos dengan alasan.
     */
    public function rejectKos(Request $request, int $id)
    {
        $request->validate([
            'alasan_reject' => 'required|string|max:500',
        ]);

        $kos = Kos::findOrFail($id);
        $kos->update([
            'status' => 'rejected',
            'alasan_reject' => $request->input('alasan_reject'),
        ]);

        return back()->with('success', "Kos \"{$kos->nama_kos}\" ditolak.");
    }

    /**
     * Daftar semua transaksi.
     */
    public function transaksi(Request $request)
    {
        $query = Transaksi::with(['user:id,name,email', 'kos:id,nama_kos', 'tipeKamar:id,tipe,nama_tipe']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transaksi = $query->latest()->paginate(20);

        return view('admin.transaksi', compact('transaksi'));
    }

    /**
     * Manajemen pengguna.
     */
    public function users(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(20);

        return view('admin.users', compact('users'));
    }

    /**
     * Toggle aktif/nonaktif user.
     */
    public function toggleUser(int $id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);

        return back()->with('success', "Status user \"{$user->name}\" diperbarui.");
    }
}
