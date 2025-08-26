<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('tasks', function ($user) {
    return true;//!is_null($user);
});
Broadcast::channel('requests', function ($user) {
    return true;//!is_null($user);
});

Broadcast::channel('company-setup', function ($user) {
    return true;//!is_null($user);
});

Broadcast::channel('client-request', function ($user) {
    return true;//!is_null($user);
});
