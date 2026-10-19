<?php $__env->startSection('title', 'Verifikasi Kos - Admin - KoSeSurabaya'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-white">Verifikasi Kos</h1>
            <p class="text-sm text-gray-400 mt-0.5"><?php echo e($kosPending->total()); ?> kos menunggu verifikasi</p>
        </div>
        <a href="<?php echo e(route('admin.dashboard')); ?>" class="text-sm text-gray-400 hover:text-white transition flex items-center gap-1">
            <i data-feather="arrow-left" class="w-4 h-4"></i> Dashboard
        </a>
    </div>

    <?php if($kosPending->isEmpty()): ?>
        <div class="glass rounded-2xl p-12 text-center">
            <span class="text-5xl">✅</span>
            <h3 class="text-lg font-semibold text-white mt-4">Semua Bersih!</h3>
            <p class="text-sm text-gray-400 mt-1">Tidak ada kos yang perlu diverifikasi saat ini.</p>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php $__currentLoopData = $kosPending; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kos): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="glass rounded-2xl p-6 hover:border-yellow-500/20 transition">
                    <div class="flex flex-col lg:flex-row gap-4">
                        <div class="flex-1">
                            <div class="flex items-start gap-3">
                                <div class="w-16 h-16 rounded-xl bg-white/5 flex-shrink-0 overflow-hidden">
                                    <?php if($kos->foto_utama): ?>
                                        <img src="<?php echo e(asset('storage/' . $kos->foto_utama)); ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center text-2xl">🏠</div>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-white"><?php echo e($kos->nama_kos); ?></h3>
                                    <p class="text-xs text-gray-400"><?php echo e($kos->alamat); ?></p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Pemilik: <span class="text-gray-300"><?php echo e($kos->owner->name); ?></span>
                                        (<?php echo e($kos->owner->email); ?>) •
                                        📞 <?php echo e($kos->owner->phone ?? '-'); ?> •
                                        Kos <?php echo e(ucfirst($kos->tipe_kos)); ?>

                                    </p>
                                </div>
                            </div>

                            
                            <div class="flex flex-wrap gap-2 mt-3">
                                <?php $__currentLoopData = $kos->tipeKamar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipe): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="px-2.5 py-1.5 rounded-lg bg-white/5 text-xs text-gray-300">
                                        Tipe <?php echo e($tipe->tipe); ?>: Rp <?php echo e(number_format($tipe->harga_bulanan, 0, ',', '.')); ?>/bln •
                                        <?php echo e($tipe->jumlah_kamar); ?> kamar •
                                        <?php echo e(implode(', ', $tipe->fasilitas ?? [])); ?>

                                    </span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>

                        <div class="flex lg:flex-col gap-2 flex-shrink-0">
                            <form method="POST" action="<?php echo e(route('admin.kos.approve', $kos->id)); ?>" class="flex-1 lg:flex-none">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="w-full px-5 py-2.5 rounded-xl bg-brand-500/20 text-brand-400 text-sm font-medium hover:bg-brand-500/30 transition border border-brand-500/30">
                                    ✓ Approve
                                </button>
                            </form>
                            <button onclick="showRejectModal(<?php echo e($kos->id); ?>, '<?php echo e(addslashes($kos->nama_kos)); ?>')"
                                    class="flex-1 lg:flex-none px-5 py-2.5 rounded-xl bg-red-500/20 text-red-400 text-sm font-medium hover:bg-red-500/30 transition border border-red-500/30">
                                ✕ Tolak
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="mt-6"><?php echo e($kosPending->links()); ?></div>
    <?php endif; ?>
</div>


<div id="reject-modal" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeRejectModal()"></div>
    <div class="absolute bottom-0 left-0 right-0 sm:bottom-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 sm:max-w-md sm:w-full glass rounded-t-3xl sm:rounded-3xl p-6 animate-slide-up">
        <h3 class="text-lg font-bold text-white mb-1">Tolak Kos</h3>
        <p class="text-sm text-gray-400 mb-4" id="reject-kos-name"></p>
        <form method="POST" id="reject-form">
            <?php echo csrf_field(); ?>
            <textarea name="alasan_reject" rows="3" required placeholder="Berikan alasan penolakan..."
                      class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-gray-200 focus:border-red-500 focus:outline-none transition resize-none mb-4"></textarea>
            <div class="flex gap-3">
                <button type="button" onclick="closeRejectModal()" class="flex-1 px-4 py-2.5 rounded-xl bg-white/5 text-gray-400 text-sm border border-white/10">Batal</button>
                <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-red-500/20 text-red-400 text-sm font-medium border border-red-500/30 hover:bg-red-500/30 transition">Tolak Kos</button>
            </div>
        </form>
    </div>
</div>

<script>
function showRejectModal(id, name) {
    document.getElementById('reject-form').action = `/admin/kos/${id}/reject`;
    document.getElementById('reject-kos-name').textContent = `Kos: ${name}`;
    document.getElementById('reject-modal').classList.remove('hidden');
}
function closeRejectModal() {
    document.getElementById('reject-modal').classList.add('hidden');
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\KoSesurabaya\resources\views/admin/kos-pending.blade.php ENDPATH**/ ?>