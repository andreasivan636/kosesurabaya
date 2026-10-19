<?php $__env->startSection('title', 'Status Pembayaran - KoSeSurabaya'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-[70vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        <div class="glass rounded-3xl p-8 shadow-2xl text-center animate-slide-up">

            <?php if($transaksi->status === 'paid'): ?>
                
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-brand-500/20 flex items-center justify-center">
                    <span class="text-5xl">✅</span>
                </div>
                <h1 class="text-2xl font-bold text-white mb-2">Pembayaran Berhasil!</h1>
                <p class="text-sm text-gray-400 mb-6">Booking kos Anda telah dikonfirmasi.</p>

            <?php elseif(in_array($transaksi->status, ['pending', 'waiting_payment'])): ?>
                
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-yellow-500/20 flex items-center justify-center">
                    <span class="text-5xl animate-pulse">⏳</span>
                </div>
                <h1 class="text-2xl font-bold text-white mb-2">Menunggu Pembayaran</h1>
                <p class="text-sm text-gray-400 mb-6">Silakan selesaikan pembayaran Anda.</p>

                <?php if($transaksi->snap_token): ?>
                    <button onclick="window.snap.pay('<?php echo e($transaksi->snap_token); ?>')"
                            class="w-full bg-gradient-to-r from-brand-500 to-brand-600 text-white py-3 rounded-xl font-semibold text-sm hover:shadow-lg hover:shadow-brand-500/25 transition-all active:scale-95 mb-4">
                        💳 Bayar Sekarang
                    </button>
                <?php endif; ?>

            <?php elseif($transaksi->status === 'expired'): ?>
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-500/20 flex items-center justify-center">
                    <span class="text-5xl">⌛</span>
                </div>
                <h1 class="text-2xl font-bold text-white mb-2">Pembayaran Kedaluwarsa</h1>
                <p class="text-sm text-gray-400 mb-6">Batas waktu pembayaran telah habis.</p>

            <?php else: ?>
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-red-500/20 flex items-center justify-center">
                    <span class="text-5xl">❌</span>
                </div>
                <h1 class="text-2xl font-bold text-white mb-2">Pembayaran Dibatalkan</h1>
                <p class="text-sm text-gray-400 mb-6">Transaksi ini telah dibatalkan.</p>
            <?php endif; ?>

            
            <div class="glass rounded-2xl p-5 text-left space-y-3 mb-6">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">Order ID</span>
                    <span class="text-gray-200 font-mono text-xs"><?php echo e($transaksi->order_id); ?></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">Kos</span>
                    <span class="text-white font-medium"><?php echo e($transaksi->kos->nama_kos ?? '-'); ?></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">Tipe Kamar</span>
                    <span class="text-gray-200"><?php echo e($transaksi->tipeKamar->nama_tipe ?? '-'); ?></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">Durasi</span>
                    <span class="text-gray-200"><?php echo e(ucfirst($transaksi->durasi)); ?></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">Periode</span>
                    <span class="text-gray-200"><?php echo e($transaksi->tanggal_mulai->format('d/m/Y')); ?> — <?php echo e($transaksi->tanggal_selesai->format('d/m/Y')); ?></span>
                </div>
                <div class="border-t border-white/5 pt-3 flex justify-between">
                    <span class="text-gray-400 font-medium">Total</span>
                    <span class="text-brand-400 font-bold text-lg">Rp <?php echo e(number_format($transaksi->jumlah_bayar, 0, ',', '.')); ?></span>
                </div>
                <?php if($transaksi->payment_type): ?>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Metode</span>
                        <span class="text-gray-200"><?php echo e(ucfirst(str_replace('_', ' ', $transaksi->payment_type))); ?></span>
                    </div>
                <?php endif; ?>
                <?php if($transaksi->paid_at): ?>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Dibayar</span>
                        <span class="text-brand-400"><?php echo e($transaksi->paid_at->format('d/m/Y H:i')); ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <?php if($transaksi->status === 'paid'): ?>
                <div class="bg-brand-500/10 border border-brand-500/20 rounded-xl p-4 mb-4">
                    <p class="text-xs text-brand-300">
                        🎉 <strong>E-Tiket Booking</strong> — Tunjukkan halaman ini kepada pemilik kos sebagai bukti booking resmi Anda.
                    </p>
                </div>
            <?php endif; ?>

            <a href="<?php echo e(route('home')); ?>"
               class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-white transition">
                <i data-feather="arrow-left" class="w-4 h-4"></i>
                Kembali ke Peta
            </a>
        </div>
    </div>
</div>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="<?php echo e(config('services.midtrans.client_key')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\KoSesurabaya\resources\views/transaksi/status.blade.php ENDPATH**/ ?>