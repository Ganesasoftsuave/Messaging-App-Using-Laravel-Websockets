<?php


use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessageController;
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


Route::post('/send-message', [MessageController::class, 'sendMessageToSingleUser'])->name('send.message.to.single.user');
Route::post('/send-message-to-all', [MessageController::class, 'sendMessageToAll'])->name('send.message.to.all');
Route::post('/send-message-to-group', [MessageController::class, 'sendMessageToGroup'])->name('send.message.to.group');
Route::post('/decrypt-message', [MessageController::class,'decryptMessage'])->name('decrypt.message');


Route::get('users/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard')->middleware('authUser');
Route::get('/get-message-list/{userId}', [UserController::class, 'getMessageList'])->name('get.message.list');
Route::post('/update-notification-count/{userId}', [UserController::class, 'updateNotificationCount'])->name('update.notification.count');
Route::post('/subscribe', [UserController::class, 'subscribe'])->name('subscribe');
