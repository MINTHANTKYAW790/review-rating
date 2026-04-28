<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::routes(['middleware' => ['auth']]);

Broadcast::channel('user.{userId}.reviews', function ($user, int $userId) {
    return (int) $user->id === $userId;
});
