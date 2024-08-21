<?php

use App\Http\Controllers\NotifychangepasswordController;
use App\Http\Controllers\ProfileController;
use App\Http\Livewire\Admin\Card\Cards;
use App\Http\Livewire\Admin\Card\Create as CardCreate;
use App\Http\Livewire\Admin\Card\Edit as CardEdit;
use App\Http\Livewire\Admin\Category\Categoies;
use App\Http\Livewire\Admin\Category\Create as CategoryCreate;
use App\Http\Livewire\Admin\Category\Edit as CategoryEdit;
use App\Http\Livewire\Admin\Dashboard;
use App\Http\Livewire\Admin\Logs;
use App\Http\Livewire\Admin\Platform\Create as PlatformCreate;
use App\Http\Livewire\Admin\Platform\Edit as PlatformEdit;
use App\Http\Livewire\Admin\Platform\Platforms;
use App\Http\Livewire\Admin\User\Edit as UserEdit;
use App\Http\Livewire\Admin\User\Users;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:admin')->group(function () {

    Route::get('admin/dashboard', Dashboard::class);

    // users
    Route::get('admin/users', Users::class);
    Route::get('admin/user/{id}/edit', UserEdit::class);

    // categories
    Route::get('admin/categories', Categoies::class);
    Route::get('admin/category/create', CategoryCreate::class);
    Route::get('admin/category/{id}/edit', CategoryEdit::class);

    // platforms
    Route::get('admin/platforms', Platforms::class);
    Route::get('admin/platform/create', PlatformCreate::class);
    Route::get('admin/platform/{id}/edit', PlatformEdit::class);

    // categories
    Route::get('admin/cards', Cards::class);
    Route::get('admin/card/create', CardCreate::class);
    Route::get('admin/card/{id}/edit', CardEdit::class);
    Route::get('/downloadCardsCSV', [Cards::class, 'downloadCsv'])->name('export');

    // logs
    Route::get('admin/logs', Logs::class);

    // profile
    Route::post('/changePassword', [ProfileController::class, 'changePassword'])->name('profile.change.password');

    //change password
    Route::get('/change-passwords', [NotifychangepasswordController::class, 'resetAllPasswords']);
});
