@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-bold">Penilaian Menunggu Persetujuan</h1>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-x-auto">
        <table id="assessmentsTable" class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">No</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Mapel</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kelas</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Guru</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Jenis</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Judul</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($assessments as $assessment)
                    @php
                        // siapkan data murid + nilai untuk modal "Lihat Nilai"
                        $studentsData = $assessment->teacherClassSubject->classroom->student->map(function ($student) use ($assessment) {
                            $grade = $assessment->grades->firstWhere('student_id', $student->id);

                            return [
                                'name'  => $student->user->name ?? '-',
                                'wali'  => $student->nama_wali ?? '-',
                                'nilai' => $grade->nilai ?? null,
                            ];
                        })->values();
                    @endphp

                    <tr>
                        <td class="px-6 py-3 whitespace-nowrap text-gray-700">{{ $loop->iteration }}</td>
                        <td class="px-4 py-2">{{ $assessment->tanggal }}</td>
                        <td class="px-4 py-2">{{ $assessment->teacherClassSubject->subject->nama }}</td>
                        <td class="px-4 py-2">{{ $assessment->teacherClassSubject->classroom->nama }}</td>
                        <td class="px-4 py-2">{{ $assessment->teacherClassSubject->teacher->user->name ?? '-' }}</td>
                        <td class="px-4 py-2 capitalize">{{ $assessment->tipe }}</td>
                        <td class="px-4 py-2">{{ $assessment->judul }}</td>
                        <td class="px-4 py-2 flex gap-2 justify-end">
                            {{-- Edit Status --}}
                            <button
                                type="button"
                                onclick='openStatusModal(
                                    {{ $assessment->id }},
                                    @json($assessment->judul),
                                    @json($assessment->status),
                                    @json($assessment->approval_note)
                                )'
                                class="text-xs border border-gray-600 text-gray-700 px-2 py-1 rounded hover:bg-gray-50">
                                Edit Status
                            </button>

                            {{-- Lihat Nilai --}}
                            <button
                                type="button"
                                onclick='openViewGradesModal(
                                    @json($assessment->judul),
                                    @json($assessment->teacherClassSubject->classroom->nama),
                                    @json($studentsData)
                                )'
                                class="text-xs border border-blue-600 text-blue-700 px-2 py-1 rounded hover:bg-blue-50">
                                Lihat Nilai
                            </button>
                        </td>
                    </tr>
                    @endforeach
            </tbody>
        </table>
    </div>

    {{-- Modal Edit Status --}}
    <div id="modal-edit-status" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 hidden">
        <div class="min-h-screen flex items-center justify-center py-6 px-4">
            <div class="bg-white w-full max-w-md mx-auto rounded-lg shadow-lg p-6 relative">
                <button onclick="closeStatusModal()" class="absolute top-4 right-4 text-xl font-bold text-gray-600 hover:text-gray-800">&times;</button>

                <h2 class="text-lg font-semibold mb-4" id="statusModalTitle">Edit Status Penilaian</h2>

                <form id="editStatusForm" method="POST" action="">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700">Status *</label>
                        <select name="status" id="statusSelect"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm" required>
                            <option value="">Pilih Status</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                        @error('status')
                            <div class="text-red-500 text-xs mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 hidden" id="rejectReasonWrapper">
                        <label class="block text-sm font-medium text-gray-700">Alasan Penolakan</label>
                        <textarea name="approval_note" id="approval_note"
                                  class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 text-sm"
                                  rows="3"
                                  placeholder="Tuliskan alasan penilaian ini ditolak"></textarea>
                        @error('approval_note')
                            <div class="text-red-500 text-xs mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="closeStatusModal()" class="px-4 py-2 rounded-md border text-sm">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-black text-white rounded-md text-sm hover:bg-gray-800">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Lihat Nilai --}}
    <div id="modal-view-grades" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 hidden">
        <div class="min-h-screen flex items-center justify-center py-6 px-4">
            <div class="bg-white w-full max-w-2xl mx-auto rounded-lg shadow-lg p-6 relative">
                <button onclick="closeViewGradesModal()" class="absolute top-4 right-4 text-xl font-bold text-gray-600 hover:text-gray-800">&times;</button>

                <h2 class="text-lg font-semibold mb-4" id="viewGradesTitle">Nilai Siswa</h2>

                <div class="overflow-x-auto max-h-96">
                    <table class="min-w-full text-sm border border-gray-200">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left">No</th>
                                <th class="px-4 py-2 text-left">Nama Siswa</th>
                                <th class="px-4 py-2 text-left">Nama Wali</th>
                                <th class="px-4 py-2 text-left">Nilai</th>
                            </tr>
                        </thead>
                        <tbody id="gradesTableBody">
                            {{-- diisi via JS --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @vite(['resources/js/app.js'])
    <script>
        function openStatusModal(assessmentId, judul, status, approvalNote) {
            const modal = document.getElementById('modal-edit-status');
            modal.classList.remove('hidden');

            document.getElementById('statusModalTitle').innerText = 'Edit Status - ' + judul;

            const form = document.getElementById('editStatusForm');
            const actionTemplate = @json(route('headmasters.assessments.updateStatus', ['assessment' => '__ID__']));
            form.action = actionTemplate.replace('__ID__', assessmentId);

            const select = document.getElementById('statusSelect');
            select.value = (status === 'approved' || status === 'rejected') ? status : '';

            const reasonWrapper = document.getElementById('rejectReasonWrapper');
            const noteInput = document.getElementById('approval_note');

            if (select.value === 'rejected') {
                reasonWrapper.classList.remove('hidden');
                noteInput.value = approvalNote || '';
            } else {
                reasonWrapper.classList.add('hidden');
                noteInput.value = '';
            }

            select.onchange = function () {
                if (this.value === 'rejected') {
                    reasonWrapper.classList.remove('hidden');
                } else {
                    reasonWrapper.classList.add('hidden');
                    noteInput.value = '';
                }
            };
        }

        function closeStatusModal() {
            document.getElementById('modal-edit-status').classList.add('hidden');
        }

        function openViewGradesModal(judul, kelas, students) {
            const modal = document.getElementById('modal-view-grades');
            modal.classList.remove('hidden');

            document.getElementById('viewGradesTitle').innerText = 'Nilai - ' + judul + ' (' + kelas + ')';

            const tbody = document.getElementById('gradesTableBody');
            tbody.innerHTML = '';

            if (!students || students.length === 0) {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td colspan="4" class="px-4 py-3 text-center text-gray-500">Belum ada data nilai.</td>`;
                tbody.appendChild(tr);
                return;
            }

            students.forEach((s, index) => {
                const tr = document.createElement('tr');
                tr.classList.add('border-b');
                tr.innerHTML = `
                    <td class="px-4 py-2">${index + 1}</td>
                    <td class="px-4 py-2">${s.name ?? '-'}</td>
                    <td class="px-4 py-2">${s.wali ?? '-'}</td>
                    <td class="px-4 py-2">${s.nilai !== null ? s.nilai : '-'}</td>
                `;
                tbody.appendChild(tr);
            });
        }

        function closeViewGradesModal() {
            document.getElementById('modal-view-grades').classList.add('hidden');
        }
    </script>

    @if (session('success') || session('error'))
        <div id="flash-message"
            data-type="{{ session('success') ? 'success' : 'error' }}"
            data-message="{{ session('success') ?? session('error') }}">
        </div>
    @endif
@endsection
