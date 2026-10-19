<?php $__env->startSection('title', 'Daftar Akun - KoSeSurabaya'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        <div class="glass rounded-3xl p-8 shadow-2xl animate-slide-up">
            
            <div class="text-center mb-6">
                <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-brand-400 to-accent-400 rounded-2xl flex items-center justify-center shadow-lg">
                    <span class="text-3xl">🏠</span>
                </div>
                <h1 class="text-2xl font-bold text-white">Buat Akun Baru</h1>
                <p class="text-sm text-gray-400 mt-1">Bergabung dengan KoSeSurabaya</p>
            </div>

            <form method="POST" action="<?php echo e(route('register.submit')); ?>" class="space-y-4">
                <?php echo csrf_field(); ?>

                
                <div>
                    <label for="name" class="block text-xs font-medium text-gray-400 mb-1.5">Nama Lengkap</label>
                    <div class="relative">
                        <i data-feather="user" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500"></i>
                        <input type="text" name="name" id="name" value="<?php echo e(old('name')); ?>" required autofocus
                               class="w-full bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-3 text-sm text-gray-200 placeholder-gray-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 focus:outline-none transition <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500/50 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               placeholder="Nama lengkap Anda">
                    </div>
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-xs text-red-400 mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                <div>
                    <label for="email" class="block text-xs font-medium text-gray-400 mb-1.5">Email</label>
                    <div class="relative">
                        <i data-feather="mail" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500"></i>
                        <input type="email" name="email" id="email" value="<?php echo e(old('email')); ?>" required
                               class="w-full bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-3 text-sm text-gray-200 placeholder-gray-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 focus:outline-none transition <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500/50 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               placeholder="email@example.com">
                    </div>
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-xs text-red-400 mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                <div>
                    <label for="phone" class="block text-xs font-medium text-gray-400 mb-1.5">No. HP (opsional)</label>
                    <div class="relative">
                        <i data-feather="phone" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500"></i>
                        <input type="tel" name="phone" id="phone" value="<?php echo e(old('phone')); ?>"
                               class="w-full bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-3 text-sm text-gray-200 placeholder-gray-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 focus:outline-none transition"
                               placeholder="08xxxxxxxxxx">
                    </div>
                </div>

                
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-2">Daftar Sebagai</label>
                    <div class="grid grid-cols-2 gap-3" x-data="{ role: '<?php echo e(old('role', 'customer')); ?>' }">
                        <label @click="role = 'customer'" :class="role === 'customer' ? 'border-brand-500 bg-brand-500/10' : 'border-white/10 bg-white/5'"
                               class="relative cursor-pointer rounded-xl border-2 p-4 text-center transition-all hover:bg-white/10">
                            <input type="radio" name="role" value="customer" class="hidden" :checked="role === 'customer'">
                            <span class="text-2xl block mb-1">🎓</span>
                            <span class="text-sm font-medium" :class="role === 'customer' ? 'text-brand-400' : 'text-gray-300'">Mahasiswa</span>
                            <p class="text-[10px] text-gray-500 mt-0.5">Cari & booking kos</p>
                        </label>
                        <label @click="role = 'owner'" :class="role === 'owner' ? 'border-brand-500 bg-brand-500/10' : 'border-white/10 bg-white/5'"
                               class="relative cursor-pointer rounded-xl border-2 p-4 text-center transition-all hover:bg-white/10">
                            <input type="radio" name="role" value="owner" class="hidden" :checked="role === 'owner'">
                            <span class="text-2xl block mb-1">🏘️</span>
                            <span class="text-sm font-medium" :class="role === 'owner' ? 'text-brand-400' : 'text-gray-300'">Pemilik Kos</span>
                            <p class="text-[10px] text-gray-500 mt-0.5">Daftarkan & kelola kos</p>
                        </label>
                    </div>
                    <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-xs text-red-400 mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                <div>
                    <label for="password" class="block text-xs font-medium text-gray-400 mb-1.5">Password</label>
                    <div class="relative">
                        <i data-feather="lock" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500"></i>
                        <input type="password" name="password" id="password" required
                               class="w-full bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-3 text-sm text-gray-200 placeholder-gray-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 focus:outline-none transition <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500/50 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               placeholder="Minimal 8 karakter">
                    </div>
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-xs text-red-400 mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                <div>
                    <label for="password_confirmation" class="block text-xs font-medium text-gray-400 mb-1.5">Konfirmasi Password</label>
                    <div class="relative">
                        <i data-feather="lock" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500"></i>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                               class="w-full bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-3 text-sm text-gray-200 placeholder-gray-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 focus:outline-none transition"
                               placeholder="Ulangi password">
                    </div>
                </div>

                
                <button type="submit"
                        class="w-full bg-gradient-to-r from-brand-500 to-brand-600 text-white py-3 rounded-xl font-semibold text-sm hover:shadow-lg hover:shadow-brand-500/25 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                    <i data-feather="user-plus" class="w-4 h-4"></i>
                    Daftar Sekarang
                </button>
            </form>

            <div class="mt-5 text-center">
                <p class="text-sm text-gray-400">
                    Sudah punya akun?
                    <a href="<?php echo e(route('login')); ?>" class="text-brand-400 hover:text-brand-300 font-medium transition">Masuk</a>
                </p>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\KoSesurabaya\resources\views/auth/register.blade.php ENDPATH**/ ?>