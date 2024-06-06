<?php


use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
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

Route::get('/', [AuthController::class, 'index'])->name('login');
Route::post('login', [AuthController::class, 'postLogin'])->name('post.login'); 
Route::get('register', [AuthController::class, 'getRegister'])->name('get.register');
Route::post('register', [AuthController::class, 'postRegister'])->name('post.register'); 
Route::get('signout', [AuthController::class, 'signOut'])->name('signout');


Route::get('users/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard')->middleware('authUser');
Route::post('/send-message', [UserController::class, 'sendMessage'])->name('send.message');
Route::get('/get-message-list/{userId}', [UserController::class, 'getMessageList'])->name('get.message.list');
Route::post('/update-notification-count/{userId}', [UserController::class, 'updateNotificationCount'])->name('update.notification.count');
Route::post('/decrypt-message', [UserController::class,'decryptMessage'])->name('decrypt.message');
Route::post('/send-message-to-all', [UserController::class, 'sendMessageToAll'])->name('send.message.to.all');
Route::post('/send-group-message', [UserController::class, 'sendGroupMessage'])->name('send.groupmessage');

Route::post('/subscribe', [UserController::class, 'subscribe'])->name('subscribe');
