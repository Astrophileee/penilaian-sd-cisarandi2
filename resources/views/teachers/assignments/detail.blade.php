@extends('layouts.app')

@section('content')

<div class="max-w-5xl mx-auto bg-white p-6 rounded-lg shadow">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <a href="{{ route('teachers.assignments.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <!-- Detail Penugasan -->
    <h1 class="text-2xl font-semibold text-gray-800 mb-4">
        {{ $assignment->subject->nama }} - Kelas {{ $assignment->classroom->nama }}
    </h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div>
            <p class="text-sm text-gray-500">Nama Mapel</p>
            <p class="text-base font-medium text-gray-800">{{ $assignment->subject->nama }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Kelas</p>
            <p class="text-base font-medium text-gray-800">{{ $assignment->classroom->nama }}</p>
        </div>
    </div>

    <div class="mb-6">
        <p class="text-sm text-gray-500 mb-1">Semester</p>
        <p class="text-gray-800">
            @if($currentSemester)
                Semester {{ $currentSemester->semester_ke }} {{ $currentSemester->tahun_ajaran }}
            @else
                -
            @endif
        </p>
    </div>

    <!-- Tabs -->
    <div>
        <div class="border-b border-gray-200 mb-4">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button id="tab-assessment"
                        onclick="showTab('assessment')"
                        class="tab-button border-b-2 border-black text-black px-3 py-2 text-sm font-medium">
                    Daftar Tugas / Penilaian
                </button>
                <button id="tab-student"
                        onclick="showTab('student')"
                        class="tab-button border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 px-3 py-2 text-sm font-medium">
                    Daftar Murid
                </button>
            </nav>
        </div>

        <!-- Tab 1: Daftar Tugas -->
        <div id="content-assessment" class="tab-content">
            <button
                type="button"
                onclick="openAssessmentModal()"
                class="text-green-600 hover:text-green-900 border border-green-600 rounded-md px-4 py-2 mb-5 text-sm focus:outline-none focus:ring-2 focus:ring-green-200">
                Buat Tugas Harian
            </button>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm border border-gray-200">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-left">No</th>
                            <th class="px-4 py-2 text-left">Tanggal</th>
                            <th class="px-4 py-2 text-left">Jenis</th>
                            <th class="px-4 py-2 text-left">Nama</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-left">Alasan Ditolak</th>
                            <th class="px-4 py-2 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($assessments as $assessment)
                            @php
                                $gradeMap = $assessment->grades->pluck('nilai', 'student_id');
                            @endphp
                            <tr class="border-b">
                                <td class="px-4 py-2">{{ $loop->iteration }}</td>
                                <td class="px-4 py-2">{{ $assessment->tanggal }}</td>
                                <td class="px-4 py-2 capitalize">{{ $assessment->tipe }}</td>
                                <td class="px-4 py-2">{{ $assessment->judul }}</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if($assessment->status === 'approved') bg-green-100 text-green-700
                                        @elseif($assessment->status === 'submitted') bg-yellow-100 text-yellow-700
                                        @elseif($assessment->status === 'rejected') bg-red-100 text-red-700
                                        @else bg-gray-100 text-gray-700
                                        @endif">
                                        {{ ucfirst($assessment->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2">{{ $assessment->approval_note ?? '-' }}</td>
                                <td class="px-4 py-2">
                                    <button
                                        type="button"
                                        onclick='openEditAssessmentModal(
                                            {{ $assessment->id }},
                                            @json($assessment->judul),
                                            @json($assessment->tanggal),
                                            @json($assessment->status)
                                        )'
                                        class="text-gray-700 hover:text-gray-900 border border-gray-500 rounded-md px-3 py-1 text-xs">
                                        Edit
                                    </button>
                                        @if(in_array($assessment->status, ['draft', 'rejected']))
                                        <button
                                            type="button"
                                            onclick='openGradeModal(
                                                {{ $assessment->id }},
                                                @json($assessment->judul),
                                                @json($assessment->grades->pluck("nilai", "student_id"))
                                            )'
                                            class="text-blue-600 hover:text-blue-900 border border-blue-600 rounded-md px-3 py-1 text-xs">
                                            Beri / Edit Nilai
                                        </button>
                                    @else
                                        <span class="text-xs text-gray-400 italic">
                                            Nilai terkunci
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-3 text-center text-gray-500">
                                    Belum ada data penilaian
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab 2: Daftar Murid -->
        <div id="content-student" class="tab-content hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm border border-gray-200">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-left">No</th>
                            <th class="px-4 py-2 text-left">Nama</th>
                            <th class="px-4 py-2 text-left">Kelas</th>
                            <th class="px-4 py-2 text-left">No HP Wali</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $student)
                            <tr class="border-b">
                                <td class="px-4 py-2">{{ $loop->iteration }}</td>
                                <td class="px-4 py-2">{{ $student->user->name ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $assignment->classroom->nama }}</td>
                                <td class="px-4 py-2">{{ $student->no_hp_wali ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-center text-gray-500">Belum ada murid</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Buat Tugas Harian --}}
<div id="modal-create-assessment" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 hidden">
    <div class="min-h-screen flex items-center justify-center py-6 px-4">
        <div class="bg-white w-full max-w-md mx-auto rounded-lg shadow-lg p-6 relative">
            <button onclick="closeAssessmentModal()" class="absolute top-4 right-4 text-xl font-bold text-gray-600 hover:text-gray-800">&times;</button>

            <h2 class="text-lg font-semibold mb-4">Buat Tugas Harian</h2>

            <form id="createAssessmentForm"
                  action="{{ route('teachers.assessments.store', $assignment->id) }}"
                  method="POST">
                @csrf
                <input type="hidden" name="type" value="tugas">

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Judul Tugas *</label>
                    <input type="text" name="judul" class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm" required>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Tanggal *</label>
                    <input type="date" name="tanggal" class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm" required>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeAssessmentModal()" class="px-4 py-2 rounded-md border text-sm">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-black text-white rounded-md text-sm hover:bg-gray-800">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Beri Nilai -->
<div id="modal-edit-student" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 hidden">
    <div class="min-h-screen flex items-center justify-center py-6 px-4">
        <div class="bg-white w-full max-w-xl mx-auto rounded-lg shadow-lg p-6 relative">
            <!-- Close button -->
            <button onclick="closeGradeModal()" class="absolute top-4 right-4 text-xl font-bold text-gray-600 hover:text-gray-800">&times;</button>

            <h2 class="text-lg font-semibold mb-4" id="gradeModalTitle">Beri Nilai Murid</h2>

            <form id="editStudentForm" action="" method="POST">
                @csrf
                @method('POST')

                <div class="overflow-x-auto max-h-80">
                    <table class="min-w-full text-sm border border-gray-200">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left">Nama</th>
                                <th class="px-4 py-2 text-left">Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($students as $student)
                                <tr class="border-b">
                                    <td class="px-4 py-2">
                                        {{ $student->user->name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="number" name="nilai[{{ $student->id }}]"
                                            step="0.01" min="0" max="100"
                                            class="w-24 border border-gray-300 rounded-md px-2 py-1 text-sm">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" onclick="closeGradeModal()" class="px-4 py-2 rounded-md border text-sm">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-black text-white rounded-md text-sm hover:bg-gray-800">Simpan Nilai</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit Assessment --}}
<div id="modal-edit-assessment" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 hidden">
    <div class="min-h-screen flex items-center justify-center py-6 px-4">
        <div class="bg-white w-full max-w-md mx-auto rounded-lg shadow-lg p-6 relative">
            <button onclick="closeEditAssessmentModal()" class="absolute top-4 right-4 text-xl font-bold text-gray-600 hover:text-gray-800">&times;</button>

            <h2 class="text-lg font-semibold mb-4" id="editAssessmentTitle">Edit Penilaian</h2>

            <form id="editAssessmentForm" method="POST" action="">
                @csrf
                @method('PATCH')

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Judul</label>
                    <input type="text" name="judul" id="editAssessmentJudul"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm">
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Tanggal *</label>
                    <input type="date" name="tanggal" id="editAssessmentTanggal"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Status *</label>
                    <select name="status" id="editAssessmentStatus" class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm" required>
                        <option value="" selected>Pilih Status</option>
                        <option value="draft">Draft</option>
                        <option value="submitted">Submit</option>
                    </select>
                    @error('status')
                        <div class="text-red-500 text-xs mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeEditAssessmentModal()" class="px-4 py-2 rounded-md border text-sm">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-black text-white rounded-md text-sm hover:bg-gray-800">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>


@vite('resources/js/app.js')
<script>
    function showTab(tab) {
        document.getElementById('content-assessment').classList.add('hidden');
        document.getElementById('content-student').classList.add('hidden');

        document.getElementById('tab-assessment').classList.remove('border-black', 'text-black');
        document.getElementById('tab-assessment').classList.add('border-transparent', 'text-gray-500');

        document.getElementById('tab-student').classList.remove('border-black', 'text-black');
        document.getElementById('tab-student').classList.add('border-transparent', 'text-gray-500');

        if (tab === 'assessment') {
            document.getElementById('content-assessment').classList.remove('hidden');
            document.getElementById('tab-assessment').classList.add('border-black', 'text-black');
        } else {
            document.getElementById('content-student').classList.remove('hidden');
            document.getElementById('tab-student').classList.add('border-black', 'text-black');
        }
    }

    function openAssessmentModal() {
        document.getElementById('modal-create-assessment').classList.remove('hidden');
    }

    function closeAssessmentModal() {
        document.getElementById('modal-create-assessment').classList.add('hidden');
        document.getElementById('createAssessmentForm').reset();
    }

    function openEditAssessmentModal(assessmentId, judul, tanggal, status) {
        const modal = document.getElementById('modal-edit-assessment');
        modal.classList.remove('hidden');

        document.getElementById('editAssessmentTitle').innerText = 'Edit Penilaian - ' + judul;

        const form = document.getElementById('editAssessmentForm');
        const actionTemplate = @json(route('teachers.assessments.update', ['assessment' => '__ID__']));
        form.action = actionTemplate.replace('__ID__', assessmentId);

        document.getElementById('editAssessmentJudul').value = judul || '';
        document.getElementById('editAssessmentTanggal').value = tanggal || '';

        status = (status || '').toLowerCase().trim();
        document.getElementById('editAssessmentStatus').value = status;
    }

    function closeEditAssessmentModal() {
        const modal = document.getElementById('modal-edit-assessment');
        modal.classList.add('hidden');
    }

    function openGradeModal(assessmentId, judul, grades) {
        document.getElementById('modal-edit-student').classList.remove('hidden');
        document.getElementById('gradeModalTitle').innerText = 'Beri Nilai - ' + judul;
        const form = document.getElementById('editStudentForm');
        const actionTemplate = @json(route('teachers.grades.store', ['assessment' => '__ID__']));
        form.action = actionTemplate.replace('__ID__', assessmentId);
        document.querySelectorAll('#editStudentForm input[name^="nilai["]').forEach((input) => {
            input.value = '';
        });
        Object.keys(grades || {}).forEach((studentId) => {
            const selector = `#editStudentForm input[name="nilai[${studentId}]"]`;
            const input = document.querySelector(selector);
            if (input) {
                input.value = grades[studentId];
            }
        });
    }

    function closeGradeModal() {
        document.getElementById('modal-edit-student').classList.add('hidden');
    }
</script>

@if (session('success') || session('error'))
    <div id="flash-message"
        data-type="{{ session('success') ? 'success' : 'error' }}"
        data-message="{{ session('success') ?? session('error') }}">
    </div>
@endif

@endsection
