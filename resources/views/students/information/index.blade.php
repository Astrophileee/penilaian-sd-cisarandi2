@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-4">
    <h1 class="text-xl font-bold">Informasi</h1>
</div>

<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <div class="divide-y divide-gray-200">
        @forelse ($informations as $information)
            <div class="p-6">
                <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800">{{ $information->judul }}</h2>
                        <p class="text-xs text-gray-500">
                            {{ $information->created_at?->format('d M Y') }}
                        </p>
                    </div>
                    @if ($information->file_path)
                        <a href="{{ asset('storage/' . $information->file_path) }}" target="_blank" rel="noopener" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
                            Unduh PDF
                        </a>
                    @endif
                </div>
                <p class="mt-3 text-gray-700 whitespace-pre-line">{{ $information->isi }}</p>
            </div>
        @empty
            <div class="p-6 text-center text-gray-500">
                Belum ada informasi.
            </div>
        @endforelse
    </div>
</div>
@endsection
