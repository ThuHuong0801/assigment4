<?php
use libs\Router;
Router::register('get', '',  [App\Controllers\BaseController::class, 'index']);

Router::register('get', '/post/index', [App\Controllers\PostController::class, 'index']);


