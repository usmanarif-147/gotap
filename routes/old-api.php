<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CardController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\PhoneContactController;
use App\Http\Controllers\Api\PlatformController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ConnectController;
use App\Http\Controllers\Api\ViewProfileController;
use Illuminate\Support\Facades\Route;
use App\Models\Card;

Route::post('/platformClick', [PlatformController::class, 'incrementClick'])->name('inc.platform.click');
Route::get('/getCards', function () {
    return Card::select(
        'id',
        'uuid',
        'status'
    )
        ->where('status', 0)
        ->get()->toArray();
});

Route::middleware('localization')->group(function () {

    Route::post('register', [AuthController::class, 'register'])->middleware(['throttle:6,1']);
    Route::post('login', [AuthController::class, 'login'])->middleware(['throttle:6,1']);
    Route::post('forgetPassword', [AuthController::class, 'forgotPassword'])->middleware(['throttle:6,1']);
    Route::post('resetPassword', [AuthController::class, 'resetPassword']);
    Route::post('/recoverAccount', [AuthController::class, 'recoverAccount']);

    Route::post('/otpVerification', [AuthController::class, 'otpVerify']);

    Route::middleware('auth:sanctum')->group(function () {

        Route::middleware('user.status')->group(function () {

            // User Profile
            Route::get('/getProfiles', [ProfileController::class, 'index']);  // user id
            Route::get('/profile', [ProfileController::class, 'profile']);   // user id
            Route::post('/addProfile', [ProfileController::class, 'addProfile']);  // user id
            Route::post('/switchProfile', [ProfileController::class, 'switchProfile']);  // user id
            Route::post('/updateProfile', [ProfileController::class, 'update']);  // user id
            Route::get('/userDirect', [ProfileController::class, 'userDirect']);
            Route::get('/search', [ProfileController::class, 'search']);

            // Platform
            Route::get('/categories', [CategoryController::class, 'index']);   // profile id
            // Route::post('/searchPlatform', [PlatformController::class, 'search']);
            Route::post('/addPlatform', [PlatformController::class, 'add']);  // profile id
            Route::post('/removePlatform', [PlatformController::class, 'remove']);  // profile id reuired
            Route::post('/swapOrder', [PlatformController::class, 'swap']);  // profile id
            // Route::post('/platformDirect', [PlatformController::class, 'direct']);
            // Route::post('/platformDetails', [PlatformController::class, 'details']);

            // Phone Contact
            Route::get('/phoneContacts', [PhoneContactController::class, 'index']);  // user id
            Route::post('/addPhoneContact', [PhoneContactController::class, 'add']); // user id
            Route::post('/phoneContact', [PhoneContactController::class, 'phoneContact']); // user id
            Route::post('/updatePhoneContact', [PhoneContactController::class, 'update']); // user id
            Route::post('/removeContact', [PhoneContactController::class, 'remove']);

            // Group
            Route::get('/groups', [GroupController::class, 'index']);  // user id
            Route::post('/group', [GroupController::class, 'group']);  // user id
            Route::post('/addGroup', [GroupController::class, 'add']);  // user id
            Route::post('/updateGroup', [GroupController::class, 'update']); // user id
            Route::post('/removeGroup', [GroupController::class, 'destroy']); // user id

            Route::post('/addUserIntoGroup', [GroupController::class, 'addUser']); // user id
            Route::post('/addContactIntoGroup', [GroupController::class, 'addContact']);  // user id
            Route::post('/removeUserFromGroup', [GroupController::class, 'removeUser']);  // user id
            Route::post('/removeContactFromGroup', [GroupController::class, 'removeContact']);  // user id

            // User
            // Route::post('/connect', [UserController::class, 'connect']);
            Route::get('/analytics', [UserController::class, 'analytics']);  // profile id
            Route::post('/privateProfile', [UserController::class, 'privateProfile']);  // profile id

            Route::get('/deactivateAccount', [UserController::class, 'deactivateAccount']);  // user id
            Route::get('/delete', [UserController::class, 'deleteAccount']);  // user id


            // Cards
            Route::get('/cards', [CardController::class, 'index']);  // profile id
            Route::post('/activateCard', [CardController::class, 'activateCard']);  // profile id
            Route::post('/changeCardStatus', [CardController::class, 'changeCardStatus']);  // profile id
            Route::post('/cardProfileDetail', [CardController::class, 'cardProfileDetail']);  // profile id

            // View User Profile
            Route::post('/viewUserProfile', [ViewProfileController::class, 'viewUserProfile']);  // profile

            // Connects
            Route::post('/connect', [ConnectController::class, 'connect']);  // user id
            Route::post('/disconnect', [ConnectController::class, 'disconnect']);  // user id
            Route::post('/connectionProfile', [ConnectController::class, 'getConnectionProfile']);  // user id
            Route::get('/connections', [ConnectController::class, 'getConnections']);  // user id

            // Change Password
            Route::post('/change-password', [AuthController::class, 'changePassword']);  // user id

            // Random
            Route::get('/groupDetail/{num}', [GroupController::class, 'groupDetail']);
        });
        Route::get('logout', [AuthController::class, 'logout']);
    });
});
