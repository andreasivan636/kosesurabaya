<?php $__env->startSection('title', 'Pengguna - Admin - KoSeSurabaya'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-white">Manajemen Pengguna</h1>
            <p class="text-sm text-gray-400 mt-0.5">Kelola semua pengguna platform</p>
        </div>
        <a href="<?php echo e(route('admin.dashboard')); ?>" class="text-sm text-gray-400 hover:text-white transition flex items-center gap-1">
            <i data-feather="arrow-left" class="w-4 h-4"></i> Dashboard
        </a>
    </div>

    
    <div class="flex flex-col sm:flex-row gap-3 mb-6">
        <form method="GET" class="flex-1 flex gap-2">
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari nama atau email..."
                   class="flex-1 bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-gray-200 focus:border-brand-500 focus:outline-none transition">
            <button type="submit" class="px-4 py-2.5 rounded-xl bg-brand-500/20 text-brand-400 text-sm font-medium hover:bg-brand-500/30 transition border border-brand-500/30">
                <i data-feather="search" class="w-4 h-4"></i>
            </button>
        </form>
        <div class="flex gap-2">
            <?php $__currentLoopData = ['all' => 'Semua', 'customer' => '🎓 Customer', 'owner' => '🏘️ Pemilik', 'admin' => '🛡️ Admin']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('admin.users', ['role' => $key === 'all' ? null : $key, 'search' => request('search')])); ?>"
                   class="px-3 py-2 rounded-xl text-xs font-medium transition <?php echo e(request('role') === $key || (!request('role') && $key === 'all') ? 'bg-brand-500/20 text-brand-400 border border-brand-500/30' : 'bg-white/5 text-gray-400 border border-white/10 hover:bg-white/10'); ?>">
                    <?php echo e($label); ?>

                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    
    <div class="glass rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/5">
                        <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Pengguna</th>
                        <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Role</th>
                        <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Phone</th>
                        <th class="text-center px-4 py-3 text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="text-right px-4 py-3 text-xs font-medium text-gray-500 uppercase">Bergabung</th>
                        <th class="text-center px-4 py-3 text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-white/5 transition">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-xs font-bold text-white flex-shrink-0">
                                        <?php echo e(substr($user->name, 0, 1)); ?>

                                    </div>
                                    <div>
                                        <div class="text-white text-xs font-medium"><?php echo e($user->name); ?></div>
                                        <div class="text-gray-500 text-[10px]"><?php echo e($user->email); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <?php
                                    $roleColors = ['admin' => 'bg-red-500/20 text-red-400', 'owner' => 'bg-blue-500/20 text-blue-400', 'customer' => 'bg-brand-500/20 text-brand-400'];
                                ?>
                                <span class="px-2 py-1 rounded-lg text-[10px] font-medium <?php echo e($roleColors[$user->role]); ?>"><?php echo e(ucfirst($user->role)); ?></span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-400"><?php echo e($user->phone ?? '-'); ?></td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center gap-1 text-xs <?php echo e($user->is_active ? 'text-brand-400' : 'text-red-400'); ?>">
                                    <div class="w-2 h-2 rounded-full <?php echo e($user->is_active ? 'bg-brand-400' : 'bg-red-400'); ?>"></div>
                                    <?php echo e($user->is_active ? 'Aktif' : 'Nonaktif'); ?>

                                </span>
                            </td>
                            <td class="px-4 py-3 text-right text-xs text-gray-500"><?php echo e($user->created_at->format('d/m/Y')); ?></td>
                            <td class="px-4 py-3 text-center">
                                <?php if($user->role !== 'admin'): ?>
                                    <form method="POST" action="<?php echo e(route('admin.users.toggle', $user->id)); ?>" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="px-3 py-1.5 rounded-lg text-[10px] font-medium transition <?php echo e($user->is_active ? 'bg-red-500/20 text-red-400 hover:bg-red-500/30 border border-red-500/30' : 'bg-brand-500/20 text-brand-400 hover:bg-brand-500/30 border border-brand-500/30'); ?>">
                                            <?php echo e($user->is_active ? 'Nonaktifkan' : 'Aktifkan'); ?>

                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" class="px-4 py-12 text-center text-gray-500">Tidak ada pengguna ditemukan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6"><?php echo e($users->appends(request()->query())->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\KoSesurabaya\resources\views/admin/users.blade.php ENDPATH**/ ?>