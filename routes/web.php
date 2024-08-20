<?php

use App\Http\Controllers\NotifychangepasswordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VCardController;
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
use App\Http\Livewire\Admin\Testing;
use App\Http\Livewire\Admin\User\Edit as UserEdit;
use App\Http\Livewire\Admin\User\Users;
use App\Models\Card;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

// this is my code

Route::get('/get-json-data', function () {

    $tables = [
        'admins',
        'users',
        'phone_contacts',
        'cards',
        'categories',
        'platforms',
        'connects',
        'groups',
        'group_contacts',
        'scan_visits',
        'user_cards',
        'user_groups',
        'user_platforms'
    ];

    foreach ($tables as $table) {
        $data = DB::table($table)->get()->toJson(JSON_PRETTY_PRINT);
        $filePath = database_path('gotaps/' . $table . '.json');
        File::put($filePath, $data);
    }

    dd("done");
});

Route::get('/save-json-data', function () {
    $tables = [
        'admins',
        'users',
        'phone_contacts',
        'cards',
        'categories',
        'platforms',
        'connects',
        'groups',
        'group_contacts',
        'scan_visits',
        'user_cards',
        'user_groups',
        'user_platforms'
    ];

    foreach ($tables as $table) {
        $filePath = database_path('gotaps/' . $table . '.json');

        if (File::exists($filePath)) {
            $jsonContent = File::get($filePath);
            $data = json_decode($jsonContent, true);

            for ($i = 0; $i < count($data); $i++) {
                DB::table($table)->insert($data[$i]);
            }
        } else {
            dd("File not found");
        }
    }

    dd("done");
});

Route::get('/privacy-and-policy', function () {
    return view('landingpage.privacy-policy');
})->name('privacy');

Route::get('/terms-and-conditions', function () {
    return view('landingpage.terms-conditions');
})->name('terms');


Route::get('/optimize', function () {
    Artisan::call('optimize:clear');
    dd("done");
});

Route::get('/key', function () {
    Artisan::call('key:generate');
    dd("key generated");
});

Route::get('/storage-link', function () {
    Artisan::call('storage:link');
    dd("storage linked");
});

Route::get('/middleware', function () {
    Artisan::call('make:middleware Localization');
    dd("localization done");
});

Route::get('/set-private-val', function () {
    User::where('private', 1)->update([
        'private' => 0
    ]);
});

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


// Profile using card_id
Route::get('/card_id/{uuid}', function ($uuid) {

    $user = Card::join('user_cards', 'cards.id', 'user_cards.card_id')
        ->join('users', 'users.id', 'user_cards.user_id')
        ->where('cards.uuid', $uuid)
        ->select('users.*', 'user_cards.status as card_status')
        ->first();

    if (!$user) {
        return abort(404);
    }

    if (!$user->card_status) {
        return abort(404);
    }

    $directPath = null;
    $direct = null;

    $userPlatforms = [];
    $platforms = DB::table('user_platforms')
        ->select(
            'platforms.id as platform_id',
            'platforms.title',
            'platforms.icon',
            'platforms.input',
            'platforms.baseUrl as base_url',
            'user_platforms.user_id as user_id',
            'user_platforms.created_at',
            'user_platforms.path',
            'user_platforms.label',
            'user_platforms.platform_order',
            'user_platforms.direct',
            'users.private as check_user_privacy'
        )
        ->join('platforms', 'platforms.id', 'user_platforms.platform_id')
        ->join('users', 'user_platforms.user_id', 'users.id')
        ->where('user_id', $user->id)
        ->orderBy(('user_platforms.platform_order'))
        ->get();

    if ($user->user_direct) {
        $direct = $platforms->first();
    }

    // $direct = $platforms->filter(function ($platform) {
    //     return $platform->direct == 1;
    // });

    // $direct = $direct->first();

    if ($direct) {
        if (!$direct->base_url) {
            if (!str_contains($direct->path, 'https') || !str_contains($direct->path, 'http')) {
                $directPath = 'https://' . $direct->path;
            }
        } else {
            $directPath = $direct->base_url . '/' . $direct->path;
        }
    }

    User::find($user->id)->increment('tiks');
    $is_private = User::where('id', $user->id)->first()->private;

    for ($i = 0; $i < $platforms->count(); $i++) {
        if (!$platforms[$i]->base_url) {
            if (!str_contains($platforms[$i]->path, 'https') || !str_contains($platforms[$i]->path, 'http')) {
                $platforms[$i]->base_url = 'https://';
            }
        }
        array_push($userPlatforms, $platforms[$i]);
    }

    $userPlatforms = array_chunk($userPlatforms, 4);

    return view('profile', compact('user', 'userPlatforms', 'is_private', 'directPath'));
});

// Profile using username
Route::get('/{username}', function ($username) {

    $user = User::where('username', request()->username)
        ->first();
    if (!$user) {
        return abort(404);
    }

    $directPath = null;
    $direct = null;

    $userPlatforms = [];
    $platforms = DB::table('user_platforms')
        ->select(
            'platforms.id as platform_id',
            'platforms.title',
            'platforms.icon',
            'platforms.input',
            'platforms.baseUrl as base_url',
            'user_platforms.user_id as user_id',
            'user_platforms.created_at',
            'user_platforms.path',
            'user_platforms.label',
            'user_platforms.platform_order',
            'user_platforms.direct',
            'users.private as check_user_privacy'
        )
        ->join('platforms', 'platforms.id', 'user_platforms.platform_id')
        ->join('users', 'user_platforms.user_id', 'users.id')
        ->where('user_id', $user->id)
        ->orderBy(('user_platforms.platform_order'))
        ->get();

    if ($user->user_direct) {
        $direct = $platforms->first();
    }

    // $direct = $platforms->filter(function ($platform) {
    //     return $platform->direct == 1;
    // });

    // $direct = $direct->first();

    if ($direct) {
        if (!$direct->base_url) {
            if (!str_contains($direct->path, 'https') || !str_contains($direct->path, 'http')) {
                $directPath = 'https://' . $direct->path;
            }
        } else {
            $directPath = $direct->base_url . '/' . $direct->path;
        }
    }

    User::find($user->id)->increment('tiks');
    $is_private = User::where('id', $user->id)->first()->private;

    for ($i = 0; $i < $platforms->count(); $i++) {
        if (!$platforms[$i]->base_url) {
            if (!str_contains($platforms[$i]->path, 'https') || !str_contains($platforms[$i]->path, 'http')) {
                $platforms[$i]->base_url = 'https://';
            }
        }
        array_push($userPlatforms, $platforms[$i]);
    }

    // dd($userPlatforms);

    $userPlatforms = array_chunk($userPlatforms, 4);

    return view('profile', compact('user', 'userPlatforms', 'is_private', 'directPath'));
});


Route::get('user/{id}/analytics', function ($id) {


    $userId = $id;
    $connections = DB::table('connects')->where('connecting_id', $userId)->get()->count();
    $profileViews = User::where('id', $userId)->first()->tiks;

    $platforms = DB::table('user_platforms')
        ->select(
            'platforms.id',
            'platforms.title',
            'platforms.icon',
            'user_platforms.path',
            'user_platforms.label',
            'user_platforms.clicks',
        )
        ->join('platforms', 'platforms.id', 'user_platforms.platform_id')
        ->where('user_id', $userId)
        ->orderBy(('user_platforms.platform_order'))
        ->get();


    return response()->json(
        [
            'user' => [
                [
                    'label' => trans('backend.connections'),
                    'connections' => $connections,
                    'icon' => 'uploads/photos/total_connections.png',
                ],
                [
                    'label' => trans('backend.profile_views'),
                    'profileViews' => $profileViews,
                    'icon' => 'uploads/photos/profile_views.png',
                ],
                [
                    'label' => trans('backend.platform_clicks'),
                    'total_clicks' => $platforms->sum('clicks'),
                    'icon' => 'uploads/photos/total_clicks.png',
                ],
                [
                    'label' => trans('backend.platforms'),
                    'total_platforms' => $platforms->count(),
                    'icon' => 'uploads/photos/total_platforms.png',
                ],
                [
                    'label' => trans('backend.groups'),
                    'total_groups' => Group::where('user_id', $userId)->count(),
                    'icon' => 'uploads/photos/total_groups.png',
                ],
            ],
            'platforms' => $platforms
        ]
    );
});

Route::get('save_contact/{id}', [VCardController::class, 'saveContact'])->name('save.contact');

require __DIR__ . '/auth.php';
