@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow">
    <div class="flex justify-between items-center mb-6">
        <a href="{{ route('teachers.assessments.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <h1 class="text-2xl font-semibold text-gray-800 mb-4">
        {{ $assignment->subject->nama }} - Kelas {{ $assignment->classroom->nama }}
    </h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div>
            <p class="text-sm text-gray-500">Nama Siswa</p>
            <p class="text-base font-medium text-gray-800">
                {{ $selectedStudent?->user->name ?? '-' }}
            </p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Guru</p>
            <p class="text-base font-medium text-gray-800">
                {{ $assignment->teacher->user->name ?? '-' }}
            </p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Kelas</p>
            <p class="text-base font-medium text-gray-800">{{ $assignment->classroom->nama }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Semester</p>
            <p class="text-base font-medium text-gray-800">
                @if($selectedSemester ?? null)
                    Semester {{ $selectedSemester->semester_ke }} {{ $selectedSemester->tahun_ajaran }}
                @else
                    -
                @endif
            </p>
        </div>
    </div>

    <div class="flex justify-between items-center mb-3">
        <h2 class="text-lg font-semibold">Daftar Nilai</h2>

        <form method="GET"
            action="{{ route('teachers.assessments.show', $assignment->id) }}"
            class="flex flex-wrap items-center gap-2">
            <label for="student_id" class="text-sm text-gray-600">Siswa:</label>

            <div class="relative">
                <select name="student_id" id="student_id"
                        class="appearance-none border border-gray-300 rounded-md px-3 py-1.5 pr-8 text-sm
                            focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                        onchange="this.form.submit()"
                        {{ $students->isEmpty() ? 'disabled' : '' }}>
                    @if($students->isEmpty())
                        <option value="">Belum ada siswa</option>
                    @else
                        @foreach($students as $student)
                            <option value="{{ $student->id }}"
                                {{ $student->id == ($selectedStudent?->id) ? 'selected' : '' }}>
                                {{ $student->user->name ?? '-' }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <label for="semester_id" class="text-sm text-gray-600">Semester:</label>

            <div class="relative">
                <select name="semester_id" id="semester_id"
                        class="appearance-none border border-gray-300 rounded-md px-3 py-1.5 pr-8 text-sm
                            focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                        onchange="this.form.submit()"
                        {{ $semesters->isEmpty() ? 'disabled' : '' }}>
                    @if($semesters->isEmpty())
                        <option value="">Belum ada semester</option>
                    @else
                        @foreach($semesters as $semester)
                            <option value="{{ $semester->id }}"
                                {{ $semester->id == ($selectedSemester?->id) ? 'selected' : '' }}>
                                Semester {{ $semester->semester_ke }} {{ $semester->tahun_ajaran }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
        </form>
    </div>

    <div class="mb-4">
        <p class="text-sm text-gray-500">Nilai Akhir</p>
        <p class="text-lg font-semibold text-gray-800">
            {{ $finalGrade !== null ? $finalGrade : '-' }}
        </p>
    </div>

    @if($students->isEmpty())
        <div class="text-center text-gray-500 py-8">
            Belum ada murid pada kelas ini.
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm border border-gray-200">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left">No</th>
                        <th class="px-4 py-2 text-left">Jenis</th>
                        <th class="px-4 py-2 text-left">Judul</th>
                        <th class="px-4 py-2 text-left">Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($assessments as $assessment)
                        @php
                            $grade = $assessment->grades->first();
                        @endphp
                        <tr class="border-b">
                            <td class="px-4 py-2">{{ $loop->iteration }}</td>
                            <td class="px-4 py-2 capitalize">{{ $assessment->tipe }}</td>
                            <td class="px-4 py-2">{{ $assessment->judul }}</td>
                            <td class="px-4 py-2 font-semibold">
                                @if($grade)
                                    {{ $grade->nilai }}
                                @else
                                    <span class="text-gray-400 italic">Belum ada nilai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-3 text-center text-gray-500">
                                Belum ada nilai yang disetujui untuk mapel ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
