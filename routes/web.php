<?php

use Spatie\Health\Http\Controllers\HealthCheckJsonResultsController;

Route::get('health', HealthCheckJsonResultsController::class);