<?php
require __DIR__.'/../../bootstrap/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->boot();
$app->make('Illuminate\Contracts\Http\Kernel')
    ->handle(Illuminate\Http\Request::capture());