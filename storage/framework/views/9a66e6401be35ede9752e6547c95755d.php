<?php $__env->startSection('title', 'Daftarkan Kos - Langkah 1 - KoSeSurabaya'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl mx-auto px-4 py-8">
    
    <div class="flex items-center justify-center gap-2 mb-8">
        <?php $__currentLoopData = [['1', 'Info Dasar', true], ['2', 'Foto & Fasilitas', false], ['3', 'Tipe Kamar', false]]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$num, $label, $active]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold <?php echo e($active ? 'bg-brand-500 text-white' : 'bg-white/10 text-gray-500'); ?>"><?php echo e($num); ?></div>
                <span class="text-xs <?php echo e($active ? 'text-white' : 'text-gray-500'); ?> hidden sm:inline"><?php echo e($label); ?></span>
            </div>
            <?php if(!$loop->last): ?>
                <div class="w-8 h-px <?php echo e($active ? 'bg-brand-500' : 'bg-white/10'); ?>"></div>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <div class="glass rounded-3xl p-6 md:p-8 shadow-2xl">
        <h1 class="text-xl font-bold text-white mb-1">📋 Informasi Dasar Kos</h1>
        <p class="text-sm text-gray-400 mb-6">Langkah 1 dari 3 — Isi detail properti Anda</p>

        <form method="POST" action="<?php echo e(route('owner.kos.store.step1')); ?>" class="space-y-5">
            <?php echo csrf_field(); ?>

            
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1.5">Nama Kos *</label>
                <input type="text" name="nama_kos" value="<?php echo e(old('nama_kos', session('kos_wizard.step1.nama_kos'))); ?>" required
                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-gray-200 focus:border-brand-500 focus:outline-none transition <?php $__errorArgs = ['nama_kos'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500/50 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       placeholder="Contoh: Kost Mawar Surabaya">
                <?php $__errorArgs = ['nama_kos'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-400 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1.5">Alamat Lengkap *</label>
                <textarea name="alamat" rows="2" required
                          class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-gray-200 focus:border-brand-500 focus:outline-none transition resize-none <?php $__errorArgs = ['alamat'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500/50 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                          placeholder="Jl. Semolowaru No. 45, Sukolilo"><?php echo e(old('alamat', session('kos_wizard.step1.alamat'))); ?></textarea>
                <?php $__errorArgs = ['alamat'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-400 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1.5">Kecamatan *</label>
                    <input type="text" name="kecamatan" value="<?php echo e(old('kecamatan', session('kos_wizard.step1.kecamatan'))); ?>" required
                           class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-gray-200 focus:border-brand-500 focus:outline-none transition"
                           placeholder="Sukolilo">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1.5">Tipe Kos *</label>
                    <select name="tipe_kos" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-gray-200 focus:border-brand-500 focus:outline-none transition">
                        <option value="putra" <?php echo e(old('tipe_kos', session('kos_wizard.step1.tipe_kos')) === 'putra' ? 'selected' : ''); ?>>🧑 Putra</option>
                        <option value="putri" <?php echo e(old('tipe_kos', session('kos_wizard.step1.tipe_kos')) === 'putri' ? 'selected' : ''); ?>>👩 Putri</option>
                        <option value="campur" <?php echo e(old('tipe_kos', session('kos_wizard.step1.tipe_kos')) === 'campur' ? 'selected' : ''); ?>>👥 Campur</option>
                    </select>
                </div>
            </div>

            
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1.5">📍 Pilih Lokasi di Peta *</label>
                <p class="text-xs text-gray-500 mb-2">Klik pada peta untuk menentukan lokasi kos Anda</p>
                <div id="location-picker-map" class="w-full h-56 rounded-xl overflow-hidden border border-white/10 mb-2"></div>
                <div class="grid grid-cols-2 gap-3">
                    <input type="text" name="latitude" id="lat-input" value="<?php echo e(old('latitude', session('kos_wizard.step1.latitude', '-7.2820'))); ?>" required readonly
                           class="bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-xs text-gray-400">
                    <input type="text" name="longitude" id="lng-input" value="<?php echo e(old('longitude', session('kos_wizard.step1.longitude', '112.7952'))); ?>" required readonly
                           class="bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-xs text-gray-400">
                </div>
            </div>

            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1.5">Kontak Pemilik *</label>
                    <input type="text" name="kontak_pemilik" value="<?php echo e(old('kontak_pemilik', session('kos_wizard.step1.kontak_pemilik'))); ?>" required
                           class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-gray-200 focus:border-brand-500 focus:outline-none transition"
                           placeholder="08xxxxxxxxxx">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1.5">Kelurahan</label>
                    <input type="text" name="kelurahan" value="<?php echo e(old('kelurahan', session('kos_wizard.step1.kelurahan'))); ?>"
                           class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-gray-200 focus:border-brand-500 focus:outline-none transition"
                           placeholder="Semolowaru">
                </div>
            </div>

            
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1.5">Deskripsi</label>
                <textarea name="deskripsi" rows="3"
                          class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-gray-200 focus:border-brand-500 focus:outline-none transition resize-none"
                          placeholder="Deskripsi singkat tentang kos Anda..."><?php echo e(old('deskripsi', session('kos_wizard.step1.deskripsi'))); ?></textarea>
            </div>

            
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1.5">Peraturan Kos</label>
                <textarea name="peraturan" rows="3"
                          class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-gray-200 focus:border-brand-500 focus:outline-none transition resize-none"
                          placeholder="Contoh: Tidak boleh bawa hewan, tamu max jam 22.00..."><?php echo e(old('peraturan', session('kos_wizard.step1.peraturan'))); ?></textarea>
            </div>

            
            <button type="submit"
                    class="w-full bg-gradient-to-r from-brand-500 to-brand-600 text-white py-3 rounded-xl font-semibold text-sm hover:shadow-lg hover:shadow-brand-500/25 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                Lanjut ke Foto & Fasilitas
                <i data-feather="arrow-right" class="w-4 h-4"></i>
            </button>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const initLat = parseFloat(document.getElementById('lat-input').value) || -7.2820;
    const initLng = parseFloat(document.getElementById('lng-input').value) || 112.7952;

    const map = L.map('location-picker-map').setView([initLat, initLng], 15);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    let marker = L.marker([initLat, initLng], { draggable: true }).addTo(map);

    marker.on('dragend', function(e) {
        const pos = e.target.getLatLng();
        document.getElementById('lat-input').value = pos.lat.toFixed(8);
        document.getElementById('lng-input').value = pos.lng.toFixed(8);
    });

    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        document.getElementById('lat-input').value = e.latlng.lat.toFixed(8);
        document.getElementById('lng-input').value = e.latlng.lng.toFixed(8);
    });

    feather.replace();
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\KoSesurabaya\resources\views/owner/kos/create-step1.blade.php ENDPATH**/ ?>