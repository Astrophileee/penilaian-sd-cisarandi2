@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-bold">Manajemen Semester</h1>
        <button
            onclick="document.getElementById('modal-create-semester').classList.remove('hidden')"
            class="bg-black text-white px-4 py-2 rounded-md shadow hover:bg-gray-800 text-sm">
            Semester Baru
        </button>
    </div>

    {{-- Card Semester Aktif --}}
    <div class="bg-white shadow rounded-lg p-4 mb-6">
        <h2 class="text-lg font-semibold mb-2">Semester Aktif</h2>
        @if($currentSemester)
            <p class="text-gray-800">
                Tahun Ajaran: <span class="font-semibold">{{ $currentSemester->tahun_ajaran }}</span><br>
                Semester: <span class="font-semibold">{{ $currentSemester->semester_ke }}</span>
            </p>
        @else
            <p class="text-gray-500 italic">Belum ada semester aktif.</p>
        @endif
    </div>

    {{-- Tabel Semua Semester --}}
    <div class="bg-white shadow-md rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">No</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Tahun Ajaran</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Semester</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($semesters as $semester)
                    <tr>
                        <td class="px-4 py-2">{{ $loop->iteration }}</td>
                        <td class="px-4 py-2">{{ $semester->tahun_ajaran }}</td>
                        <td class="px-4 py-2">{{ $semester->semester_ke }}</td>
                        <td class="px-4 py-2">
                            @if($semester->is_active)
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Aktif</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-right">
                            @if(!$semester->is_active)
                                <form method="POST" action="{{ route('semesters.activate', $semester) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button
                                        class="text-xs border border-blue-600 text-blue-700 px-3 py-1 rounded hover:bg-blue-50">
                                        Jadikan Aktif
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-gray-400">Sedang aktif</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-3 text-center text-gray-500">
                            Belum ada data semester.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Semester Baru --}}
<div id="modal-create-semester" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 hidden">
    <div class="min-h-screen flex items-center justify-center py-6 px-4">
        <div class="bg-white w-full max-w-md mx-auto rounded-lg shadow-lg p-6 relative">
            <button onclick="document.getElementById('modal-create-semester').classList.add('hidden')"
                    class="absolute top-4 right-4 text-xl font-bold text-gray-600 hover:text-gray-800">
                &times;
            </button>

            <h2 class="text-lg font-semibold mb-4">Buat Semester Baru</h2>

            <form method="POST" action="{{ route('semesters.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Tahun Ajaran *</label>
                    <input type="text" name="tahun_ajaran"
                           placeholder="contoh: 2024/2025"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm"
                           required>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Semester *</label>
                    <select name="semester_ke"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm"
                            required>
                        <option value="">Pilih Semester</option>
                        <option value="1">Semester 1</option>
                        <option value="2">Semester 2</option>
                    </select>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button"
                            onclick="document.getElementById('modal-create-semester').classList.add('hidden')"
                            class="px-4 py-2 rounded-md border text-sm">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-black text-white rounded-md text-sm hover:bg-gray-800">
                        Simpan & Aktifkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if (session('success') || session('error'))
    <div id="flash-message"
        data-type="{{ session('success') ? 'success' : 'error' }}"
        data-message="{{ session('success') ?? session('error') }}">
    </div>
@endif
@endsection
