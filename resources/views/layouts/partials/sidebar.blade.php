<div id="overlay" onclick="closeSidebar()" class="fixed inset-0 bg-[rgba(0,0,0,0.75)] z-30 hidden lg:hidden"></div>



<!-- Sidebar -->
<aside id="sidebar" class="fixed z-40 top-0 left-0 w-64 min-h-screen bg-white border-r border-gray-200 transform -translate-x-full transition-transform duration-300 lg:translate-x-0 lg:static lg:z-auto">
    <div class="p-4 flex items-center gap-2">
    <img src="/logo" alt="Logo" class="w-8 h-8" />
    <div>
        <h1 class="font-bold text-sm">SD Cisarandi 2</h1>
    </div>
    </div>
    <nav class="mt-4 space-y-2 text-sm">
    <!-- Single link -->
    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100">
        <i class="fas fa-home w-5 h-5 pt-1 text-gray-600"></i>
        Dashboard
    </a>
    @hasrole('admin')
    <a href="{{ route('admins.index') }}" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100">
        <i class="fas fa-home w-5 h-5 pt-1 text-gray-600"></i>
        Manajamen Admin
    </a>
    <a href="{{ route('teachers.index') }}" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100">
        <i class="fas fa-home w-5 h-5 pt-1 text-gray-600"></i>
        Manajamen Guru
    </a>
    <a href="{{ route('students.index') }}" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100">
        <i class="fas fa-home w-5 h-5 pt-1 text-gray-600"></i>
        Manajamen Murid
    </a>
    <a href="{{ route('classrooms.index') }}" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100">
        <i class="fas fa-home w-5 h-5 pt-1 text-gray-600"></i>
        Manajamen Kelas
    </a>
    <a href="{{ route('subjects.index') }}" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100">
        <i class="fas fa-home w-5 h-5 pt-1 text-gray-600"></i>
        Manajamen Mata Pelajaran
    </a>
    @endhasrole
    {{-- <a href="{{ route('assignments.index') }}" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100">
        <i class="fas fa-home w-5 h-5 pt-1 text-gray-600"></i>
        Penugasan Guru Mata Pelajaran
    </a> --}}
    @hasrole('teacher')
    <a href="{{ route('teachers.assignments.index') }}" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100">
        <i class="fas fa-home w-5 h-5 pt-1 text-gray-600"></i>
        Daftar Penugasan Guru
    </a>
    <a href="{{ route('teachers.assessments.index') }}" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100">
        <i class="fas fa-home w-5 h-5 pt-1 text-gray-600"></i>
        Histori Nilai Siswa
    </a>
    @endhasrole
    @hasrole('headmaster')
    <a href="{{ route('headmasters.assessments.index') }}" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100">
        <i class="fas fa-home w-5 h-5 pt-1 text-gray-600"></i>
        Persetujuan Nilai
    </a>
    <a href="{{ route('semesters.index') }}" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100">
        <i class="fas fa-home w-5 h-5 pt-1 text-gray-600"></i>
        Manajemen Semester
    </a>
    @endhasrole
    @hasrole('student')
    <a href="{{ route('students.assessments.index') }}" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100">
        <i class="fas fa-home w-5 h-5 pt-1 text-gray-600"></i>
        Lihat Nilai
    </a>
    @endhasrole
    </nav>
</aside>
