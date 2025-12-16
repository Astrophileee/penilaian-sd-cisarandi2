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
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 m-5">
            @forelse ($assignments as $assignment)
                <a href="{{ route('teachers.assignments.show', $assignment->id) }}"
                   class="block bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300">
                    <div class="p-4">
                        <h2 class="text-lg font-bold text-gray-900 mb-2">
                            <span
                                class="px-3 py-1 rounded-full bg-green-100 text-green-700 inline-block mt-1">
                                {{ $assignment->subject->nama ?? '-' }}
                            </span> - Kelas {{ $assignment->classroom->nama ?? '-' }}
                        </h2>
                        <div class="w-full text-sm text-gray-600">
                            <p>
                                Tingkat:
                                <span class="font-semibold">
                                    {{ $assignment->classroom->tingkat ?? '-' }}
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
            @empty
                <div class="col-span-full text-center text-gray-500 py-10">
                    <p class="text-lg font-medium">Tidak ada mapel yang ditugaskan.</p>
                </div>
            @endforelse
        </div>
    </div>


@vite(['resources/js/app.js'])

@endsection
