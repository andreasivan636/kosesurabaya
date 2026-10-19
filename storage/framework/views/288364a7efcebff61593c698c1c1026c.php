<?php $__env->startSection('title', 'Cari Kos - KoSeSurabaya'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-6xl mx-auto px-4 py-8">
    
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-white mb-1">Cari Kos di Surabaya</h1>
        <p class="text-sm text-gray-400">Temukan kos terbaik dekat kampus Anda</p>
    </div>

    
    <form method="GET" action="<?php echo e(route('kos.search')); ?>" class="glass rounded-2xl p-5 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="lg:col-span-2">
                <input type="text" name="q" value="<?php echo e(request('q')); ?>" placeholder="Cari nama kos, alamat..."
                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-gray-200 focus:border-brand-500 focus:outline-none transition">
            </div>
            <select name="kampus" class="bg-white/5 border border-white/10 rounded-xl px-3 py-2.5 text-sm text-gray-200 focus:border-brand-500 focus:outline-none">
                <option value="">Semua Kampus</option>
                <option value="UNTAG" <?php echo e(request('kampus') === 'UNTAG' ? 'selected' : ''); ?>>UNTAG Surabaya</option>
                <option value="ITS" <?php echo e(request('kampus') === 'ITS' ? 'selected' : ''); ?>>ITS</option>
                <option value="UNAIR" <?php echo e(request('kampus') === 'UNAIR' ? 'selected' : ''); ?>>Universitas Airlangga</option>
                <option value="UNESA" <?php echo e(request('kampus') === 'UNESA' ? 'selected' : ''); ?>>UNESA</option>
            </select>
            <select name="tipe_kos" class="bg-white/5 border border-white/10 rounded-xl px-3 py-2.5 text-sm text-gray-200 focus:border-brand-500 focus:outline-none">
                <option value="">Semua Tipe</option>
                <option value="putra" <?php echo e(request('tipe_kos') === 'putra' ? 'selected' : ''); ?>>Putra</option>
                <option value="putri" <?php echo e(request('tipe_kos') === 'putri' ? 'selected' : ''); ?>>Putri</option>
                <option value="campur" <?php echo e(request('tipe_kos') === 'campur' ? 'selected' : ''); ?>>Campur</option>
            </select>
            <button type="submit" class="bg-gradient-to-r from-brand-500 to-brand-600 text-white py-2.5 rounded-xl font-semibold text-sm hover:shadow-lg hover:shadow-brand-500/25 transition-all flex items-center justify-center gap-2">
                <i data-feather="search" class="w-4 h-4"></i> Cari
            </button>
        </div>
        <div class="flex flex-wrap gap-2 mt-3">
            <span class="text-xs text-gray-500">Urutkan:</span>
            <?php $__currentLoopData = ['rating' => '⭐ Rating', 'harga_asc' => '💰 Termurah', 'harga_desc' => '💎 Termahal']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('kos.search', array_merge(request()->query(), ['sort' => $key]))); ?>"
                   class="px-2.5 py-1 rounded-lg text-xs transition <?php echo e(request('sort', 'rating') === $key ? 'bg-brand-500/20 text-brand-400 border border-brand-500/30' : 'bg-white/5 text-gray-400 hover:bg-white/10'); ?>">
                    <?php echo e($label); ?>

                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </form>

    
    <?php if($results->isEmpty()): ?>
        <div class="glass rounded-2xl p-12 text-center">
            <span class="text-5xl">🔍</span>
            <h3 class="text-lg font-semibold text-white mt-4">Tidak Ditemukan</h3>
            <p class="text-sm text-gray-400 mt-1">Coba ubah filter pencarian Anda</p>
        </div>
    <?php else: ?>
        <p class="text-xs text-gray-500 mb-4">Menampilkan <?php echo e($results->total()); ?> kos</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kos): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('kos.detail', $kos->id)); ?>" class="glass rounded-2xl overflow-hidden hover:border-brand-500/20 transition-all group">
                    
                    <div class="relative h-44 bg-white/5 overflow-hidden">
                        <?php if($kos->foto_utama): ?>
                            <img src="<?php echo e(asset('storage/' . $kos->foto_utama)); ?>" alt="<?php echo e($kos->nama_kos); ?>"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center text-5xl">🏠</div>
                        <?php endif; ?>

                        
                        <div class="absolute top-3 left-3 flex gap-1.5">
                            <span class="px-2 py-1 rounded-lg text-[10px] font-medium bg-black/50 backdrop-blur-sm text-white">
                                Kos <?php echo e(ucfirst($kos->tipe_kos)); ?>

                            </span>
                            <?php if($kos->hasActiveSubscription()): ?>
                                <span class="px-2 py-1 rounded-lg text-[10px] font-medium bg-accent-500/80 text-black animate-pulse">
                                    ⭐ Premium
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="absolute top-3 right-3">
                            <span class="px-2 py-1 rounded-lg text-[10px] font-medium bg-brand-500/80 text-white">
                                ✓ Verified
                            </span>
                        </div>
                    </div>

                    
                    <div class="p-4">
                        <h3 class="font-semibold text-white text-sm group-hover:text-brand-400 transition truncate"><?php echo e($kos->nama_kos); ?></h3>
                        <p class="text-xs text-gray-400 mt-0.5 flex items-center gap-1 truncate">
                            <i data-feather="map-pin" class="w-3 h-3 flex-shrink-0"></i>
                            <?php echo e($kos->alamat); ?>

                        </p>

                        
                        <div class="flex items-center gap-1.5 mt-2">
                            <span class="text-accent-400 text-xs">★ <?php echo e(number_format($kos->rating_avg, 1)); ?></span>
                            <span class="text-gray-600 text-xs">(<?php echo e($kos->rating_count); ?>)</span>
                            <?php if($kos->kampus_terdekat): ?>
                                <span class="text-gray-600 text-xs">•</span>
                                <span class="text-xs text-gray-400">🎓 <?php echo e($kos->kampus_terdekat); ?></span>
                            <?php endif; ?>
                        </div>

                        
                        <div class="flex items-center justify-between mt-3 pt-3 border-t border-white/5">
                            <div>
                                <span class="text-brand-400 font-bold text-sm">Rp <?php echo e(number_format($kos->harga_mulai ?? 0, 0, ',', '.')); ?></span>
                                <span class="text-gray-500 text-xs">/bln</span>
                            </div>
                            <span class="text-xs text-gray-400 bg-white/5 px-2 py-1 rounded-lg">
                                🛏️ <?php echo e($kos->total_kamar_kosong); ?> kosong
                            </span>
                        </div>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="mt-8"><?php echo e($results->appends(request()->query())->links()); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\KoSesurabaya\resources\views/kos/search.blade.php ENDPATH**/ ?>