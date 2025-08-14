<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudyGroupController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\GroupSessionController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\GroupMaterialController;



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
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/groups', [StudyGroupController::class, 'index'])->name('groups.index');
    Route::get('/groups/create', [StudyGroupController::class, 'create'])->name('groups.create');
    Route::post('/groups', [StudyGroupController::class, 'store'])->name('groups.store');
    Route::post('/groups/{group}/join', [StudyGroupController::class, 'join'])->name('groups.join');
    Route::delete('/groups/{group}/leave', [StudyGroupController::class, 'leave'])->name('groups.leave');
    
    Route::prefix('groups/{group}')->group(function () {
        Route::get('sessions',        [GroupSessionController::class, 'index'])->name('groups.sessions.index');
        Route::get('sessions/create', [GroupSessionController::class, 'create'])->name('groups.sessions.create');
        Route::post('sessions',       [GroupSessionController::class, 'store'])->name('groups.sessions.store');
        Route::get('assignments',        [AssignmentController::class, 'index'])->name('groups.assignments.index');
        Route::get('assignments/create', [AssignmentController::class, 'create'])->name('groups.assignments.create');
        Route::post('assignments',       [AssignmentController::class, 'store'])->name('groups.assignments.store');
        Route::get('assignments/{assignment}/edit', [AssignmentController::class, 'edit'])->name('groups.assignments.edit');
        Route::patch('assignments/{assignment}',      [AssignmentController::class, 'update'])->name('groups.assignments.update');
        Route::delete('assignments/{assignment}',     [AssignmentController::class, 'destroy'])->name('groups.assignments.destroy');
        Route::get('materials', [GroupMaterialController::class, 'index'])->name('groups.materials.index');
        Route::post('materials', [GroupMaterialController::class, 'store'])->name('groups.materials.store');
        Route::get('materials/{material}/download', [GroupMaterialController::class, 'download'])->name('groups.materials.download');
        Route::delete('materials/{material}', [GroupMaterialController::class, 'destroy'])->name('groups.materials.destroy');
        Route::get('/materials/{material}/preview',[GroupMaterialController::class, 'preview'])->name('groups.materials.preview');
        


    });

});

require __DIR__.'/auth.php';
