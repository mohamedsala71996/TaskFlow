<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    Artisan::call('storage:link'); // Fixing the Artisan call syntax
    Artisan::call('cache:clear'); // Fixing the Artisan call syntax
    // Artisan::call('migrate'); // Fixing the Artisan call syntax
    Artisan::call('route:cache'); // Fixing the Artisan call syntax
    Artisan::call('view:clear'); // Fixing the Artisan call syntax
    Artisan::call('optimize:clear'); // Fixing the Artisan call syntax
    // Artisan::call('cache:clear'); // Fixing the Artisan call syntax
    return view('welcome');
});

// Route::get('/{any}', function () {
//     return view('index'); // or point to your React index file
// })->where('any', '.*');
