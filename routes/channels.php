<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('user.{receiverId}', function ($user, $receiverId) {

    return $user->id == $receiverId;
});
Broadcast::channel('group.{receiverId}', function ($user,$receiverId) {

    return $user->id == $receiverId;
});
Broadcast::channel('allUser.{receiverId}', function ($user, $receiverId) {

    return $user->id == $receiverId;
});
