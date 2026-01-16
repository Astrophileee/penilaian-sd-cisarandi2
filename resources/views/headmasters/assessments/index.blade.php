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
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Mapel</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kelas</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Guru</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Aspek Penilaian</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($assessmentGroups as $groupKey => $group)
                    @php
                        $representative = $group->first();
                        $finalAssessment = $finalAssessments->get($groupKey);
                        $statusTitle = ($representative->teacherClassSubject->subject->nama ?? '-') . ' - Kelas ' . ($representative->teacherClassSubject->classroom->nama ?? '-');
                        $aspects = $group->map(function ($assessment) {
                            $type = strtolower(trim($assessment->tipe ?? ''));
                            $label = $assessment->judul;

                            if ($type === 'uts') {
                                $label = 'ATS';
                            } elseif ($type === 'uas') {
                                $label = 'ASAS';
                            } elseif ($type === 'tugas') {
                                $label = 'Tugas Harian';
                            } elseif ($type === 'absen') {
                                $label = 'Absen';
                            } elseif ($type === 'sikap') {
                                $label = 'Sikap';
                            } elseif (!$label) {
                                $label = ucfirst($type ?: '-');
                            }

                            return [
                                'id' => $assessment->id,
                                'label' => $label,
                            ];
                        })->values();
                        $studentsData = $representative->teacherClassSubject->classroom->student->map(function ($student) use ($group, $finalAssessment) {
                            $grades = $group->mapWithKeys(function ($assessment) use ($student) {
                                $grade = $assessment->grades->firstWhere('student_id', $student->id);

                                return [
                                    $assessment->id => $grade?->nilai,
                                ];
                            })->toArray();
                            $finalGrade = $finalAssessment?->grades->firstWhere('student_id', $student->id)?->nilai;

                            return [
                                'name'  => $student->user->name ?? '-',
                                'wali'  => $student->nama_wali ?? '-',
                                'grades' => $grades,
                                'final' => $finalGrade,
                            ];
                        })->values();
                    @endphp

                    <tr>
                        <td class="px-6 py-4 text-gray-700">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $representative->teacherClassSubject->subject->nama }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $representative->teacherClassSubject->classroom->nama }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $representative->teacherClassSubject->teacher->user->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-700">
                            <div class="space-y-1">
                                @foreach ($group as $aspect)
                                    @php
                                        $typeKey = strtolower(trim($aspect->tipe ?? ''));
                                        $typeLabel = $typeKey === 'uts' ? 'ATS' : ($typeKey === 'uas' ? 'ASAS' : $aspect->tipe);
                                    @endphp
                                    <div class="text-xs text-gray-700">
                                        <span class="font-semibold capitalize">{{ $typeLabel }}</span>
                                        - {{ $aspect->judul }}
                                    </div>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-4 py-2 text-right whitespace-nowrap">
                            <div class="flex justify-end gap-2">
                                {{-- Edit Status --}}
                                <button
                                    type="button"
                                    onclick='openStatusModal(
                                        {{ $representative->id }},
                                        @json($statusTitle),
                                        @json($representative->status),
                                        @json($representative->approval_note)
                                    )'
                                    class="text-xs border border-gray-600 text-gray-700 px-2 py-1 rounded hover:bg-gray-50">
                                    Edit Status
                                </button>

                                {{-- Lihat Nilai --}}
                                <button
                                    type="button"
                                    onclick='openViewGradesModal(
                                        @json($statusTitle),
                                        @json($representative->teacherClassSubject->classroom->nama),
                                        @json($aspects),
                                        @json($studentsData)
                                    )'
                                    class="text-xs border border-blue-600 text-blue-700 px-2 py-1 rounded hover:bg-blue-50">
                                    Lihat Nilai
                                </button>
                            </div>
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

                <h2 class="text-lg font-semibold mb-4" id="viewGradesTitle">Nilai Per Aspek</h2>

                <div class="overflow-x-auto max-h-96">
                    <table class="min-w-full text-sm border border-gray-200">
                        <thead id="gradesTableHead" class="bg-gray-100 text-gray-700"></thead>
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

        function openViewGradesModal(judul, kelas, aspects, students) {
            const modal = document.getElementById('modal-view-grades');
            modal.classList.remove('hidden');

            document.getElementById('viewGradesTitle').innerText = 'Nilai Per Aspek - ' + judul + ' (' + kelas + ')';

            const safeAspects = Array.isArray(aspects) ? aspects : [];
            const head = document.getElementById('gradesTableHead');
            let headHtml = '<tr>';
            headHtml += '<th class="px-4 py-2 text-left">No</th>';
            headHtml += '<th class="px-4 py-2 text-left">Nama Siswa</th>';
            headHtml += '<th class="px-4 py-2 text-left">Nama Wali</th>';
            safeAspects.forEach((aspect) => {
                const label = (aspect && aspect.label) ? aspect.label : '-';
                headHtml += `<th class="px-4 py-2 text-left">${label}</th>`;
            });
            headHtml += '<th class="px-4 py-2 text-left">Nilai Akhir</th>';
            headHtml += '</tr>';
            head.innerHTML = headHtml;

            const tbody = document.getElementById('gradesTableBody');
            tbody.innerHTML = '';

            if (!students || students.length === 0) {
                const emptyColspan = 4 + safeAspects.length;
                const tr = document.createElement('tr');
                tr.innerHTML = `<td colspan="${emptyColspan}" class="px-4 py-3 text-center text-gray-500">Belum ada data nilai.</td>`;
                tbody.appendChild(tr);
                return;
            }

            students.forEach((s, index) => {
                const grades = s.grades || {};
                const gradesHtml = safeAspects.map((aspect) => {
                    const nilai = grades[aspect.id];
                    const nilaiText = nilai !== null && nilai !== undefined ? nilai : '-';
                    return `<td class="px-4 py-2">${nilaiText}</td>`;
                }).join('');
                const finalText = s.final !== null && s.final !== undefined ? s.final : '-';
                const tr = document.createElement('tr');
                tr.classList.add('border-b');
                tr.innerHTML = `
                    <td class="px-4 py-2">${index + 1}</td>
                    <td class="px-4 py-2">${s.name ?? '-'}</td>
                    <td class="px-4 py-2">${s.wali ?? '-'}</td>
                    ${gradesHtml}
                    <td class="px-4 py-2 font-semibold">${finalText}</td>
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
