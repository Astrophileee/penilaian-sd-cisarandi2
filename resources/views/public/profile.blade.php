@extends('layouts.public')

@section('title', 'Profil Sekolah')

@section('content')
<div class="max-w-6xl mx-auto space-y-12">
    <section class="relative overflow-hidden rounded-[32px] bg-white shadow-xl ring-1 ring-black/5 p-8 md:p-12">
        <div class="absolute -top-24 -right-10 h-72 w-72 rounded-full opacity-70 blur-3xl" style="background: radial-gradient(circle, var(--accent) 0%, transparent 70%);"></div>
        <div class="absolute -bottom-24 -left-16 h-80 w-80 rounded-full opacity-60 blur-3xl" style="background: radial-gradient(circle, var(--accent-3) 0%, transparent 70%);"></div>

        <div class="relative z-10 grid gap-10 md:grid-cols-[1.2fr_0.8fr] items-center">
            <div class="space-y-6 fade-up">
                <span class="inline-flex items-center gap-2 rounded-full bg-accent-soft px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-[var(--accent-2)]">Profil Sekolah</span>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-semibold text-[var(--ink)]">
                    SD Cisarandi 2
                </h1>
                <p class="text-base sm:text-lg text-[var(--muted)] max-w-xl">
                    Sekolah dasar yang menumbuhkan karakter, kecakapan hidup, dan rasa ingin tahu melalui pembelajaran aktif, kolaboratif, dan berbasis proyek.
                </p>
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('public.information') }}" class="px-5 py-3 rounded-full bg-[var(--accent)] text-white text-sm font-semibold shadow hover:opacity-90 transition">Lihat Informasi</a>
                    <a href="{{ route('login') }}" class="px-5 py-3 rounded-full border border-[var(--accent-2)] text-[var(--accent-2)] text-sm font-semibold hover:bg-[var(--accent-2)] hover:text-white transition">Masuk Portal</a>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-4">
                    <div class="rounded-2xl bg-white/80 border border-black/5 p-4 text-center">
                        <p class="text-2xl font-semibold text-[var(--accent-2)]">8</p>
                        <p class="text-xs uppercase tracking-wider text-[var(--muted)]">Guru</p>
                    </div>
                    <div class="rounded-2xl bg-white/80 border border-black/5 p-4 text-center">
                        <p class="text-2xl font-semibold text-[var(--accent-2)]">2</p>
                        <p class="text-xs uppercase tracking-wider text-[var(--muted)]">Staff</p>
                    </div>
                    <div class="rounded-2xl bg-white/80 border border-black/5 p-4 text-center">
                        <p class="text-2xl font-semibold text-[var(--accent-2)]">208</p>
                        <p class="text-xs uppercase tracking-wider text-[var(--muted)]">Siswa</p>
                    </div>
                    <div class="rounded-2xl bg-white/80 border border-black/5 p-4 text-center">
                        <p class="text-2xl font-semibold text-[var(--accent-2)]">6</p>
                        <p class="text-xs uppercase tracking-wider text-[var(--muted)]">Kelas</p>
                    </div>
                </div>
            </div>

            <div class="relative fade-up delay-2">
                <div class="absolute -top-4 -right-4 h-12 w-12 rounded-2xl bg-accent-soft-2"></div>
                <div class="absolute -bottom-6 left-6 h-16 w-16 rounded-3xl bg-accent-3-soft"></div>
                <div class="rounded-3xl bg-accent-2-soft p-6 border border-black/5">
                    <div class="rounded-3xl bg-white p-6 shadow-sm">
                        <p class="text-xs uppercase tracking-[0.2em] text-[var(--muted)]">Fokus Pembelajaran</p>
                        <h3 class="text-2xl font-semibold text-[var(--ink)] mt-3">Literasi, Numerasi, dan Karakter</h3>
                        <p class="mt-4 text-sm text-[var(--muted)]">
                            Kurikulum kami menekankan kemampuan dasar yang kuat, pembiasaan disiplin positif, serta ruang untuk kreativitas dan kepemimpinan siswa.
                        </p>
                        <div class="mt-6 flex items-center gap-3">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-accent-soft-2 text-[var(--accent-2)] font-semibold">K</span>
                            <div>
                                <p class="text-sm font-semibold text-[var(--ink)]">Kurikulum Deep Learning</p>
                                <p class="text-xs text-[var(--muted)]">Terintegrasi dengan budaya lokal.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="grid gap-6 md:grid-cols-3">
        <div class="rounded-3xl bg-white p-6 shadow-sm border border-black/5 fade-up">
            <p class="text-xs uppercase tracking-[0.2em] text-[var(--muted)]">Visi</p>
            <h3 class="text-2xl font-semibold text-[var(--ink)] mt-3">Mewujudkan peserta didik yang cerdas, berkarakter, dan berprestasi dalam suasana belajar yang aktif, menyenangkan, serta berlandaskan nilai-nilai Pancasila.</h3>
        </div>
        <div class="rounded-3xl bg-white p-6 shadow-sm border border-black/5 fade-up delay-1">
            <p class="text-xs uppercase tracking-[0.2em] text-[var(--muted)]">Misi</p>
            <ul class="mt-3 space-y-2 text-sm text-[var(--muted)]">
                <li>Menyelenggarakan pembelajaran yang aktif, kreatif, dan menyenangkan dengan mengedepankan pendekatan tematik dan pembelajaran berbasis proyek.</li>
                <li>Menanamkan nilai-nilai moral dan karakter kepada seluruh peserta didik, seperti kejujuran, tanggung jawab, dan kerja sama, melalui kegiatan intrakurikuler dan ekstrakurikuler.</li>
                <li>Mengembangkan potensi siswa secara optimal, baik di bidang akademik maupun non-akademik, melalui pelatihan, lomba, dan program pengembangan diri.</li>
                <li>Meningkatkan kompetensi pendidik dan tenaga kependidikan, agar mampu menciptakan proses belajar yang berkualitas dan relevan dengan perkembangan teknologi.</li>
            </ul>
        </div>
        <div class="rounded-3xl bg-white p-6 shadow-sm border border-black/5 fade-up delay-2">
            <p class="text-xs uppercase tracking-[0.2em] text-[var(--muted)]">Nilai Inti</p>
            <div class="mt-4 flex flex-wrap gap-2">
                <span class="px-3 py-1 rounded-full bg-accent-soft text-xs font-semibold text-[var(--accent-2)]">Gotong royong</span>
                <span class="px-3 py-1 rounded-full bg-accent-soft text-xs font-semibold text-[var(--accent-2)]">Integritas</span>
                <span class="px-3 py-1 rounded-full bg-accent-soft text-xs font-semibold text-[var(--accent-2)]">Kreativitas</span>
                <span class="px-3 py-1 rounded-full bg-accent-soft text-xs font-semibold text-[var(--accent-2)]">Semangat belajar</span>
            </div>
        </div>
    </section>

    <section class="grid gap-6 md:grid-cols-[0.9fr_1.1fr] items-start">
        <div class="rounded-3xl bg-white p-6 shadow-sm border border-black/5 fade-up">
            <p class="text-xs uppercase tracking-[0.2em] text-[var(--muted)]">Program Unggulan</p>
            <h3 class="text-2xl font-semibold text-[var(--ink)] mt-3">Pembelajaran yang relevan dan menyenangkan.</h3>
            <div class="mt-6 space-y-4 text-sm text-[var(--muted)]">
                <div class="flex items-start gap-3">
                    <span class="h-10 w-10 rounded-2xl bg-accent-soft-2 flex items-center justify-center text-[var(--accent-2)] font-semibold">01</span>
                    <div>
                        <p class="font-semibold text-[var(--ink)]">Kelas Literasi Pagi</p>
                        <p>Rutinitas membaca dan menulis kreatif setiap hari.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <span class="h-10 w-10 rounded-2xl bg-accent-3-soft flex items-center justify-center text-[var(--accent-2)] font-semibold">02</span>
                    <div>
                        <p class="font-semibold text-[var(--ink)]">Proyek Sains Mini</p>
                        <p>Eksperimen sederhana yang melatih rasa ingin tahu.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <span class="h-10 w-10 rounded-2xl bg-accent-2-soft-2 flex items-center justify-center text-[var(--accent-2)] font-semibold">03</span>
                    <div>
                        <p class="font-semibold text-[var(--ink)]">Kelas Karakter</p>
                        <p>Pembiasaan nilai-nilai positif dan kepemimpinan siswa.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 fade-up delay-1">
            <div class="rounded-3xl bg-white p-6 shadow-sm border border-black/5">
                <p class="text-xs uppercase tracking-[0.2em] text-[var(--muted)]">Fasilitas</p>
                <ul class="mt-4 space-y-2 text-sm text-[var(--muted)]">
                    <li>Perpustakaan literasi</li>
                    <li>Laboratorium sains dasar</li>
                    <li>Ruang kreativitas siswa</li>
                    <li>Lapangan olahraga</li>
                </ul>
            </div>
            <div class="rounded-3xl bg-[var(--accent-2)] text-white p-6 shadow-sm">
                <p class="text-xs uppercase tracking-[0.2em] text-white/70">Jam Operasional</p>
                <div class="mt-4 space-y-2 text-sm">
                    <p>Senin - Jumat: 07:00 - 13:30</p>
                    <p>Sabtu: 07:30 - 11:00</p>
                    <p>Ekstrakurikuler: 13:30 - 15:30</p>
                </div>
            </div>
            <div class="rounded-3xl bg-white p-6 shadow-sm border border-black/5 sm:col-span-2">
                <p class="text-xs uppercase tracking-[0.2em] text-[var(--muted)]">Kegiatan Terbaru</p>
                <div class="mt-4 grid gap-3 md:grid-cols-3 text-sm">
                    <div class="rounded-2xl bg-accent-soft p-3">
                        <p class="font-semibold text-[var(--ink)]">Gebyar Literasi</p>
                        <p class="text-[var(--muted)]">Pentas baca puisi dan cerita.</p>
                    </div>
                    <div class="rounded-2xl bg-accent-3-soft p-3">
                        <p class="font-semibold text-[var(--ink)]">Pasar Sains</p>
                        <p class="text-[var(--muted)]">Pameran eksperimen kelas.</p>
                    </div>
                    <div class="rounded-2xl bg-accent-2-soft-2 p-3">
                        <p class="font-semibold text-[var(--ink)]">Project P5</p>
                        <p class="text-[var(--muted)]">Kolaborasi lintas kelas.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-[32px] bg-white p-8 md:p-10 shadow-sm border border-black/5 grid gap-6 md:grid-cols-[1fr_1fr] items-start fade-up">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-[var(--muted)]">Kontak</p>
            <h3 class="text-2xl font-semibold text-[var(--ink)] mt-3">Kami siap berdiskusi dengan orang tua dan wali siswa.</h3>
            <p class="mt-4 text-sm text-[var(--muted)]">Silakan hubungi kami untuk informasi pendaftaran, jadwal pembelajaran, atau konsultasi perkembangan siswa.</p>
        </div>
        <div class="grid gap-4 text-sm text-[var(--muted)]">
            <div class="rounded-2xl bg-accent-soft p-4">
                <p class="font-semibold text-[var(--ink)]">Alamat</p>
                <p>Kp. Lemburtengah, CISARANDI. Kec. Warungkondang.  Kab. Cianjur, Jawa Barat, dengan kode pos 43261.</p>
            </div>
            <div class="rounded-2xl bg-accent-3-soft p-4">
                <p class="font-semibold text-[var(--ink)]">Telepon</p>
                <p>-</p>
            </div>
            <div class="rounded-2xl bg-accent-2-soft-2 p-4">
                <p class="font-semibold text-[var(--ink)]">Email</p>
                <p>-</p>
            </div>
        </div>
    </section>
</div>
@endsection
