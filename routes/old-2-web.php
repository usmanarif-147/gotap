<?php

use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\VCardController;
use App\Models\Connect;
use App\Models\done\Card;
use App\Models\done\Category;
use App\Models\done\Group;
use App\Models\done\PhoneContact;
use App\Models\done\Platform;
use App\Models\done\Profile;
use App\Models\done\User;
use App\Models\done\UserPlatform;
use App\Models\done\UserCard;
use App\Models\done\GroupContacts;
use App\Models\UserGroups;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;



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

Route::get('/save-users', function () {
    $filePath = database_path('gotaps/users.json');

    if (File::exists($filePath)) {
        $jsonContent = File::get($filePath);
        $data = json_decode($jsonContent, true);

        foreach ($data as $user) {
            $userData = User::create([
                "old_id" => $user['id'],
                "name" => $user['name'],
                "email" => $user['email'],
                "username" => $user['username'],
                "status" => $user['status'],
                "is_suspended" => $user['is_suspended'],
                "password" => $user['password'],
                "address" => $user['address'],
                "gender" => $user['gender'],
                "tiks" => $user['tiks'],
                "dob" => $user['dob'],
                "verified" => $user['verified'],
                "featured" => $user['featured'],
                "fcm_token" => $user['fcm_token'],
                "deactivated_at" => $user['deactivated_at'],
                "is_email_sent" => $user['is_email_sent'],
                "created_at" => $user['created_at'],
                "updated_at" => $user['updated_at'],
            ]);

            Profile::create([
                "user_id" => $userData->id,
                "name" => $user['name'],
                "email" => $user['email'],
                "username" => $user['username'],
                "work_position" => $user['work_position'],
                "job_title" => $user['job_title'],
                "company" => $user['company'],
                "address" => $user['address'],
                "bio" => $user['bio'],
                "phone" => $user['phone'],
                "photo" => $user['photo'],
                "cover_photo" => $user['cover_photo'],
                "active" => 1,
                "user_direct" => $user['user_direct'],
                "tiks" => $user['tiks'],
                "private" => $user['private'],
                "created_at" => $user['created_at'],
                "updated_at" => $user['updated_at'],
            ]);
        }
    } else {
        dd("File not found");
    }


    dd("users saved done");
});

Route::get('/save-phone-contacts', function () {
    $table = 'phone_contacts';
    $filePath = database_path('gotaps/' . $table . '.json');
    if (File::exists($filePath)) {
        $jsonContent = File::get($filePath);
        $data = json_decode($jsonContent, true);
        foreach ($data as $phone) {
            $user = User::where('old_id', $phone['user_id'])->first();
            $phone['user_id'] = $user->id;
            $phone['old_phone_contact_id'] = $phone['id'];

            PhoneContact::create($phone);
        }
    } else {
        dd("File not found");
    }

    dd("phone contacts saved");
});

Route::get('/save-cards', function () {
    $table = 'cards';
    $filePath = database_path('gotaps/' . $table . '.json');
    if (File::exists($filePath)) {
        $jsonContent = File::get($filePath);
        $data = json_decode($jsonContent, true);

        foreach ($data as $card) {
            $card['old_card_id'] = $card['id'];
            Card::create($card);
        }
    } else {
        dd("File not found");
    }

    dd("cards saved");
});

Route::get('/save-categories', function () {
    $table = 'categories';
    $filePath = database_path('gotaps/' . $table . '.json');
    if (File::exists($filePath)) {
        $jsonContent = File::get($filePath);
        $data = json_decode($jsonContent, true);
        foreach ($data as $cat) {
            $cat['old_category_id'] = $cat['id'];
            // dd($cat);
            Category::create($cat);
        }
    } else {
        dd("File not found");
    }

    dd("categories saved");
});

Route::get('/save-cat-platforms', function () {
    $table = 'platforms';
    $filePath = database_path('gotaps/' . $table . '.json');
    if (File::exists($filePath)) {
        $jsonContent = File::get($filePath);
        $data = json_decode($jsonContent, true);
        foreach ($data as $platform) {
            $category = Category::where('old_category_id', $platform['category_id'])->first();
            $platform['old_platform_id'] = $platform['id'];
            $platform['category_id'] = $category->id;
            Platform::create($platform);
        }
    } else {
        dd("File not found");
    }

    dd("platforms saved");
});

Route::get('/save-groups', function () {
    $table = 'groups';
    $filePath = database_path('gotaps/' . $table . '.json');
    if (File::exists($filePath)) {
        $jsonContent = File::get($filePath);
        $data = json_decode($jsonContent, true);
        foreach ($data as $group) {
            $user = User::where('old_id', $group['user_id'])->first();
            $group['user_id'] = $user->id;
            $group['old_group_id'] = $group['id'];
            $group['total_profiles'] = $group['total_members'];
            Group::create($group);
        }
    } else {
        dd("File not found");
    }

    dd("groups saved");
});

Route::get('/save-user-platforms', function () {
    $table = 'user_platforms';
    $filePath = database_path('gotaps/' . $table . '.json');
    if (File::exists($filePath)) {
        $jsonContent = File::get($filePath);
        $data = json_decode($jsonContent, true);

        foreach ($data as $up) {
            $user = User::where('old_id', $up['user_id'])->first();
            $profile = Profile::where('user_id', $user->id)->first();
            $platform = Platform::where('old_platform_id', $up['platform_id'])->first();

            $up['user_id'] = $user->id;
            $up['profile_id'] = $profile->id;
            $up['platform_id'] = $platform->id;

            UserPlatform::create($up);
        }
    } else {
        dd("File not found");
    }

    dd("user platforms saved");
});

Route::get('/save-user-cards', function () {
    $table = 'user_cards';
    $filePath = database_path('gotaps/' . $table . '.json');
    if (File::exists($filePath)) {
        $jsonContent = File::get($filePath);
        $data = json_decode($jsonContent, true);

        foreach ($data as $userCard) {

            $user = User::where('old_id', $userCard['user_id'])->first();
            $profile = Profile::where('user_id', $user->id)->first();
            $card = Card::where('old_card_id', $userCard['card_id'])->first();

            $userCard['user_id'] = $user->id;
            $userCard['profile_id'] = $profile->id;
            $userCard['card_id'] = $card->id;

            UserCard::create($userCard);
        }
    } else {
        dd("File not found");
    }

    dd("user cards saved");
});

Route::get('/save-user-connects', function () {
    $table = 'connects';
    $filePath = database_path('gotaps/' . $table . '.json');
    if (File::exists($filePath)) {
        $jsonContent = File::get($filePath);
        $data = json_decode($jsonContent, true);

        foreach ($data as $connect) {

            // dd($connect);
            $user = User::where('old_id', $connect['connecting_id'])->first();
            $connectedUser = User::where('old_id', $connect['connected_id'])->first();
            $profile = Profile::where('user_id', $connectedUser->id)->first();

            $connect['connecting_id'] = $user->id;
            $connect['connected_id'] = $profile->id;

            Connect::create($connect);

            // UserCard::create($userCard);
        }
    } else {
        dd("File not found");
    }

    dd("user connects saved");
});

Route::get('/save-group-contacts', function () {
    $table = 'group_contacts';
    $filePath = database_path('gotaps/' . $table . '.json');
    if (File::exists($filePath)) {
        $jsonContent = File::get($filePath);
        $data = json_decode($jsonContent, true);

        foreach ($data as $gc) {

            $group = Group::where('old_group_id', $gc['group_id'])->first();
            $contact = PhoneContact::where('old_phone_contact_id', $gc['contact_id'])->first();

            $gc['contact_id'] = $contact->id;
            $gc['group_id'] = $group->id;

            GroupContacts::create($gc);
        }
    } else {
        dd("File not found");
    }

    dd("user group contacts saved");
});

Route::get('/save-group-contacts', function () {
    $table = 'group_contacts';
    $filePath = database_path('gotaps/' . $table . '.json');
    if (File::exists($filePath)) {
        $jsonContent = File::get($filePath);
        $data = json_decode($jsonContent, true);

        foreach ($data as $gc) {

            $group = Group::where('old_group_id', $gc['group_id'])->first();
            $contact = PhoneContact::where('old_phone_contact_id', $gc['contact_id'])->first();

            $gc['contact_id'] = $contact->id;
            $gc['group_id'] = $group->id;

            GroupContacts::create($gc);
        }
    } else {
        dd("File not found");
    }

    dd("user group contacts saved");
});

Route::get('/save-user-groups', function () {
    $table = 'user_groups';
    $filePath = database_path('gotaps/' . $table . '.json');
    if (File::exists($filePath)) {
        $jsonContent = File::get($filePath);
        $data = json_decode($jsonContent, true);

        foreach ($data as $ug) {

            $group = Group::where('old_group_id', $ug['group_id'])->first();
            $user = User::where('old_id', $ug['user_id'])->first();
            $profile = Profile::where('user_id', $user->id)->first();

            $ug['group_id'] = $group->id;
            $ug['profile_id'] = $profile->id;

            UserGroups::create($ug);
        }
    } else {
        dd("File not found");
    }

    dd("user groups saved");
});

// Route::get('/save-without-profile', function () {
//     $tables = [
//         'admins',
//         'phone_contacts',
//         'cards',
//         'categories',
//         'platforms',
//         'groups',
//         'group_contacts',
//         'scan_visits'
//     ];

//     foreach ($tables as $table) {
//         $filePath = database_path('gotaps/' . $table . '.json');

//         if (File::exists($filePath)) {
//             $jsonContent = File::get($filePath);
//             $data = json_decode($jsonContent, true);

//             for ($i = 0; $i < count($data); $i++) {
//                 DB::table($table)->insert($data[$i]);
//             }
//         } else {
//             dd("File not found");
//         }
//     }

//     dd("without profile done");
// });

// Route::get('/save-connects', function () {
//     $tables = [
//         'connects',
//         'user_cards',
//         'user_groups',
//         'user_platforms'
//     ];

//     foreach ($tables as $table) {
//         $filePath = database_path('gotaps/' . $table . '.json');

//         if (File::exists($filePath)) {
//             $jsonContent = File::get($filePath);
//             $data = json_decode($jsonContent, true);

//             for ($i = 0; $i < count($data); $i++) {
//                 DB::table($table)->insert($data[$i]);
//             }
//         } else {
//             dd("File not found");
//         }
//     }

//     dd("done");
// });









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


// Profile using card_id
Route::get('/card_id/{uuid}', [UserProfileController::class, 'index']);

// Profile using username
Route::get('/{username}', [UserProfileController::class, 'index']);


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
