<?php
use Illuminate\Support\Facades\Route;
use SiteRig\Brevo\Http\Controllers\GetFormFieldsController;

Route::name('sendinblue.')->prefix('sendinblue')->group(function () {
    Route::get('form-fields/{form}', [GetFormFieldsController::class, '__invoke'])->name('form-fields');
});
