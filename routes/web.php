<?php

use Illuminate\Support\Facades\Route;
use Modules\SystemInfo\Http\Controllers\SystemInfoController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('systeminfos', SystemInfoController::class)->names('systeminfo');
});
