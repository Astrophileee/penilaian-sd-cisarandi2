<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Profil Sekolah')</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700&display=swap" rel="stylesheet" />
        <link href="https://fonts.bunny.net/css?family=playfair-display:400,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            :root {
                --paper: #f6f1e9;
                --ink: #1f2937;
                --muted: #5f6b76;
                --accent: #e07a5f;
                --accent-2: #3d405b;
                --accent-3: #81b29a;
            }

            body {
                font-family: "Manrope", sans-serif;
                background-color: var(--paper);
                color: var(--ink);
            }

            h1, h2, h3, h4 {
                font-family: "Playfair Display", serif;
            }

            .fade-up {
                animation: fadeUp 0.7s ease-out both;
            }

            .fade-up.delay-1 {
                animation-delay: 0.08s;
            }

            .fade-up.delay-2 {
                animation-delay: 0.16s;
            }

            .fade-up.delay-3 {
                animation-delay: 0.24s;
            }

            .bg-accent-soft {
                background-color: rgba(224, 122, 95, 0.16);
            }

            .bg-accent-soft-2 {
                background-color: rgba(224, 122, 95, 0.22);
            }

            .bg-accent-2-soft {
                background-color: rgba(61, 64, 91, 0.12);
            }

            .bg-accent-2-soft-2 {
                background-color: rgba(61, 64, 91, 0.18);
            }

            .bg-accent-3-soft {
                background-color: rgba(129, 178, 154, 0.2);
            }

            @keyframes fadeUp {
                from {
                    opacity: 0;
                    transform: translateY(18px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        </style>
    </head>
    <body class="bg-[var(--paper)] text-[var(--ink)]">
        <div class="min-h-screen flex flex-col">
            <header class="sticky top-0 z-40 backdrop-blur bg-[rgba(246,241,233,0.9)] border-b border-black/5">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-10 py-4 flex flex-wrap items-center justify-between gap-4">
                    <a href="{{ url('/') }}" class="flex items-center gap-3">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-[var(--accent)] text-white font-semibold">SD</span>
                        <div>
                            <p class="text-sm uppercase tracking-[0.2em] text-[var(--muted)]">Profil Sekolah</p>
                            <p class="text-lg font-semibold">SD Cisarandi 2</p>
                        </div>
                    </a>
                    <nav class="flex flex-wrap items-center gap-3 text-sm font-medium">
                        <a href="{{ route('public.profile') }}" class="px-3 py-2 rounded-full hover:bg-white/70 transition">Profil</a>
                        <a href="{{ route('public.information') }}" class="px-3 py-2 rounded-full hover:bg-white/70 transition">Informasi</a>
                        <a href="{{ route('login') }}" class="px-4 py-2 rounded-full bg-[var(--accent-2)] text-white hover:opacity-90 transition">Masuk</a>
                    </nav>
                </div>
            </header>

            <main class="flex-1 px-4 sm:px-6 lg:px-10 py-10">
                @yield('content')
            </main>

            <footer class="border-t border-black/10">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-10 py-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3 text-sm text-[var(--muted)]">
                    <p>SD Cisarandi 2 - Bersama tumbuh, berkarakter, dan berprestasi.</p>
                    <p>Jl. Cisarandi No. 2, Sumedang - Jawa Barat</p>
                </div>
            </footer>
        </div>
    </body>
</html>
