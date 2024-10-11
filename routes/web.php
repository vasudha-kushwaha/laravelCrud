<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('v4')->group(function(){
    Route::prefix('student')->group(function(){
        // Route::get('/list', [StudentController::Class,'List'])->name('listStudent'); 
        Route::get('/list/{range?}/{listView?}', [StudentController::Class,'List'])->name('listStudent');
        Route::get('/detail/{id}', [StudentController::Class,'Detail'])->name('detailStudent');
        Route::any('/add', [StudentController::Class,'Add'])->name('addStudent');
        Route::post('/save', [StudentController::Class,'Add'])->name('saveStudent');
        Route::any('/edit/{id}', [StudentController::Class,'Edit'])->name('editStudent');
        Route::put('/update', [StudentController::Class,'Update'])->name('updateStudent');    
    }); 
    // Route::prefix('subject')->group(function(){
    //     Route::get('/test', function () {
    //         echo "inside subject";
    //     });
    //     Route::get('/list', [StudentController::Class,'List'])->name('listStudent');
    //     Route::get('/detail/{id}', [StudentController::Class,'Detail'])->name('detailStudent');
    //     Route::post('/add', [StudentController::Class,'Add'])->name('addStudent');
    //     Route::any('/edit/{id}', [StudentController::Class,'Edit'])->name('editStudent');
    //     Route::put('/update', [StudentController::Class,'Update'])->name('updateStudent');    
    // }); 
});
