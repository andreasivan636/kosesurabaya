<?php $__env->startSection('title', 'Daftarkan Kos - Langkah 3 - KoSeSurabaya'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl mx-auto px-4 py-8"
     x-data="{
         showTipe2: <?php echo e(old('tipe_b_enabled', old('tipe_b_nama') ? '1' : '0') == '1' ? 'true' : 'false'); ?>,
         isSubmitting: false,
         errorMessage: '',
         fieldErrors: {},
         fieldLabels: {
             'tipe_a_nama': 'Nama Tipe Kamar 1',
             'tipe_a_harga_bulanan': 'Harga/Bulan Tipe 1',
             'tipe_a_harga_tahunan': 'Harga/Tahun Tipe 1',
             'tipe_a_jumlah_kamar': 'Jumlah Kamar Tipe 1',
             'tipe_a_kamar_tersedia': 'Kamar Kosong Tipe 1',
             'tipe_a_ukuran': 'Ukuran Kamar Tipe 1',
             'tipe_a_fasilitas': 'Fasilitas Tipe 1',
             'tipe_b_nama': 'Nama Tipe Kamar 2',
             'tipe_b_harga_bulanan': 'Harga/Bulan Tipe 2',
             'tipe_b_harga_tahunan': 'Harga/Tahun Tipe 2',
             'tipe_b_jumlah_kamar': 'Jumlah Kamar Tipe 2',
             'tipe_b_kamar_tersedia': 'Kamar Kosong Tipe 2',
             'tipe_b_ukuran': 'Ukuran Kamar Tipe 2',
             'tipe_b_fasilitas': 'Fasilitas Tipe 2',
             'session': 'Sesi Pendaftaran'
         },
         formatErrorMessage(errText, fieldKey) {
             const label = this.fieldLabels[fieldKey] || fieldKey.replace(/_/g, ' ');
             if (typeof errText === 'string') {
                 if (errText.includes('validation.required') || errText === 'validation.required') {
                     return `${label} wajib diisi.`;
                 }
                 if (errText.includes('validation.min') || errText === 'validation.min') {
                     return `${label} tidak memenuhi batas minimum.`;
                 }
                 if (errText.includes('validation.max') || errText === 'validation.max') {
                     return `${label} melebihi batas maksimum.`;
                 }
                 if (errText.includes('validation.integer') || errText === 'validation.integer') {
                     return `${label} harus berupa angka bulat.`;
                 }
                 if (errText.includes('validation.array') || errText === 'validation.array') {
                     return `${label} harus dipilih minimal 1 pilihan.`;
                 }
                 return errText.replace(/^validation\./, '');
             }
             return String(errText);
         },
         async submitForm(e) {
             this.isSubmitting = true;
             this.errorMessage = '';
             this.fieldErrors = {};

             const form = e.target;
             const formData = new FormData(form);

             try {
                 const response = await fetch(form.action, {
                     method: 'POST',
                     headers: {
                         'X-CSRF-TOKEN': window.CSRF_TOKEN || document.querySelector('meta[name=\'csrf-token\']')?.getAttribute('content'),
                         'Accept': 'application/json',
                         'X-Requested-With': 'XMLHttpRequest'
                     },
                     body: formData,
                 });

                 const data = await response.json().catch(() => ({}));

                 if (response.ok && (data.success || response.status === 200 || response.status === 201)) {
                     window.location.href = data.redirect_url || '<?php echo e(route('owner.dashboard')); ?>';
                 } else {
                     this.isSubmitting = false;
                     let errorItems = [];
                     if (data.errors && typeof data.errors === 'object') {
                         this.fieldErrors = {};
                         for (const [key, errs] of Object.entries(data.errors)) {
                             const arr = Array.isArray(errs) ? errs : [errs];
                             const formatted = arr.map(e => this.formatErrorMessage(e, key));
                             this.fieldErrors[key] = formatted;
                             errorItems.push(...formatted);
                         }
                     }

                     let userMsg = errorItems.length > 0
                         ? 'Mohon periksa isian berikut:\n\n• ' + errorItems.join('\n• ')
                         : (data.message || 'Terjadi kesalahan validasi.');

                     this.errorMessage = userMsg;
                     alert(userMsg);
                 }
             } catch (error) {
                 this.isSubmitting = false;
                 this.errorMessage = 'Terjadi kesalahan jaringan atau koneksi server: ' + error.message;
                 alert(this.errorMessage);
             }
         }
     }">
    
    <div class="flex items-center justify-center gap-2 mb-8">
        <?php $__currentLoopData = [['1', 'Info Dasar'], ['2', 'Foto & Fasilitas'], ['3', 'Tipe Kamar']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$num, $label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold <?php echo e($loop->index < 2 ? 'bg-brand-500/30 text-brand-400' : 'bg-brand-500 text-white'); ?>"><?php echo e($num); ?></div>
                <span class="text-xs <?php echo e($loop->last ? 'text-white' : 'text-gray-500'); ?> hidden sm:inline"><?php echo e($label); ?></span>
            </div>
            <?php if(!$loop->last): ?>
                <div class="w-8 h-px bg-brand-500"></div>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <div class="glass rounded-3xl p-6 md:p-8 shadow-2xl">
        <div class="flex flex-wrap items-center justify-between gap-2 mb-1">
            <h1 class="text-xl font-bold text-white">🏷️ Tipe Kamar (1 - 2 Tipe)</h1>
            <span class="text-xs px-2.5 py-1 rounded-full bg-brand-500/20 text-brand-400 font-semibold border border-brand-500/30">
                Dinamis: Min. 1 & Maks. 2 Tipe
            </span>
        </div>
        <p class="text-sm text-gray-400 mb-6">Langkah 3 dari 3 — Masukkan minimal 1 dan maksimal 2 tipe kamar untuk kos Anda.</p>

        
        <div x-show="errorMessage" x-cloak class="mb-6 p-4 rounded-2xl bg-red-500/15 border border-red-500/30 text-red-300 flex items-start gap-3 shadow-lg shadow-red-500/10">
            <div class="w-8 h-8 rounded-xl bg-red-500/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                <i data-feather="alert-circle" class="w-5 h-5 text-red-400"></i>
            </div>
            <div class="flex-1">
                <h4 class="text-sm font-bold text-white mb-1">Gagal Menyimpan Data</h4>
                <p class="text-xs text-red-200 whitespace-pre-line leading-relaxed" x-text="errorMessage"></p>
            </div>
        </div>

        <form method="POST" action="<?php echo e(route('owner.kos.store.step3')); ?>" @submit.prevent="submitForm($event)" class="space-y-6">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="tipe_b_enabled" :value="showTipe2 ? '1' : '0'">

            <?php
                $fasilitasOptions = [
                    'AC', 'WiFi', 'Kamar Mandi Dalam', 'Kasur', 'Lemari',
                    'Meja Belajar', 'Kipas Angin', 'TV', 'Dapur', 'Parkir Motor',
                ];
            ?>

            
            <div class="glass rounded-2xl p-5 border-l-4 border-l-accent-400">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">⭐</span>
                        <div>
                            <h3 class="font-bold text-white">Tipe Kamar 1 (Utama)</h3>
                            <span class="text-xs text-brand-400 font-medium">Wajib Diisi</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">Nama Tipe *</label>
                        <input type="text" name="tipe_a_nama"
                               value="<?php echo e(old('tipe_a_nama', 'Deluxe')); ?>" required
                               class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-gray-200 focus:border-brand-500 focus:outline-none transition"
                               placeholder="Misal: Deluxe / Kamar AC">
                        <?php $__errorArgs = ['tipe_a_nama'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-400 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <template x-if="fieldErrors.tipe_a_nama"><p class="text-xs text-red-400 mt-1" x-text="fieldErrors.tipe_a_nama[0]"></p></template>
                    </div>

                    
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1">Harga /Bulan (Rp) *</label>
                            <input type="number" name="tipe_a_harga_bulanan"
                                   value="<?php echo e(old('tipe_a_harga_bulanan')); ?>" required min="100000"
                                   class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-gray-200 focus:border-brand-500 focus:outline-none transition"
                                   placeholder="850000">
                            <?php $__errorArgs = ['tipe_a_harga_bulanan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-400 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <template x-if="fieldErrors.tipe_a_harga_bulanan"><p class="text-xs text-red-400 mt-1" x-text="fieldErrors.tipe_a_harga_bulanan[0]"></p></template>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1">Harga /Tahun (opsional)</label>
                            <input type="number" name="tipe_a_harga_tahunan"
                                   value="<?php echo e(old('tipe_a_harga_tahunan')); ?>" min="1000000"
                                   class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-gray-200 focus:border-brand-500 focus:outline-none transition"
                                   placeholder="9000000">
                        </div>
                    </div>

                    
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1">Jumlah Kamar *</label>
                            <input type="number" name="tipe_a_jumlah_kamar"
                                   value="<?php echo e(old('tipe_a_jumlah_kamar', 3)); ?>" required min="1"
                                   class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2.5 text-sm text-gray-200 focus:border-brand-500 focus:outline-none transition">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1">Kamar Kosong *</label>
                            <input type="number" name="tipe_a_kamar_tersedia"
                                   value="<?php echo e(old('tipe_a_kamar_tersedia', 3)); ?>" required min="0"
                                   class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2.5 text-sm text-gray-200 focus:border-brand-500 focus:outline-none transition">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1">Ukuran (m²)</label>
                            <input type="text" name="tipe_a_ukuran"
                                   value="<?php echo e(old('tipe_a_ukuran')); ?>"
                                   class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2.5 text-sm text-gray-200 focus:border-brand-500 focus:outline-none transition"
                                   placeholder="3x4">
                            <?php $__errorArgs = ['tipe_a_ukuran'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-400 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <template x-if="fieldErrors.tipe_a_ukuran"><p class="text-xs text-red-400 mt-1" x-text="fieldErrors.tipe_a_ukuran[0]"></p></template>
                        </div>
                    </div>

                    
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-2">Fasilitas Kamar * (min. 1)</label>
                        <div class="flex flex-wrap gap-2">
                            <?php $__currentLoopData = $fasilitasOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white/5 border border-white/10 cursor-pointer hover:bg-white/10 transition has-[:checked]:bg-brand-500/10 has-[:checked]:border-brand-500/30 text-xs text-gray-300">
                                    <input type="checkbox" name="tipe_a_fasilitas[]" value="<?php echo e($fas); ?>"
                                           <?php echo e(in_array($fas, old('tipe_a_fasilitas', [])) ? 'checked' : ''); ?>

                                           class="w-3.5 h-3.5 rounded bg-white/10 border-white/20 text-brand-500 focus:ring-brand-500 focus:ring-offset-0">
                                    <?php echo e($fas); ?>

                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php $__errorArgs = ['tipe_a_fasilitas'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-400 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <template x-if="fieldErrors.tipe_a_fasilitas"><p class="text-xs text-red-400 mt-1" x-text="fieldErrors.tipe_a_fasilitas[0]"></p></template>
                    </div>

                    
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">Deskripsi Tipe</label>
                        <textarea name="tipe_a_deskripsi" rows="2"
                                  class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-gray-200 focus:border-brand-500 focus:outline-none transition resize-none"
                                  placeholder="Deskripsi singkat tipe kamar ini..."><?php echo e(old('tipe_a_deskripsi')); ?></textarea>
                    </div>
                </div>
            </div>

            
            <div>
                
                <div x-show="!showTipe2" x-transition>
                    <button type="button" @click="showTipe2 = true"
                            class="w-full py-4 border-2 border-dashed border-brand-500/40 hover:border-brand-400 hover:bg-brand-500/10 rounded-2xl flex items-center justify-center gap-2 text-sm text-brand-400 font-semibold transition-all">
                        <i data-feather="plus-circle" class="w-5 h-5"></i>
                        + Tambah Tipe Kamar Kedua (Maks. 2 Tipe)
                    </button>
                </div>

                
                <div x-show="showTipe2" x-transition class="glass rounded-2xl p-5 border-l-4 border-l-brand-400">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">🏠</span>
                            <div>
                                <h3 class="font-bold text-white">Tipe Kamar 2 (Kedua)</h3>
                                <span class="text-xs text-gray-400">Opsional</span>
                            </div>
                        </div>
                        <button type="button" @click="showTipe2 = false"
                                class="px-3 py-1.5 rounded-xl bg-red-500/10 text-red-400 hover:bg-red-500/20 text-xs font-medium transition flex items-center gap-1 border border-red-500/20">
                            <i data-feather="trash-2" class="w-3.5 h-3.5"></i>
                            Hapus Tipe 2
                        </button>
                    </div>

                    <div class="space-y-4">
                        
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1">Nama Tipe *</label>
                            <input type="text" name="tipe_b_nama"
                                   value="<?php echo e(old('tipe_b_nama')); ?>"
                                   :disabled="!showTipe2"
                                   :required="showTipe2"
                                   class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-gray-200 focus:border-brand-500 focus:outline-none transition"
                                   placeholder="Misal: Standard / Kamar Non-AC">
                            <?php $__errorArgs = ['tipe_b_nama'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-400 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <template x-if="fieldErrors.tipe_b_nama"><p class="text-xs text-red-400 mt-1" x-text="fieldErrors.tipe_b_nama[0]"></p></template>
                        </div>

                        
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-400 mb-1">Harga /Bulan (Rp) *</label>
                                <input type="number" name="tipe_b_harga_bulanan"
                                       value="<?php echo e(old('tipe_b_harga_bulanan')); ?>"
                                       :disabled="!showTipe2"
                                       :required="showTipe2" min="100000"
                                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-gray-200 focus:border-brand-500 focus:outline-none transition"
                                       placeholder="600000">
                                <?php $__errorArgs = ['tipe_b_harga_bulanan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-400 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <template x-if="fieldErrors.tipe_b_harga_bulanan"><p class="text-xs text-red-400 mt-1" x-text="fieldErrors.tipe_b_harga_bulanan[0]"></p></template>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-400 mb-1">Harga /Tahun (opsional)</label>
                                <input type="number" name="tipe_b_harga_tahunan"
                                       value="<?php echo e(old('tipe_b_harga_tahunan')); ?>" min="1000000"
                                       :disabled="!showTipe2"
                                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-gray-200 focus:border-brand-500 focus:outline-none transition"
                                       placeholder="6500000">
                            </div>
                        </div>

                        
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-400 mb-1">Jumlah Kamar *</label>
                                <input type="number" name="tipe_b_jumlah_kamar"
                                       value="<?php echo e(old('tipe_b_jumlah_kamar', 3)); ?>"
                                       :disabled="!showTipe2"
                                       :required="showTipe2" min="1"
                                       class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2.5 text-sm text-gray-200 focus:border-brand-500 focus:outline-none transition">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-400 mb-1">Kamar Kosong *</label>
                                <input type="number" name="tipe_b_kamar_tersedia"
                                       value="<?php echo e(old('tipe_b_kamar_tersedia', 3)); ?>"
                                       :disabled="!showTipe2"
                                       :required="showTipe2" min="0"
                                       class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2.5 text-sm text-gray-200 focus:border-brand-500 focus:outline-none transition">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-400 mb-1">Ukuran (m²)</label>
                                <input type="text" name="tipe_b_ukuran"
                                       value="<?php echo e(old('tipe_b_ukuran')); ?>"
                                       :disabled="!showTipe2"
                                       class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2.5 text-sm text-gray-200 focus:border-brand-500 focus:outline-none transition"
                                       placeholder="3x3">
                                <?php $__errorArgs = ['tipe_b_ukuran'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-400 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <template x-if="fieldErrors.tipe_b_ukuran"><p class="text-xs text-red-400 mt-1" x-text="fieldErrors.tipe_b_ukuran[0]"></p></template>
                            </div>
                        </div>

                        
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-2">Fasilitas Kamar * (min. 1)</label>
                            <div class="flex flex-wrap gap-2">
                                <?php $__currentLoopData = $fasilitasOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label class="flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white/5 border border-white/10 cursor-pointer hover:bg-white/10 transition has-[:checked]:bg-brand-500/10 has-[:checked]:border-brand-500/30 text-xs text-gray-300">
                                        <input type="checkbox" name="tipe_b_fasilitas[]" value="<?php echo e($fas); ?>"
                                               <?php echo e(in_array($fas, old('tipe_b_fasilitas', [])) ? 'checked' : ''); ?>

                                               :disabled="!showTipe2"
                                               class="w-3.5 h-3.5 rounded bg-white/10 border-white/20 text-brand-500 focus:ring-brand-500 focus:ring-offset-0">
                                        <?php echo e($fas); ?>

                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <?php $__errorArgs = ['tipe_b_fasilitas'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-400 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <template x-if="fieldErrors.tipe_b_fasilitas"><p class="text-xs text-red-400 mt-1" x-text="fieldErrors.tipe_b_fasilitas[0]"></p></template>
                        </div>

                        
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1">Deskripsi Tipe</label>
                            <textarea name="tipe_b_deskripsi" rows="2"
                                      :disabled="!showTipe2"
                                      class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-gray-200 focus:border-brand-500 focus:outline-none transition resize-none"
                                      placeholder="Deskripsi singkat tipe kamar ini..."><?php echo e(old('tipe_b_deskripsi')); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="flex gap-3 pt-2">
                <a href="<?php echo e(route('owner.kos.create.step2')); ?>"
                   class="flex-1 text-center px-4 py-3 rounded-xl bg-white/5 text-gray-400 text-sm font-medium hover:bg-white/10 transition border border-white/10">
                    ← Kembali
                </a>
                <button type="submit"
                        :disabled="isSubmitting"
                        :class="isSubmitting ? 'opacity-70 cursor-not-allowed' : ''"
                        class="flex-1 bg-gradient-to-r from-brand-500 to-brand-600 text-white py-3 rounded-xl font-semibold text-sm hover:shadow-lg hover:shadow-brand-500/25 transition-all active:scale-[0.98] flex items-center justify-center gap-2 cursor-pointer">
                    <template x-if="!isSubmitting">
                        <span class="flex items-center gap-2">
                            <i data-feather="check" class="w-4 h-4"></i>
                            Daftarkan Kos
                        </span>
                    </template>
                    <template x-if="isSubmitting">
                        <span class="flex items-center gap-2">
                            <span class="animate-spin inline-block">⏳</span>
                            Menyimpan & Mendaftarkan Kos...
                        </span>
                    </template>
                </button>
            </div>

            <p class="text-xs text-gray-500 text-center mt-2">
                ⏳ Setelah didaftarkan, kos akan menunggu verifikasi admin sebelum tayang di platform.
            </p>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\KoSesurabaya\resources\views/owner/kos/create-step3.blade.php ENDPATH**/ ?>