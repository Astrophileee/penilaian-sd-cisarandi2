@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-bold">Manajemen Informasi</h1>
        <button onclick="document.getElementById('modal-tambah-information').classList.remove('hidden')" class="bg-black text-white px-4 py-2 rounded-md shadow hover:bg-gray-800">
            Tambah
        </button>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-x-auto">
        <table id="informationTable" class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">No</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Judul</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Isi</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">File</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($informations as $information)
                    <tr>
                        <td class="whitespace-nowrap text-gray-700">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $information->judul }}</td>
                        <td class="px-6 py-4 text-gray-700">
                            {{ \Illuminate\Support\Str::limit($information->isi, 80) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                            @if ($information->file_path)
                                <a href="{{ asset('storage/' . $information->file_path) }}" target="_blank" rel="noopener" class="text-blue-600 hover:text-blue-800">
                                    Lihat PDF
                                </a>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <button
                                    type="button"
                                    class="text-blue-600 hover:text-blue-900 border border-blue-600 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200"
                                    onclick='openEditModal(@json($information))'>
                                    Edit
                                </button>

                                <form id="deleteForm{{ $information->id }}" action="{{ route('information.destroy', $information) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="button"
                                        onclick="confirmDelete('{{ $information->id }}')"
                                        class="text-red-600 hover:text-red-900 border border-red-600 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada informasi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Tambah -->
<div id="modal-tambah-information" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 hidden">
    <div class="min-h-screen flex items-center justify-center py-6 px-4">
        <div class="bg-white w-full max-w-md mx-auto rounded-lg shadow-lg p-6 relative">
            <button onclick="document.getElementById('modal-tambah-information').classList.add('hidden')" class="absolute top-4 right-4 text-xl font-bold text-gray-600 hover:text-gray-800">&times;</button>

            <h2 class="text-lg font-semibold mb-4">Tambah Informasi</h2>

                <form action="{{ route('information.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Judul *</label>
                        <input type="text" name="judul" value="{{ old('judul') }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm">
                        @error('judul')
                            <div class="text-red-500 text-xs mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Isi *</label>
                        <textarea name="isi" required class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm" rows="4">{{ old('isi') }}</textarea>
                        @error('isi')
                            <div class="text-red-500 text-xs mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">File (PDF, opsional)</label>
                        <input type="file" name="file" accept="application/pdf" class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm">
                        @error('file')
                            <div class="text-red-500 text-xs mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="resetForm(); document.getElementById('modal-tambah-information').classList.add('hidden')" class="px-4 py-2 rounded-md border text-sm">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-black text-white rounded-md text-sm hover:bg-gray-800">Simpan</button>
                    </div>
                </form>

        </div>
    </div>
</div>

<!-- Modal Edit -->
<div id="modal-edit-information" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 hidden">
    <div class="min-h-screen flex items-center justify-center py-6 px-4">
        <div class="bg-white w-full max-w-md mx-auto rounded-lg shadow-lg p-6 relative">
            <button onclick="document.getElementById('modal-edit-information').classList.add('hidden')" class="absolute top-4 right-4 text-xl font-bold text-gray-600 hover:text-gray-800">&times;</button>
            <h2 class="text-lg font-semibold mb-4">Edit Informasi</h2>

            <form id="editInformationForm" method="POST" enctype="multipart/form-data"
            action="{{ route('information.update', ['information' => '__ID__']) }}">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-sm font-medium text-gray-700">Judul *</label>
                    <input type="text" name="judul" id="editJudul" value="{{ old('judul') }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm">
                    @error('judul')
                        <div class="text-red-500 text-xs mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Isi *</label>
                    <textarea name="isi" id="editIsi" required class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm" rows="4">{{ old('isi') }}</textarea>
                    @error('isi')
                        <div class="text-red-500 text-xs mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div id="editFileWrapper" class="text-sm text-gray-600 hidden">
                    File saat ini:
                    <a id="editFileLink" href="#" target="_blank" rel="noopener" class="text-blue-600 hover:text-blue-800">Lihat PDF</a>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Ganti File (PDF, opsional)</label>
                    <input type="file" name="file" id="editFile" accept="application/pdf" class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm">
                    @error('file')
                        <div class="text-red-500 text-xs mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="resetForm(); document.getElementById('modal-edit-information').classList.add('hidden')" class="px-4 py-2 rounded-md border text-sm">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-black text-white rounded-md text-sm hover:bg-gray-800">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>


@vite(['resources/js/app.js'])
<script>
    const storageBase = @json(asset('storage'));

    function openEditModal(information) {
        const modal = document.getElementById('modal-edit-information');
        modal.classList.remove('hidden');

        const form = document.getElementById('editInformationForm');
        const baseAction = form.dataset.baseAction ?? form.action;
        form.dataset.baseAction = baseAction;
        form.action = baseAction.replace('__ID__', information.id);

        document.getElementById('editJudul').value = information.judul ?? '';
        document.getElementById('editIsi').value = information.isi ?? '';
        document.getElementById('editFile').value = '';

        const fileWrapper = document.getElementById('editFileWrapper');
        const fileLink = document.getElementById('editFileLink');

        if (information.file_path) {
            fileWrapper.classList.remove('hidden');
            fileLink.href = storageBase + '/' + information.file_path;
        } else {
            fileWrapper.classList.add('hidden');
            fileLink.removeAttribute('href');
        }
    }

function confirmDelete(informationId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deleteForm' + informationId).submit();
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    @if($errors->any())
        document.getElementById('modal-tambah-information').classList.remove('hidden');
    @endif
});

function resetForm() {
    document.querySelectorAll('#modal-tambah-information form, #modal-edit-information form').forEach(form => form.reset());
}

document.querySelector('#modal-tambah-information .absolute').addEventListener('click', function() {
    resetForm();
    document.getElementById('modal-tambah-information').classList.add('hidden');
});

</script>

@if (session('success') || session('error'))
    <div id="flash-message"
        data-type="{{ session('success') ? 'success' : 'error' }}"
        data-message="{{ session('success') ?? session('error') }}">
    </div>
@endif

@endsection
