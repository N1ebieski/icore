<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Http\Controllers\Admin\FileManagerController;

Route::get('file-manager', [FileManagerController::class, 'index'])
    ->name('filemanager.index');
