<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Multiuser — Komunikasi Tim yang Lebih Cerdas</title>
    @vite(['resources/css/app.css'])
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        .fade-up {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.6s cubic-bezier(0.2, 0, 0, 1), transform 0.6s cubic-bezier(0.2, 0, 0, 1);
        }
        .fade-up.visible {
            opacity: 1;
            transform: translateY(0);
        }
        .scale-in {
            opacity: 0;
            transform: scale(0.92);
            transition: opacity 0.7s cubic-bezier(0.2, 0, 0, 1), transform 0.7s cubic-bezier(0.2, 0, 0, 1);
        }
        .scale-in.visible {
            opacity: 1;
            transform: scale(1);
        }
        .btn-press {
            transition: transform 0.15s cubic-bezier(0.2, 0, 0, 1);
        }
        .btn-press:active {
            transform: scale(0.97);
        }
        .hero-child {
            opacity: 0;
            transform: translateY(20px);
            animation: heroFadeIn 0.6s cubic-bezier(0.2, 0, 0, 1) forwards;
        }
        .hero-child:nth-child(1) { animation-delay: 0ms; }
        .hero-child:nth-child(2) { animation-delay: 100ms; }
        .hero-child:nth-child(3) { animation-delay: 200ms; }
        .hero-child:nth-child(4) { animation-delay: 300ms; }
        @keyframes heroFadeIn {
            to { opacity: 1; transform: translateY(0); }
        }
        .play-btn {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.08); opacity: 0.85; }
        }
    </style>
</head>
<body class="bg-white text-gray-900 antialiased">

    <!-- Hero -->
    <section class="min-h-screen flex items-center justify-center px-6 bg-gradient-to-br from-emerald-50 to-teal-50">
        <div class="max-w-4xl mx-auto text-center">
            <p class="hero-child text-emerald-600 font-semibold text-sm uppercase tracking-wider mb-4">Platform Komunikasi Tim #1</p>
            <h1 class="hero-child text-4xl md:text-6xl font-bold leading-tight bg-gradient-to-r from-emerald-500 to-teal-600 bg-clip-text text-transparent">
                Chat Multiuser — Komunikasi Tim yang Lebih Cerdas
            </h1>
            <p class="hero-child mt-6 text-lg md:text-xl text-gray-600 max-w-2xl mx-auto">
                Kolaborasi realtime dengan AI assistant bawaan. Kelola tim, pantau aktivitas, dan tingkatkan produktivitas dalam satu platform.
            </p>
            <div class="hero-child mt-10 flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/blade/register" class="btn-press inline-block px-8 py-4 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-semibold rounded-xl shadow-lg shadow-emerald-200 hover:shadow-xl hover:shadow-emerald-300 transition-shadow">
                    Mulai Gratis
                </a>
                <a href="/blade/login" class="btn-press inline-block px-8 py-4 border-2 border-emerald-500 text-emerald-600 font-semibold rounded-xl hover:bg-emerald-50 transition-colors">
                    Login
                </a>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="py-24 px-6 bg-white">
        <div class="max-w-6xl mx-auto">
            <h2 class="fade-up text-3xl md:text-4xl font-bold text-center mb-4">Fitur Unggulan</h2>
            <p class="fade-up text-gray-600 text-center mb-16 max-w-2xl mx-auto">Semua yang tim Anda butuhkan untuk berkomunikasi lebih efektif.</p>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="fade-up p-6 rounded-2xl bg-gray-50 border border-gray-100 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </div>
                    <h3 class="font-bold text-lg mb-2">Realtime Chat</h3>
                    <p class="text-gray-600 text-sm">Pesan instan dengan WebSocket. Tidak ada delay, langsung terkirim.</p>
                </div>
                <div class="fade-up p-6 rounded-2xl bg-gray-50 border border-gray-100 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    </div>
                    <h3 class="font-bold text-lg mb-2">AI Assistant</h3>
                    <p class="text-gray-600 text-sm">Asisten AI bawaan membantu menjawab pertanyaan dan meringkas percakapan.</p>
                </div>
                <div class="fade-up p-6 rounded-2xl bg-gray-50 border border-gray-100 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="font-bold text-lg mb-2">Multi Platform</h3>
                    <p class="text-gray-600 text-sm">Akses dari desktop, tablet, atau smartphone. Responsive di semua perangkat.</p>
                </div>
                <div class="fade-up p-6 rounded-2xl bg-gray-50 border border-gray-100 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3 class="font-bold text-lg mb-2">Admin Panel</h3>
                    <p class="text-gray-600 text-sm">Kelola user, monitor aktivitas, dan atur permission dengan mudah.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Video Demo -->
    <section class="py-24 px-6 bg-gray-50">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="fade-up text-3xl md:text-4xl font-bold mb-4">Lihat Cara Kerjanya</h2>
            <p class="fade-up text-gray-600 mb-12">Tonton demo singkat platform kami dalam aksi.</p>
            <div class="scale-in relative rounded-2xl overflow-hidden aspect-video bg-gradient-to-br from-emerald-500 to-teal-700 shadow-2xl shadow-emerald-200 cursor-pointer group">
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <div class="play-btn w-20 h-20 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center border-2 border-white/40 group-hover:bg-white/30 transition-colors">
                        <svg class="w-8 h-8 text-white ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </div>
                    <p class="mt-4 text-white/90 font-semibold text-lg">Lihat Demo</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-24 px-6 bg-white">
        <div class="max-w-6xl mx-auto">
            <h2 class="fade-up text-3xl md:text-4xl font-bold text-center mb-16">Dipercaya oleh Tim Terbaik</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="fade-up p-8 rounded-2xl bg-gray-50 border border-gray-100">
                    <p class="text-gray-600 mb-6">"Chat Multiuser mengubah cara tim kami berkolaborasi. Fitur AI assistant sangat membantu meringkas meeting notes."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500"></div>
                        <div>
                            <p class="font-semibold text-sm">Rina Susanti</p>
                            <p class="text-gray-500 text-xs">Product Manager, TechCorp</p>
                        </div>
                    </div>
                </div>
                <div class="fade-up p-8 rounded-2xl bg-gray-50 border border-gray-100">
                    <p class="text-gray-600 mb-6">"Admin panel-nya lengkap banget. Bisa monitor semua aktivitas tim dan atur akses dengan mudah."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500"></div>
                        <div>
                            <p class="font-semibold text-sm">Budi Hartono</p>
                            <p class="text-gray-500 text-xs">CTO, StartupXYZ</p>
                        </div>
                    </div>
                </div>
                <div class="fade-up p-8 rounded-2xl bg-gray-50 border border-gray-100">
                    <p class="text-gray-600 mb-6">"Realtime chat-nya super cepat. Tidak ada lag sama sekali, bahkan saat banyak user online bersamaan."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500"></div>
                        <div>
                            <p class="font-semibold text-sm">Dewi Anggraini</p>
                            <p class="text-gray-500 text-xs">Engineering Lead, DataFlow</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer CTA -->
    <section class="py-24 px-6 bg-gradient-to-br from-emerald-500 to-teal-600">
        <div class="max-w-3xl mx-auto text-center">
            <h2 class="fade-up text-3xl md:text-4xl font-bold text-white mb-4">Mulai Sekarang — Gratis!</h2>
            <p class="fade-up text-emerald-100 text-lg mb-10">Bergabung dengan ratusan tim yang sudah menggunakan Chat Multiuser.</p>
            <a href="/blade/register" class="fade-up btn-press inline-block px-10 py-4 bg-white text-emerald-600 font-bold rounded-xl shadow-lg hover:shadow-xl transition-shadow text-lg">
                Daftar Sekarang
            </a>
        </div>
    </section>

    <script>
        $(function() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry, i) => {
                    if (entry.isIntersecting) {
                        const el = $(entry.target);
                        const delay = el.index() * 150;
                        setTimeout(() => {
                            el.addClass('visible');
                        }, Math.min(delay, 600));
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.15 });

            $('.fade-up, .scale-in').each(function() {
                observer.observe(this);
            });
        });
    </script>
</body>
</html>
