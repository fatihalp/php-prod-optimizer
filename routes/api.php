<?php

use Illuminate\Support\Facades\Route;
use Modules\SystemInfo\Http\Controllers\SystemInfoController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('systeminfos', SystemInfoController::class)->names('systeminfo');
});
