<?php

declare(strict_types=1);

use App\Http\Action\{
    NotifyAllAction,
    NotifyRoomAction,
    SendEmailAction,
    ConvertDocAction,
    UserInfoAction,
};
use Slim\App;

return static function (App $app): void {
    $app->post('/notify-room', NotifyRoomAction::class);
    $app->post('/notify-all', NotifyAllAction::class);
    $app->post('/job-email', SendEmailAction::class);
    $app->post('/job-convert', ConvertDocAction::class);
    $app->get('/user-info', UserInfoAction::class);
};

