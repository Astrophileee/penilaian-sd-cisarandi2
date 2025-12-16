@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-bold">Daftar Murid</h1>
        <button onclick="document.getElementById('modal-tambah-student').classList.remove('hidden')" class="bg-black text-white px-4 py-2 rounded-md shadow hover:bg-gray-800">
            Tambah
        </button>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-x-auto">
        <table id="studentsTable" class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">No</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kelas</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Alamat</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal Lahir</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nama Wali</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">No HP Wali</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($students as $student)
                    <tr>
                        <td class="whitespace-nowrap text-gray-700">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $student->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $student->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $student->student->classroom->nama }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $student->student->alamat }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $student->student->tanggal_lahir }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $student->student->nama_wali }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $student->student->no_hp_wali }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $student->student->status }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <!-- Tombol Edit -->
                                <button
                                    type="button"
                                    class="text-blue-600 hover:text-blue-900 border border-blue-600 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200"
                                    onclick='openEditModal(@json($student))'>
                                    Edit
                                </button>

                                <!-- Tombol Delete -->
                                <form id="deleteForm{{ $student->student->id }}" action="{{ route('students.destroy', $student->student) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="button"
                                        onclick="confirmDelete('{{ $student->student->id }}')"
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
<div id="modal-tambah-student" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 hidden">
    <div class="min-h-screen flex items-center justify-center py-6 px-4">
        <div class="bg-white w-full max-w-md mx-auto rounded-lg shadow-lg p-6 relative">
            <!-- Close button -->
            <button onclick="document.getElementById('modal-tambah-student').classList.add('hidden')" class="absolute top-4 right-4 text-xl font-bold text-gray-600 hover:text-gray-800">&times;</button>

            <h2 class="text-lg font-semibold mb-4">Tambah Murid</h2>

                <form action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
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

                    <!-- Kelas -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kelas *</label>
                        <select name="kelas_id" class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm" required>
                            <option value="" selected>Pilih Kelas</option>
                            @foreach ($classrooms as $classroom)
                                <option value="{{ $classroom->id }}" {{ old('kelas_id') == $classroom->id ? 'selected' : '' }}>
                                    {{ 'Kelas ' . $classroom->nama}}
                                </option>
                            @endforeach
                        </select>
                        @error('kelas_id')
                            <div class="text-red-500 text-xs mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Tanggal Lahir -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tanggal Lahir *</label>
                        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm">
                        @error('tanggal_lahir')
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

                    <!-- Nama Wali -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Wali *</label>
                        <input type="text" name="nama_wali" value="{{ old('nama_wali') }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm">
                        @error('nama_wali')
                            <div class="text-red-500 text-xs mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- No HP Wali -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">No HP Wali *</label>
                        <input type="tel" name="no_hp_wali" value="{{ old('no_hp_wali') }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm">
                        @error('no_hp_wali')
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
                        <button type="button" onclick="resetForm(); document.getElementById('modal-tambah-student').classList.add('hidden')" class="px-4 py-2 rounded-md border text-sm">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-black text-white rounded-md text-sm hover:bg-gray-800">Simpan</button>
                    </div>
                </form>

        </div>
    </div>
</div>

<!-- Modal Edit -->
<div id="modal-edit-student" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 hidden">
    <div class="min-h-screen flex items-center justify-center py-6 px-4">
        <div class="bg-white w-full max-w-md mx-auto rounded-lg shadow-lg p-6 relative">
            <button onclick="document.getElementById('modal-edit-student').classList.add('hidden')" class="absolute top-4 right-4 text-xl font-bold text-gray-600 hover:text-gray-800">&times;</button>
            <h2 class="text-lg font-semibold mb-4">Edit Murid</h2>

            <form id="editStudentForm" method="POST" enctype="multipart/form-data"
            action="{{ route('students.update', ['student' => '__ID__']) }}">
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

                <!-- Kelas -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Kelas *</label>
                    <select name="kelas_id" id="editKelasId" class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm" required>
                        <option value="" selected>Pilih Kelas</option>
                        @foreach ($classrooms as $classroom)
                            <option value="{{ $classroom->id }}">{{ 'Kelas ' . $classroom->nama}}</option>
                        @endforeach
                    </select>
                    @error('kelas_id')
                        <div class="text-red-500 text-xs mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Tanggal Lahir -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Lahir *</label>
                    <input type="date" id="editTanggalLahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm">
                    @error('tanggal_lahir')
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

                <!-- Nama Wali -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Wali *</label>
                    <input type="text" id="editNamaWali" name="nama_wali" value="{{ old('nama_wali') }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm">
                    @error('nama_wali')
                        <div class="text-red-500 text-xs mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <!-- No HP Wali -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">No HP Wali *</label>
                    <input type="tel" id="editNoHpWali" name="no_hp_wali" value="{{ old('no_hp_wali') }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm">
                    @error('no_hp_wali')
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
                    <button type="button" onclick="resetForm(); document.getElementById('modal-edit-student').classList.add('hidden')" class="px-4 py-2 rounded-md border text-sm">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-black text-white rounded-md text-sm hover:bg-gray-800">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>


@vite(['resources/js/app.js'])
<script>
    function openEditModal(student) {
        const modal = document.getElementById('modal-edit-student');
        modal.classList.remove('hidden');

        const form = document.getElementById('editStudentForm');
        const baseAction = form.dataset.baseAction ?? form.action;
        form.dataset.baseAction = baseAction;
        form.action = baseAction.replace('__ID__', student.student.id);
        document.getElementById('editName').value = student.name ?? '';
        document.getElementById('editEmail').value = student.email ?? '';
        document.getElementById('editKelasId').value = student.student.kelas_id ?? '';
        document.getElementById('editTanggalLahir').value = student.student.tanggal_lahir ?? '';
        document.getElementById('editAlamat').value = student.student.alamat ?? '';
        document.getElementById('editNamaWali').value = student.student.nama_wali ?? '';
        document.getElementById('editNoHpWali').value = student.student.no_hp_wali ?? '';
        document.getElementById('editStatus').value = student.student.status ?? '';
    }

function confirmDelete(studentId) {
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
            document.getElementById('deleteForm' + studentId).submit();
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    @if($errors->any())
        document.getElementById('modal-tambah-student').classList.remove('hidden');
    @endif
});

function resetForm() {
    document.querySelectorAll('#modal-tambah-student form, #modal-edit-student form').forEach(form => form.reset());
}

document.querySelector('#modal-tambah-student .absolute').addEventListener('click', function() {
    resetForm();
    document.getElementById('modal-tambah-student').classList.add('hidden');
});



</script>

@if (session('success') || session('error'))
    <div id="flash-message"
        data-type="{{ session('success') ? 'success' : 'error' }}"
        data-message="{{ session('success') ?? session('error') }}">
    </div>
@endif

@if(session('editStudent'))
    <script>
        window.onload = function() {
            openEditModal(@json(session('editStudent')));
        }
    </script>
@endif



@endsection
