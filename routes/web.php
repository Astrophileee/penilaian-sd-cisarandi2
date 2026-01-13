<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\HeadmasterAssessmentController;
use App\Http\Controllers\InformationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\StudentAssessmentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentInformationController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherAssessmentController;
use App\Http\Controllers\TeacherClassSubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TeacherController as ControllersTeacherController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

    Route::prefix('admins')->name('admins.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::post('/', [AdminController::class, 'store'])->name('store');
        Route::patch('/{admin}', [AdminController::class, 'update'])->name('update');
        Route::delete('/{admin}', [AdminController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('teachers')->name('teachers.')->group(function () {
        Route::get('/', [TeacherController::class, 'index'])->name('index');
        Route::post('/', [TeacherController::class, 'store'])->name('store');
        Route::patch('/{teacher}', [TeacherController::class, 'update'])->name('update');
        Route::delete('/{teacher}', [TeacherController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('students')->name('students.')->group(function () {
        Route::get('/', [StudentController::class, 'index'])->name('index');
        Route::post('/', [StudentController::class, 'store'])->name('store');
        Route::patch('/{student}', [StudentController::class, 'update'])->name('update');
        Route::delete('/{student}', [StudentController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('classrooms')->name('classrooms.')->group(function () {
        Route::get('/', [ClassroomController::class, 'index'])->name('index');
        Route::post('/', [ClassroomController::class, 'store'])->name('store');
        Route::patch('/{classroom}', [ClassroomController::class, 'update'])->name('update');
        Route::delete('/{classroom}', [ClassroomController::class, 'destroy'])->name('destroy');
    });
    Route::prefix('subjects')->name('subjects.')->group(function () {
        Route::get('/', [SubjectController::class, 'index'])->name('index');
        Route::post('/', [SubjectController::class, 'store'])->name('store');
        Route::patch('/{subject}', [SubjectController::class, 'update'])->name('update');
        Route::delete('/{subject}', [SubjectController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('information')->name('information.')->group(function () {
        Route::get('/', [InformationController::class, 'index'])->name('index');
        Route::post('/', [InformationController::class, 'store'])->name('store');
        Route::patch('/{information}', [InformationController::class, 'update'])->name('update');
        Route::delete('/{information}', [InformationController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('assignments')->name('assignments.')->group(function () {
        Route::get('/', [TeacherClassSubjectController::class, 'index'])->name('index');
        Route::post('/', [TeacherClassSubjectController::class, 'store'])->name('store');
        Route::patch('/{assignment}', [TeacherClassSubjectController::class, 'update'])->name('update');
        Route::delete('/{assignment}', [TeacherClassSubjectController::class, 'destroy'])->name('destroy');
    });

    Route::get('/teacher/assignments', [TeacherController::class, 'assignments'])->name('teachers.assignments.index');
    Route::get('/teacher/assignments/{assignment}', [TeacherClassSubjectController::class, 'show'])->name('teachers.assignments.show');
    Route::post('/teacher/assignments/{assignment}/assessments', [TeacherClassSubjectController::class, 'storeAssessment'])
    ->name('teachers.assessments.store');
    Route::post('/teacher/assignments/{assignment}/assessments/submit', [TeacherClassSubjectController::class, 'submitAssessments'])
    ->name('teachers.assessments.submit');
    Route::post('/teacher/assignments/{assignment}/assessments/final', [TeacherClassSubjectController::class, 'generateFinalGrades'])
    ->name('teachers.assessments.generateFinal');
    Route::post('/teacher/assessments/{assessment}/grades', [TeacherClassSubjectController::class, 'storeGrades'])
    ->name('teachers.grades.store');
    Route::patch('/teacher/assessments/{assessment}', [TeacherClassSubjectController::class, 'updateAssessment'])
        ->name('teachers.assessments.update');

    Route::get('/teacher/nilai', [TeacherAssessmentController::class, 'index'])
        ->name('teachers.assessments.index');
    Route::get('/teacher/nilai/{assignment}', [TeacherAssessmentController::class, 'show'])
        ->name('teachers.assessments.show');

    Route::get('/headmaster/assessments', [HeadmasterAssessmentController::class, 'index'])->name('headmasters.assessments.index');
    Route::patch('/headmaster/assessments/{assessment}/status', [HeadmasterAssessmentController::class, 'updateStatus'])->name('headmasters.assessments.updateStatus');

    Route::get('/siswa/nilai', [StudentAssessmentController::class, 'index'])
        ->name('students.assessments.index');

    Route::get('/siswa/informasi', [StudentInformationController::class, 'index'])
        ->name('students.information.index');

    Route::get('/siswa/nilai/{assignment}', [StudentAssessmentController::class, 'show'])
        ->name('students.assessments.show');

            Route::get('/headmaster/semesters', [SemesterController::class, 'index'])
        ->name('semesters.index');

    Route::post('/headmaster/semesters', [SemesterController::class, 'store'])
        ->name('semesters.store');

    Route::patch('/headmaster/semesters/{semester}/activate', [SemesterController::class, 'activate'])
        ->name('semesters.activate');

require __DIR__.'/auth.php';
