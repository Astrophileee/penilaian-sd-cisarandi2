@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-4">
    <h1 class="text-xl font-bold">Histori Nilai Siswa</h1>
    <p class="text-sm text-gray-500">
        Guru: {{ $teacher->user->name ?? '-' }}
    </p>
</div>

<div class="bg-white shadow-md rounded-lg overflow-x-auto">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 m-5">
        @forelse ($assignments as $assignment)
            <a href="{{ route('teachers.assessments.show', $assignment->id) }}"
               class="block bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300">
                <div class="p-4">
                    <div class="text-sm text-gray-500 mb-1">
                        <span class="font-bold text-gray-800">
                            {{ $assignment->subject->nama }}
                        </span>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700 inline-block">
                            Kelas {{ $assignment->classroom->nama }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600">
                        Tingkat: {{ $assignment->classroom->tingkat ?? '-' }}
                    </p>
                </div>
            </a>
        @empty
            <div class="col-span-full text-center text-gray-500 py-10">
                <p class="text-lg font-medium">Belum ada penugasan.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
