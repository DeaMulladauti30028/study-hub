<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudyGroupController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\GroupSessionController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\GroupMaterialController;
use App\Http\Controllers\GroupAssignmentController;
use App\Http\Controllers\ContributionController;
use App\Http\Controllers\ContributionCommentController;
use App\Http\Controllers\GroupModeratorController;
use App\Http\Controllers\ContributionEndorseController;
use App\Http\Controllers\GroupMaterialPinController;




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
    Route::get('/groups/{group}', [StudyGroupController::class, 'show'])->name('groups.show');
    Route::post('/groups', [StudyGroupController::class, 'store'])->name('groups.store');
    Route::post('/groups/{group}/join', [StudyGroupController::class, 'join'])->name('groups.join');
    Route::delete('/groups/{group}/leave', [StudyGroupController::class, 'leave'])->name('groups.leave');
    
    Route::prefix('groups/{group}')->group(function () {
        Route::get('sessions',        [GroupSessionController::class, 'index'])->name('groups.sessions.index');
        Route::get('sessions/create', [GroupSessionController::class, 'create'])->name('groups.sessions.create');
        Route::post('sessions',       [GroupSessionController::class, 'store'])->name('groups.sessions.store');
        
        Route::get('materials', [GroupMaterialController::class, 'index'])->name('groups.materials.index');
        Route::post('materials', [GroupMaterialController::class, 'store'])->name('groups.materials.store');
        Route::get('materials/{material}/download', [GroupMaterialController::class, 'download'])->name('groups.materials.download');
        Route::delete('materials/{material}', [GroupMaterialController::class, 'destroy'])->name('groups.materials.destroy');
        Route::get('materials/{material}/preview',[GroupMaterialController::class, 'preview'])->name('groups.materials.preview');
    
        Route::get('tasks',                         [GroupAssignmentController::class, 'index'])->name('groups.tasks.index');
        Route::post('tasks/{assignment}/toggle',    [GroupAssignmentController::class, 'toggleDone'])->name('groups.tasks.toggle');
        Route::get('tasks/create',                  [GroupAssignmentController::class, 'create'])->name('groups.tasks.create');
        Route::post('tasks',                        [GroupAssignmentController::class, 'store'])->name('groups.tasks.store');
        Route::get('tasks/{assignment}/edit',       [GroupAssignmentController::class, 'edit'])->name('groups.tasks.edit');
        Route::put('tasks/{assignment}',            [GroupAssignmentController::class, 'update'])->name('groups.tasks.update');
        Route::delete('tasks/{assignment}',         [GroupAssignmentController::class, 'destroy'])->name('groups.tasks.destroy');
    
        Route::get('contributions',                         [ContributionController::class, 'index'])->name('groups.contributions.index');
        Route::get('contributions/create',                  [ContributionController::class, 'create'])->name('groups.contributions.create');
        Route::post('contributions',                        [ContributionController::class, 'store'])->name('groups.contributions.store');
        Route::get('contributions/{contribution}',          [ContributionController::class, 'show'])->name('groups.contributions.show');
        Route::get('contributions/{contribution}/file',     [ContributionController::class, 'file'])->name('groups.contributions.file');
        Route::get('contributions/{contribution}/edit',     [ContributionController::class, 'edit'])->name('groups.contributions.edit');
        Route::put('contributions/{contribution}',          [ContributionController::class, 'update'])->name('groups.contributions.update');
        Route::delete('contributions/{contribution}',       [ContributionController::class, 'destroy'])->name('groups.contributions.destroy');
        Route::post('contributions/{contribution}/helpful', [ContributionController::class, 'toggleHelpful'])->name('groups.contributions.helpful.toggle');
    

        Route::post('contributions/{contribution}/comments', [ContributionCommentController::class, 'store'])->name('groups.contributions.comments.store');

        Route::delete('contributions/{contribution}/comments/{comment}', [ContributionCommentController::class, 'destroy'])->name('groups.contributions.comments.destroy');
        Route::post('contributions/{contribution}/endorse',  [ContributionEndorseController::class, 'store'])->name('contributions.endorse');
        Route::delete('contributions/{contribution}/endorse', [ContributionEndorseController::class, 'destroy'])->name('contributions.unendorse');
    

        Route::post('moderators/{user}/promote', [GroupModeratorController::class, 'promote'])->name('groups.moderators.promote');
        Route::post('moderators/{user}/demote',  [GroupModeratorController::class, 'demote'])->name('groups.moderators.demote');

        Route::post('materials/{material}/pin',  [GroupMaterialPinController::class, 'store'])->name('groups.materials.pin');
        Route::delete('materials/{material}/pin', [GroupMaterialPinController::class, 'destroy'])->name('groups.materials.unpin');


    });
    

});

require __DIR__.'/auth.php';
