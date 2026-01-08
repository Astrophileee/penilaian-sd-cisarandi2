@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-bold">Daftar Guru</h1>
        <a href="{{ route('assignments.index') }}" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100">
            <button class="bg-black text-white px-4 py-2 rounded-md shadow hover:bg-gray-800">
                Penugasan Guru Mata Pelajaran
            </button>
        </a>
        <button onclick="document.getElementById('modal-tambah-teacher').classList.remove('hidden')" class="bg-black text-white px-4 py-2 rounded-md shadow hover:bg-gray-800">
            Tambah
        </button>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-x-auto">
        <table id="teachersTable" class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">No</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">NIP</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Alamat</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">No HP</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($teachers as $teacher)
                    <tr>
                        <td class="whitespace-nowrap text-gray-700">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $teacher->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $teacher->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $teacher->teacher->nip ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $teacher->teacher->alamat }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $teacher->teacher->no_hp }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $teacher->teacher->status }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <!-- Tombol Edit -->
                                <button
                                    type="button"
                                    class="text-blue-600 hover:text-blue-900 border border-blue-600 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200"
                                    onclick='openEditModal(@json($teacher))'>
                                    Edit
                                </button>

                                <!-- Tombol Delete -->
                                <form id="deleteForm{{ $teacher->teacher->id }}" action="{{ route('teachers.destroy', $teacher->teacher) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="button"
                                        onclick="confirmDelete('{{ $teacher->teacher->id }}')"
                                        class="text-red-600 hover:text-red-900 border border-red-600 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>


    <!-- Modal Tambah -->
<div id="modal-tambah-teacher" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 hidden">
    <div class="min-h-screen flex items-center justify-center py-6 px-4">
        <div class="bg-white w-full max-w-md mx-auto rounded-lg shadow-lg p-6 relative">
            <!-- Close button -->
            <button onclick="document.getElementById('modal-tambah-teacher').classList.add('hidden')" class="absolute top-4 right-4 text-xl font-bold text-gray-600 hover:text-gray-800">&times;</button>

            <h2 class="text-lg font-semibold mb-4">Tambah Guru</h2>

                <form action="{{ route('teachers.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <!-- Nama -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm">
                        @error('name')
                            <div class="text-red-500 text-xs mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm">
                        @error('email')
                            <div class="text-red-500 text-xs mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- NIP -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">NIP</label>
                        <input type="number" name="nip" value="{{ old('nip') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm">
                        @error('nip')
                            <div class="text-red-500 text-xs mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- NO HP -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">No HP *</label>
                        <input type="number" name="no_hp" value="{{ old('no_hp') }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm">
                        @error('no_hp')
                            <div class="text-red-500 text-xs mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Alamat -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Alamat *</label>
                        <textarea name="alamat" required class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm">{{ old('alamat') }}</textarea>
                        @error('alamat')
                            <div class="text-red-500 text-xs mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status *</label>
                            <div>
                                <select name="status"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm" required>
                                    <option value="" selected>Pilih Status</option>
                                    <option value="aktif">Aktif</option>
                                    <option value="tidak aktif">Tidak Aktif</option>
                                </select>
                            </div>
                        @error('status')
                            <div class="text-red-500 text-xs mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Action -->
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="resetForm(); document.getElementById('modal-tambah-teacher').classList.add('hidden')" class="px-4 py-2 rounded-md border text-sm">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-black text-white rounded-md text-sm hover:bg-gray-800">Simpan</button>
                    </div>
                </form>

        </div>
    </div>
</div>

<!-- Modal Edit -->
<div id="modal-edit-teacher" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 hidden">
    <div class="min-h-screen flex items-center justify-center py-6 px-4">
        <div class="bg-white w-full max-w-md mx-auto rounded-lg shadow-lg p-6 relative">
            <button onclick="document.getElementById('modal-edit-teacher').classList.add('hidden')" class="absolute top-4 right-4 text-xl font-bold text-gray-600 hover:text-gray-800">&times;</button>
            <h2 class="text-lg font-semibold mb-4">Edit Guru</h2>

            <form id="editTeacherForm" method="POST" enctype="multipart/form-data"
            action="{{ route('teachers.update', ['teacher' => '__ID__']) }}">
                @csrf
                @method('PATCH')

                <!-- Nama -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama *</label>
                    <input type="text" name="name" id="editName" value="{{ old('name') }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm">
                    @error('name')
                        <div class="text-red-500 text-xs mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email *</label>
                    <input type="email" name="email" id="editEmail" value="{{ old('email') }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm">
                    @error('email')
                        <div class="text-red-500 text-xs mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <!-- NIP -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">NIP</label>
                        <input type="number" id="editNip" name="nip" value="{{ old('nip') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm">
                        @error('nip')
                            <div class="text-red-500 text-xs mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- NO HP -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">No HP *</label>
                        <input type="number" id="editNoHp" name="no_hp" value="{{ old('no_hp') }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm">
                        @error('no_hp')
                            <div class="text-red-500 text-xs mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Alamat -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Alamat *</label>
                        <textarea name="alamat" id="editAlamat" required class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm">{{ old('alamat') }}</textarea>
                        @error('alamat')
                            <div class="text-red-500 text-xs mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status *</label>
                            <div>
                                <select name="status" id="editStatus"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm" required>
                                    <option value="" selected>Pilih Status</option>
                                    <option value="aktif">Aktif</option>
                                    <option value="tidak aktif">Tidak Aktif</option>
                                </select>
                            </div>
                        @error('status')
                            <div class="text-red-500 text-xs mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Password Baru (Opsional)</label>
                    <input type="password" name="password" id="editPassword" class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm" placeholder="Kosongkan jika tidak ingin mengubah">
                    @error('password')
                        <div class="text-red-500 text-xs mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="resetForm(); document.getElementById('modal-edit-teacher').classList.add('hidden')" class="px-4 py-2 rounded-md border text-sm">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-black text-white rounded-md text-sm hover:bg-gray-800">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>


@vite(['resources/js/app.js'])
<script>
    function openEditModal(teacher) {
        const modal = document.getElementById('modal-edit-teacher');
        modal.classList.remove('hidden');

        const form = document.getElementById('editTeacherForm');
        form.action = form.action.replace('__ID__', teacher.teacher.id);
        document.getElementById('editName').value = teacher.name ?? '';
        document.getElementById('editEmail').value = teacher.email ?? '';
        document.getElementById('editNip').value = teacher.teacher.nip ?? '';
        document.getElementById('editAlamat').value = teacher.teacher.alamat ?? '';
        document.getElementById('editNoHp').value = teacher.teacher.no_hp ?? '';
        document.getElementById('editStatus').value = teacher.teacher.status ?? '';
    }

function confirmDelete(teacherId) {
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
            document.getElementById('deleteForm' + teacherId).submit();
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    @if($errors->any())
        document.getElementById('modal-tambah-teacher').classList.remove('hidden');
    @endif
});

function resetForm() {
    const form = document.querySelector('#modal-tambah-teacher form');
    form.reset();
}

document.querySelector('#modal-tambah-teacher .absolute').addEventListener('click', function() {
    resetForm();
    document.getElementById('modal-tambah-teacher').classList.add('hidden');
});



</script>

@if (session('success') || session('error'))
    <div id="flash-message"
        data-type="{{ session('success') ? 'success' : 'error' }}"
        data-message="{{ session('success') ?? session('error') }}">
    </div>
@endif

@if(session('editTeacher'))
    <script>
        window.onload = function() {
            openEditModal(@json(session('editTeacher')));
        }
    </script>
@endif



@endsection
