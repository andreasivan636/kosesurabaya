<?php $__env->startSection('title', 'Daftarkan Kos - Langkah 2 - KoSeSurabaya'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl mx-auto px-4 py-8">
    
    <div class="flex items-center justify-center gap-2 mb-8">
        <?php $__currentLoopData = [['1', 'Info Dasar', false], ['2', 'Foto & Fasilitas', true], ['3', 'Tipe Kamar', false]]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$num, $label, $active]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold <?php echo e($loop->index < 1 ? 'bg-brand-500/30 text-brand-400' : ($active ? 'bg-brand-500 text-white' : 'bg-white/10 text-gray-500')); ?>"><?php echo e($num); ?></div>
                <span class="text-xs <?php echo e($active ? 'text-white' : 'text-gray-500'); ?> hidden sm:inline"><?php echo e($label); ?></span>
            </div>
            <?php if(!$loop->last): ?>
                <div class="w-8 h-px <?php echo e($loop->index < 1 ? 'bg-brand-500' : 'bg-white/10'); ?>"></div>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <div class="glass rounded-3xl p-6 md:p-8 shadow-2xl">
        <h1 class="text-xl font-bold text-white mb-1">📸 Foto & Fasilitas Umum</h1>
        <p class="text-sm text-gray-400 mb-6">Langkah 2 dari 3 — Upload foto dan pilih fasilitas</p>

        <form method="POST" action="<?php echo e(route('owner.kos.store.step2')); ?>" enctype="multipart/form-data" class="space-y-5">
            <?php echo csrf_field(); ?>

            
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1.5">Foto Utama * (tampil di peta & pencarian)</label>
                <div class="relative" x-data="{ preview: null }">
                    <input type="file" name="foto_utama" accept="image/*" required
                           @change="preview = URL.createObjectURL($event.target.files[0])"
                           class="w-full bg-white/5 border border-white/10 border-dashed rounded-xl px-4 py-8 text-sm text-gray-400 focus:border-brand-500 focus:outline-none transition cursor-pointer file:mr-3 file:rounded-lg file:border-0 file:bg-brand-500/20 file:text-brand-400 file:px-3 file:py-1.5 file:text-xs file:font-medium file:cursor-pointer">
                    <img x-show="preview" :src="preview" class="mt-2 w-full h-40 object-cover rounded-xl" alt="Preview">
                </div>
                <?php $__errorArgs = ['foto_utama'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-400 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1.5">Foto Tambahan (opsional, max 5)</label>
                <input type="file" name="foto[]" accept="image/*" multiple
                       class="w-full bg-white/5 border border-white/10 border-dashed rounded-xl px-4 py-6 text-sm text-gray-400 focus:border-brand-500 focus:outline-none transition cursor-pointer file:mr-3 file:rounded-lg file:border-0 file:bg-white/10 file:text-gray-300 file:px-3 file:py-1.5 file:text-xs file:font-medium file:cursor-pointer">
            </div>

            
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-3">🏢 Fasilitas Umum (area bersama)</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    <?php
                        $fasilitasUmumOptions = [
                            'Parkir Motor', 'Parkir Mobil', 'Dapur Bersama', 'Ruang Tamu',
                            'Jemuran', 'CCTV', 'Penjaga Kos', 'Laundry',
                            'WiFi Area', 'Musholla', 'Taman', 'Rooftop',
                        ];
                    ?>
                    <?php $__currentLoopData = $fasilitasUmumOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label class="flex items-center gap-2 px-3 py-2.5 rounded-xl bg-white/5 border border-white/10 cursor-pointer hover:bg-white/10 transition has-[:checked]:bg-brand-500/10 has-[:checked]:border-brand-500/30">
                            <input type="checkbox" name="fasilitas_umum[]" value="<?php echo e($fas); ?>"
                                   class="w-4 h-4 rounded bg-white/10 border-white/20 text-brand-500 focus:ring-brand-500 focus:ring-offset-0">
                            <span class="text-xs text-gray-300"><?php echo e($fas); ?></span>
                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            
            <div class="flex gap-3">
                <a href="<?php echo e(route('owner.kos.create.step1')); ?>"
                   class="flex-1 text-center px-4 py-3 rounded-xl bg-white/5 text-gray-400 text-sm font-medium hover:bg-white/10 transition border border-white/10">
                    ← Kembali
                </a>
                <button type="submit"
                        class="flex-1 bg-gradient-to-r from-brand-500 to-brand-600 text-white py-3 rounded-xl font-semibold text-sm hover:shadow-lg hover:shadow-brand-500/25 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                    Lanjut ke Tipe Kamar
                    <i data-feather="arrow-right" class="w-4 h-4"></i>
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\KoSesurabaya\resources\views/owner/kos/create-step2.blade.php ENDPATH**/ ?>