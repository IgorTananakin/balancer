<?php

use Illuminate\Support\Facades\Route;

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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/login', function () {
    return view('app', [
        'isLoginPage' => true,
        'isHistoryPage' => false
    ]);
});

Route::get('/history/{email}', function ($email) {
    return view('app', [
        'userEmail' => $email,
        'isHistoryPage' => true, // Флаг страница истории
        'isLoginPage' => false
    ]);
})->where('email', '.*');

Route::get('/{email?}', function ($email = null) {
    // заглушка не забыть убрать
    if (!$email) {
        $email = 'ivan@example.com';
    }
    return view('app', ['userEmail' => $email, 'isHistoryPage' => false, 'isLoginPage' => false]);
})->where('email', '.*'); // Разрешаем любые символы 

// Route::get('/{any?}', function () {
//     return view('app');
// })->where('any', '.*');
