@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-4">
            <h1 class="text-xl font-bold mb-4">
                Daftar Mapel yang Ditugaskan
                @if($currentSemester)
                    <span class="text-sm font-normal text-gray-600">
                        (Semester {{ $currentSemester->semester_ke }} {{ $currentSemester->tahun_ajaran }})
                    </span>
                @endif
            </h1>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-x-auto">
        @php
            $groupedAssignments = $assignments->groupBy(function ($assignment) {
                return $assignment->classroom->id ?? 'tanpa-kelas';
            });
        @endphp

        @forelse ($groupedAssignments as $classAssignments)
            @php
                $classroom = $classAssignments->first()->classroom;
            @endphp

            <div class="px-5 py-5">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-lg font-semibold text-gray-800">
                        Kelas {{ $classroom->nama ?? '-' }}
                        @if($classroom?->tingkat)
                            <span class="text-sm font-normal text-gray-500">Tingkat {{ $classroom->tingkat }}</span>
                        @endif
                    </h2>
                    <span class="text-xs text-gray-500">{{ $classAssignments->count() }} mapel</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($classAssignments as $assignment)
                        <a href="{{ route('teachers.assignments.show', $assignment->id) }}"
                           class="block bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300">
                            <div class="p-4">
                                <h3 class="text-base font-bold text-gray-900 mb-2">
                                    <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 inline-block mt-1">
                                        {{ $assignment->subject->nama ?? '-' }}
                                    </span>
                                </h3>
                                <div class="w-full text-sm text-gray-600">
                                    <p>
                                        Kelas:
                                        <span class="font-semibold">
                                            {{ $assignment->classroom->nama ?? '-' }}
                                        </span>
                                    </p>
                                    @if($currentSemester)
                                        <p>
                                            Semester:
                                            <span class="font-semibold">
                                                {{ $currentSemester->semester_ke }} {{ $currentSemester->tahun_ajaran }}
                                            </span>
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            @if(!$loop->last)
                <div class="border-t border-gray-200"></div>
            @endif
        @empty
            <div class="text-center text-gray-500 py-10">
                <p class="text-lg font-medium">Tidak ada mapel yang ditugaskan.</p>
            </div>
        @endforelse
    </div>


@vite(['resources/js/app.js'])

@endsection
