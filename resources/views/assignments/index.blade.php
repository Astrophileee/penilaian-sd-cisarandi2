@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-bold">Daftar Penugasan Guru</h1>
        <button onclick="document.getElementById('modal-tambah-assignment').classList.remove('hidden')" class="bg-black text-white px-4 py-2 rounded-md shadow hover:bg-gray-800">
            Tambah
        </button>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-x-auto">
        <table id="assignmentsTable" class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">No</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nama Guru</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kelas</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Mapel</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($assignments as $assignment)
                    <tr>
                        <td class="whitespace-nowrap text-gray-700">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $assignment->teacher->user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $assignment->classroom->nama }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $assignment->subject->nama }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <!-- Tombol Edit -->
                                <button
                                    type="button"
                                    class="text-blue-600 hover:text-blue-900 border border-blue-600 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200"
                                    onclick='openEditModal(@json($assignment))'>
                                    Edit
                                </button>

                                <!-- Tombol Delete -->
                                <form id="deleteForm{{ $assignment->id }}" action="{{ route('assignments.destroy', $assignment) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="button"
                                        onclick="confirmDelete('{{ $assignment->id }}')"
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
<div id="modal-tambah-assignment" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 hidden">
    <div class="min-h-screen flex items-center justify-center py-6 px-4">
        <div class="bg-white w-full max-w-md mx-auto rounded-lg shadow-lg p-6 relative">
            <!-- Close button -->
            <button onclick="document.getElementById('modal-tambah-assignment').classList.add('hidden')" class="absolute top-4 right-4 text-xl font-bold text-gray-600 hover:text-gray-800">&times;</button>

            <h2 class="text-lg font-semibold mb-4">Tambah Kelas</h2>

                <form action="{{ route('assignments.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <!-- Guru -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Guru *</label>
                    <select name="teacher_id" class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm" required>
                        <option value="" selected>Pilih Guru</option>
                        @foreach ($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->user->name}}</option>
                        @endforeach
                    </select>
                    @error('teacher_id')
                        <div class="text-red-500 text-xs mt-2">{{ $message }}</div>
                    @enderror
                </div>

                    <!-- Kelas -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Kelas *</label>
                    <select name="classroom_id" class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm" required>
                        <option value="" selected>Pilih Kelas</option>
                        @foreach ($classrooms as $classroom)
                            <option value="{{ $classroom->id }}">{{ $classroom->nama}}</option>
                        @endforeach
                    </select>
                    @error('classroom_id')
                        <div class="text-red-500 text-xs mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Mapel -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Mata Pelajaran *</label>
                    <select name="subject_id" class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm" required>
                        <option value="" selected>Pilih Mapel</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->nama}}</option>
                        @endforeach
                    </select>
                    @error('subject_id')
                        <div class="text-red-500 text-xs mt-2">{{ $message }}</div>
                    @enderror
                </div>

                    <!-- Action -->
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="resetForm(); document.getElementById('modal-tambah-assignment').classList.add('hidden')" class="px-4 py-2 rounded-md border text-sm">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-black text-white rounded-md text-sm hover:bg-gray-800">Simpan</button>
                    </div>
                </form>

        </div>
    </div>
</div>

<!-- Modal Edit -->
<div id="modal-edit-assignment" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 hidden">
    <div class="min-h-screen flex items-center justify-center py-6 px-4">
        <div class="bg-white w-full max-w-md mx-auto rounded-lg shadow-lg p-6 relative">
            <button onclick="document.getElementById('modal-edit-assignment').classList.add('hidden')" class="absolute top-4 right-4 text-xl font-bold text-gray-600 hover:text-gray-800">&times;</button>
            <h2 class="text-lg font-semibold mb-4">Edit Pengguna</h2>

            <form id="editAssignmentForm" method="POST" enctype="multipart/form-data"
            action="{{ route('assignments.update', ['assignment' => '__ID__']) }}">
                @csrf
                @method('PATCH')

                    <!-- Guru -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Guru *</label>
                    <select name="teacher_id" id="editGuru" class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm" required>
                        <option value="" selected>Pilih Guru</option>
                        @foreach ($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->user->name}}</option>
                        @endforeach
                    </select>
                    @error('teacher_id')
                        <div class="text-red-500 text-xs mt-2">{{ $message }}</div>
                    @enderror
                </div>

                    <!-- Kelas -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Kelas *</label>
                    <select name="classroom_id" id="editKelas" class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm" required>
                        <option value="" selected>Pilih Guru</option>
                        @foreach ($classrooms as $classroom)
                            <option value="{{ $classroom->id }}">{{ $classroom->nama}}</option>
                        @endforeach
                    </select>
                    @error('classroom_id')
                        <div class="text-red-500 text-xs mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Mapel -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Mata Pelajaran *</label>
                    <select name="subject_id" id="editMapel" class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm" required>
                        <option value="" selected>Pilih Mapel</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->nama}}</option>
                        @endforeach
                    </select>
                    @error('subject_id')
                        <div class="text-red-500 text-xs mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="resetForm(); document.getElementById('modal-edit-assignment').classList.add('hidden')" class="px-4 py-2 rounded-md border text-sm">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-black text-white rounded-md text-sm hover:bg-gray-800">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>


@vite(['resources/js/app.js'])
<script>
    function openEditModal(assignment) {
        const modal = document.getElementById('modal-edit-assignment');
        modal.classList.remove('hidden');

        const form = document.getElementById('editAssignmentForm');
        form.action = form.action.replace('__ID__', assignment.id);

        document.getElementById('editGuru').value = assignment.teacher_id ?? '';
        document.getElementById('editKelas').value = assignment.classroom_id ?? '';
        document.getElementById('editMapel').value = assignment.subject_id ?? '';
    }

function confirmDelete(assignmentId) {
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
            document.getElementById('deleteForm' + assignmentId).submit();
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    @if($errors->any())
        document.getElementById('modal-tambah-assignment').classList.remove('hidden');
    @endif
});

function resetForm() {
    const form = document.querySelector('#modal-tambah-assignment form');
    form.reset();
}

document.querySelector('#modal-tambah-assignment .absolute').addEventListener('click', function() {
    resetForm();
    document.getElementById('modal-tambah-assignment').classList.add('hidden');
});



</script>

@if (session('success') || session('error'))
    <div id="flash-message"
        data-type="{{ session('success') ? 'success' : 'error' }}"
        data-message="{{ session('success') ?? session('error') }}">
    </div>
@endif

@if(session('editAssignment'))
    <script>
        window.onload = function() {
            openEditModal(@json(session('editAssignment')));
        }
    </script>
@endif



@endsection
