<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="KoSeSurabaya - Platform pencarian kos terpercaya untuk mahasiswa di Surabaya. Cari kos dekat UNTAG, ITS, Unair, Unesa.">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo $__env->yieldContent('title', 'KoSeSurabaya - Cari Kos Aman untuk Mahasiswa'); ?></title>

    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        },
                        accent: {
                            400: '#facc15',
                            500: '#eab308',
                        },
                        dark: {
                            800: '#1e293b',
                            900: '#0f172a',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    animation: {
                        'glow-pulse': 'glow-pulse 2s ease-in-out infinite',
                        'float': 'float 3s ease-in-out infinite',
                        'slide-up': 'slide-up 0.3s ease-out',
                    },
                    keyframes: {
                        'glow-pulse': {
                            '0%, 100%': { boxShadow: '0 0 5px #22c55e, 0 0 10px #22c55e, 0 0 20px #22c55e' },
                            '50%': { boxShadow: '0 0 10px #4ade80, 0 0 20px #4ade80, 0 0 40px #4ade80' },
                        },
                        'float': {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' },
                        },
                        'slide-up': {
                            '0%': { transform: 'translateY(20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                    },
                }
            }
        }
    </script>

    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    
    <script src="https://unpkg.com/feather-icons"></script>

    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <?php echo $__env->yieldContent('styles'); ?>

    <style>
        body { font-family: 'Inter', sans-serif; }

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #1e293b; }
        ::-webkit-scrollbar-thumb { background: #22c55e; border-radius: 3px; }

        /* Leaflet marker glow animation */
        .marker-glow {
            animation: marker-glow-anim 2s ease-in-out infinite;
            border-radius: 50%;
        }
        @keyframes marker-glow-anim {
            0%, 100% { box-shadow: 0 0 8px 4px rgba(34, 197, 94, 0.6); }
            50% { box-shadow: 0 0 16px 8px rgba(74, 222, 128, 0.8); }
        }

        /* Glassmorphism */
        .glass {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .glass-light {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="bg-dark-900 text-gray-100 min-h-screen antialiased">

    
    <nav class="glass sticky top-0 z-50 px-4 py-3">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            
            <a href="<?php echo e(route('home')); ?>" class="flex items-center gap-2 group">
                <div class="w-10 h-10 bg-gradient-to-br from-brand-400 to-brand-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-brand-500/30 transition-shadow">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <span class="text-xl font-bold bg-gradient-to-r from-brand-400 to-accent-400 bg-clip-text text-transparent">
                    KoSeSurabaya
                </span>
            </a>

            
            <div class="hidden md:flex items-center gap-6">
                <a href="<?php echo e(route('home')); ?>" class="text-sm text-gray-300 hover:text-brand-400 transition-colors">Peta Kos</a>
                <a href="<?php echo e(route('kos.search')); ?>" class="text-sm text-gray-300 hover:text-brand-400 transition-colors">Cari Kos</a>
            </div>

            
            <div class="flex items-center gap-3">
                <?php if(auth()->guard()->check()): ?>
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 px-3 py-2 rounded-xl glass-light hover:bg-white/10 transition">
                            <div class="w-8 h-8 bg-gradient-to-br from-brand-400 to-brand-600 rounded-full flex items-center justify-center text-sm font-bold text-white">
                                <?php echo e(substr(Auth::user()->name, 0, 1)); ?>

                            </div>
                            <span class="text-sm hidden sm:inline"><?php echo e(Auth::user()->name); ?></span>
                            <i data-feather="chevron-down" class="w-4 h-4"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition
                             class="absolute right-0 mt-2 w-48 glass rounded-xl py-2 shadow-2xl">
                            <?php if(Auth::user()->isOwner()): ?>
                                <a href="<?php echo e(route('owner.dashboard')); ?>" class="block px-4 py-2 text-sm text-gray-300 hover:text-brand-400 hover:bg-white/5 transition">
                                    <i data-feather="layout" class="w-4 h-4 inline mr-2"></i>Dashboard
                                </a>
                            <?php endif; ?>
                            <?php if(Auth::user()->isAdmin()): ?>
                                <a href="<?php echo e(route('admin.dashboard')); ?>" class="block px-4 py-2 text-sm text-gray-300 hover:text-brand-400 hover:bg-white/5 transition">
                                    <i data-feather="shield" class="w-4 h-4 inline mr-2"></i>Admin Panel
                                </a>
                            <?php endif; ?>
                            <form method="POST" action="<?php echo e(route('logout')); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-white/5 transition">
                                    <i data-feather="log-out" class="w-4 h-4 inline mr-2"></i>Logout
                                </button>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>" class="text-sm text-gray-300 hover:text-white transition px-3 py-2">Masuk</a>
                    <a href="<?php echo e(route('register')); ?>" class="text-sm bg-gradient-to-r from-brand-500 to-brand-600 text-white px-4 py-2 rounded-xl hover:shadow-lg hover:shadow-brand-500/25 transition-all">
                        Daftar
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    
    <?php if(session('success')): ?>
        <div class="flash-alert fixed top-20 right-4 z-50 animate-slide-up">
            <div class="glass bg-brand-600/20 border-brand-500/30 text-brand-300 px-4 py-3 rounded-xl flex items-center gap-2 shadow-2xl">
                <i data-feather="check-circle" class="w-5 h-5"></i>
                <span class="text-sm"><?php echo e(session('success')); ?></span>
            </div>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="flash-alert fixed top-20 right-4 z-50 animate-slide-up">
            <div class="glass bg-red-600/20 border-red-500/30 text-red-300 px-4 py-3 rounded-xl flex items-center gap-2 shadow-2xl">
                <i data-feather="alert-circle" class="w-5 h-5"></i>
                <span class="text-sm"><?php echo e(session('error')); ?></span>
            </div>
        </div>
    <?php endif; ?>

    
    <main>
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    
    <?php echo $__env->make('components.ai-chat-widget', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <footer class="glass mt-12 py-8 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-lg font-bold bg-gradient-to-r from-brand-400 to-accent-400 bg-clip-text text-transparent mb-3">KoSeSurabaya</h3>
                    <p class="text-sm text-gray-400 leading-relaxed">Platform pencarian kos terpercaya untuk mahasiswa Surabaya. Aman dari penipuan, terverifikasi admin.</p>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-200 mb-3">Kampus Partner</h4>
                    <ul class="text-sm text-gray-400 space-y-2">
                        <li>🎓 UNTAG Surabaya</li>
                        <li>🎓 ITS</li>
                        <li>🎓 Universitas Airlangga</li>
                        <li>🎓 Universitas Negeri Surabaya</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-200 mb-3">Keamanan</h4>
                    <ul class="text-sm text-gray-400 space-y-2">
                        <li>✅ Kos Terverifikasi Admin</li>
                        <li>🔒 Pembayaran Resmi via Midtrans</li>
                        <li>🤖 CS AI 24 Jam</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-white/5 mt-8 pt-6 text-center">
                <p class="text-xs text-gray-500">&copy; <?php echo e(date('Y')); ?> KoSeSurabaya. Prototipe Lomba — Seluruh Hak Dilindungi.</p>
            </div>
        </div>
    </footer>

    
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        // CSRF token untuk AJAX requests
        window.CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Feather icons
        document.addEventListener('DOMContentLoaded', () => feather.replace());

        // Auto-dismiss flash messages
        setTimeout(() => {
            document.querySelectorAll('.flash-alert').forEach(el => {
                el.style.transition = 'opacity 0.5s';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            });
        }, 5000);
    </script>

    <?php echo $__env->yieldContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\laragon\www\KoSesurabaya\resources\views/layouts/app.blade.php ENDPATH**/ ?>