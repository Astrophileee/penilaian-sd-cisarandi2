@extends('layouts.public')

@section('title', 'Informasi Sekolah')

@section('content')
<style>
    summary.info-summary::-webkit-details-marker {
        display: none;
    }
</style>
<div class="max-w-6xl mx-auto space-y-10">
    <section class="relative overflow-hidden rounded-[32px] bg-white shadow-sm border border-black/5 p-8 md:p-10">
        <div class="absolute -top-20 -right-10 h-64 w-64 rounded-full opacity-70 blur-3xl" style="background: radial-gradient(circle, var(--accent-3) 0%, transparent 70%);"></div>
        <div class="relative z-10 space-y-4 fade-up">
            <span class="inline-flex items-center gap-2 rounded-full bg-accent-soft px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-[var(--accent-2)]">Informasi</span>
            <h1 class="text-3xl sm:text-4xl font-semibold text-[var(--ink)]">Pengumuman dan Jadwal Terbaru</h1>
            <p class="text-sm sm:text-base text-[var(--muted)] max-w-2xl">
                Temukan informasi kegiatan sekolah, jadwal pembelajaran, dan pengumuman penting lainnya untuk siswa dan wali.
            </p>
            <p class="text-xs uppercase tracking-[0.2em] text-[var(--muted)]">Klik judul untuk melihat detail.</p>
        </div>
    </section>

    <section class="grid gap-4">
        @forelse ($informations as $information)
            <details class="group rounded-3xl bg-white p-6 shadow-sm border border-black/5 fade-up">
                <summary class="info-summary cursor-pointer list-none">
                    <h2 class="text-xl font-semibold text-[var(--ink)]">{{ $information->judul }}</h2>
                    <p class="text-xs uppercase tracking-[0.2em] text-[var(--muted)] mt-1">
                        {{ $information->created_at?->format('d M Y') }}
                    </p>
                </summary>
                <div class="mt-4 border-t border-black/5 pt-4 space-y-4">
                    <p class="text-sm sm:text-base text-[var(--muted)] whitespace-pre-line">{{ $information->isi }}</p>
                    @if ($information->file_path)
                        <div class="flex flex-wrap items-center gap-3">
                            <a href="{{ asset('storage/' . $information->file_path) }}" download class="px-4 py-2 rounded-full bg-[var(--accent)] text-white text-xs font-semibold shadow hover:opacity-90 transition">
                                Unduh PDF
                            </a>
                            <a href="{{ asset('storage/' . $information->file_path) }}" target="_blank" rel="noopener" class="px-4 py-2 rounded-full border border-[var(--accent-2)] text-[var(--accent-2)] text-xs font-semibold hover:bg-[var(--accent-2)] hover:text-white transition">
                                Buka File
                            </a>
                        </div>
                    @endif
                </div>
            </details>
        @empty
            <div class="rounded-3xl bg-white p-8 text-center text-[var(--muted)] border border-dashed border-black/10">
                Belum ada informasi yang dipublikasikan.
            </div>
        @endforelse
    </section>
</div>
@endsection
