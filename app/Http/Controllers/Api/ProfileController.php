<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Profile\AddProfileRequest;
use App\Http\Requests\Api\Profile\UpdateProfileRequest;
use App\Http\Requests\SearchRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Api\UserProfileResource;
use App\Http\Resources\Api\UserResource;
use App\Models\Group;
use App\Models\Profile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{

    public function index()
    {

        $user = User::where('id', auth()->id())->first();

        return response()->json(
            [
                'message' => 'User Profiles',
                'data' => new UserResource($user),
            ]
        );
    }

    public function profile()
    {
        $profile = getActiveProfile();
        $platforms = DB::table('user_platforms')
            ->select(
                'platforms.id',
                'platforms.title',
                'platforms.icon',
                'platforms.input',
                'platforms.baseUrl',
                'user_platforms.created_at',
                'user_platforms.path',
                'user_platforms.label',
                'user_platforms.platform_order',
                'user_platforms.direct',
                'user_platforms.profile_id'
            )
            ->join('platforms', 'platforms.id', '=', 'user_platforms.platform_id')
            ->where('user_id', auth()->id())
            ->where('profile_id', $profile->id)
            ->orderBy('user_platforms.platform_order')
            ->get();

        return response()->json(
            [
                'profile' => new UserProfileResource($profile),
                // 'platforms' => PlatformResource::collection($platforms)
            ]
        );
    }

    public function switchProfile(Request $request)
    {
        $request->validate([
            'profile_id' => ['required']
        ], [
            'profile_id.required' => 'Please enter valid Profile Id'
        ]);

        $exist = Profile::where('user_id', auth()->id())
            ->where('id', $request->profile_id)
            ->exists();
        if (!$exist) {
            return response()->json([
                'message' => 'Profile Does not exist'
            ]);
        }

        $active = Profile::where('user_id', auth()->id())
            ->where('id', $request->profile_id)
            ->first()
            ->active;
        if ($active) {
            return response()->json([
                'message' => 'Profile is Already Active'
            ]);
        }

        Profile::where('user_id', auth()->id())->update([
            'active' => 0
        ]);

        Profile::where('user_id', auth()->id())
            ->where('id', $request->profile_id)
            ->update([
                'active' => 1
            ]);

        return response()->json([
            'message' => 'Profile Switched Successfully'
        ]);
    }

    public function addProfile(AddProfileRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = [];
            if ($request->hasFile('photo')) {
                $data['photo'] = Storage::disk('public')->put('/uploads/photos', $request->photo);
            }
            if ($request->hasFile('cover_photo')) {
                $data['cover_photo'] = Storage::disk('public')->put('/uploads/coverPhotos', $request->cover_photo);
            }

            $data['user_id'] = auth()->id();
            $data['name'] = $request->name;
            $data['email'] = $request->email;
            $data['username'] = $request->username;
            $data['work_position'] = $request->work_position;
            $data['phone'] = $request->phone;
            $data['job_title'] = $request->job_title;
            $data['company'] = $request->company;
            $data['address'] = $request->address;
            $data['bio'] = $request->bio;
            $data['active'] = 1;

            Profile::where('user_id', auth()->id())->update(['active' => 0]);

            $profile = Profile::create($data);

            Group::create([
                'user_id' => $profile->user_id,
                'profile_id' => $profile->id,
                'title' => 'favourites',
            ]);

            Group::create([
                'user_id' => $profile->user_id,
                'profile_id' => $profile->id,
                'title' => 'scanned card',
            ]);

            DB::commit();

            return response()->json([
                'message' => trans('profile created successfully'),
                'data' => new UserProfileResource($profile)
            ]);
        } catch (Exception $ex) {

            DB::rollBack();
            return response()->json([
                'message' => $ex->getMessage(),
            ]);
        }
    }


    public function update(UpdateProfileRequest $request)
    {
        try {
            $cover_photo = auth()->user()->cover_photo;
            $photo = auth()->user()->photo;

            if ($request->hasFile('cover_photo')) {
                if ($cover_photo) {
                    Storage::disk('public')->delete($cover_photo);
                }
                $cover_photo = Storage::disk('public')->put('uploads/coverPhotos', $request->cover_photo);
            }

            if ($request->hasFile('photo')) {
                if ($photo) {
                    Storage::disk('public')->delete($photo);
                }
                $photo = Storage::disk('public')->put('uploads/photos', $request->photo);
            }

            // $user = User::where('id', auth()->id())
            //     ->first();

            // $isUpdated = User::where('id', auth()->id())
            //     ->update([
            //         'name' => $request->name,
            //     ]);

            $profile = getActiveProfile();

            $isUpdated = Profile::where('id', $profile->id)
                ->where('user_id', auth()->id())
                ->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'username' => $request->username,
                    'phone' => $request->phone,
                    'work_position' => $request->work_position,
                    'job_title' => $request->job_title,
                    'company' => $request->company,
                    'address' => $request->address,
                    'bio' => $request->bio,
                    'cover_photo' => $cover_photo,
                    'photo' => $photo,
                ]);

            if (!$isUpdated) {
                return response()->json([
                    'message' => trans('backend.profile_updated_failed')
                ]);
            }

            $user = User::where('id', auth()->id())->get()->first();
            $profile = Profile::where('id', $profile->id)
                ->where('user_id', auth()->id())
                ->get()
                ->first();

            return response()->json([
                'message' => trans('backend.profile_updated_success'),
                'user' => new UserResource($user),
                'profile' => new UserProfileResource($profile)
            ]);
        } catch (Exception $ex) {
            return response()->json([
                'message' => $ex->getMessage()
            ]);
        }
    }

    public function userDirect()
    {
        $profile = getActiveProfile();
        if ($profile->user_direct) {
            Profile::where('id', $profile->id)
                ->update(
                    [
                        'user_direct' => 0
                    ]
                );

            $profile = getActiveProfile();

            return response()->json(
                [
                    'message' => trans('backend.platform_set_public'),
                    'profile' => new UserProfileResource($profile)
                ]
            );
        }

        Profile::where('id', $profile->id)
            ->update(
                [
                    'user_direct' => 1
                ]
            );
        $profile = getActiveProfile();
        return response()->json(
            [
                'message' => trans('backend.first_platform_public'),
                'profile' => new UserProfileResource($profile)
            ]
        );
    }

    public function privateProfile()
    {

        $status = auth()->user()->private ? 'Public' : 'Private';

        User::where('id', auth()->id())
            ->update(
                [
                    'user_direct' => auth()->user()->user_direct ? 0 : 1
                ]
            );

        return response()->json(['message' => trans('backend.profile_set_to') . $status, 'data' => auth()->user()]);
    }

    public function search(SearchRequest $request)
    {
        $searchQuery = $request->input('query', '');

        if (empty($searchQuery)) {
            $users = User::all();
            $message = 'All users fetched successfully.';
        } else {
            $users = User::where('name', 'like', '%' . $searchQuery . '%')->get();
            if ($users->isEmpty()) {
                $message = 'No users found.';
            } else {
                $message = 'Users have been found.';
            }
        }

        return response()->json([
            'message' => $message,
            'data' => $users
        ]);
    }
}
