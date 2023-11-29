<?php

use App\Http\Controllers\TutorialController;
use App\Http\Controllers\AkunController;
use App\Http\Controllers\FeedbackController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::group(['prefix' => 'dashboard/admin'], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::group(['prefix' => 'profile'], function () {
        Route::get('/', [HomeController::class, 'profile'])->name('profile');
        Route::post('update', [HomeController::class, 'updateprofile'])->name('profile.update');
    });

    Route::controller(AkunController::class)
        ->prefix('akun')
        ->as('akun.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('showdata', 'dataTable')->name('dataTable');
            Route::match(['get','post'],'tambah', 'tambahAkun')->name('add');
            Route::match(['get','post'],'{id}/ubah', 'ubahAkun')->name('edit');
            Route::delete('{id}/hapus', 'hapusAkun')->name('delete');
            Route::get('/getUserId', 'getUserId')->name('getUserId');
        });
    Route::controller(TutorialController::class)
        ->prefix('tutorial')
        ->as('tutorial.')
        ->group(function (){
            Route::get('tutorial', 'index')->name('index');
            Route::get('tutorial/editPage', 'editTutorialPage')->name('editPage');
            Route::get('tutorialDetail/{id}', [TutorialController::class, 'showDetail'])->name('tutorialDetail');
            Route::get('tutorial/dataTutorial', [TutorialController::class, 'getDataTutorial'])->name('dataTutorial');
            Route::get('/viewTambah', 'addTutorialPage')->name('tambah');
            Route::post('tambahTutorial', 'store')->name('store');
            Route::post('{id}/edit', 'editTutorial')->name('edit');
            Route::delete('{id}/delete', 'deleteTutorial')->name('delete');
        });
    // Route::controller(FeedbackController::class)
    //     ->prefix('feedback')
    //     ->as('feedback.')
    //     ->group(function (){
    //         Route::get('feedback', 'index')->name('index');
    //         Route::get('feedback/editPage', 'editFeedbackPage')->name('editPage');
    //         Route::get('feedbackDetail/{id}', [TutorialController::class, 'showDetail'])->name('feedbackDetail');
    //         Route::get('feedback/dataFeedback', [TutorialController::class, 'getDataFeedback'])->name('dataFeedback');
    //         Route::get('/viewTambah', 'addFeedbackPage')->name('tambah');
    //         Route::post('tambahFeedback', 'store')->name('store');
    //         Route::post('{id}/edit', 'editFeedback')->name('edit');
    //         Route::delete('{id}/delete', 'deleteFeedback')->name('delete');
    //     });
        
});
