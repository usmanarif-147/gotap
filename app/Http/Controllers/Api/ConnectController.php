<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\ConnectRequest;
use App\Http\Requests\Api\User\GetConnect;
use App\Http\Requests\SearchRequest;
use App\Models\Profile;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class ConnectController extends Controller
{

    public function connect(ConnectRequest $request)
    {
        // check connect id eual to logged in user any profile id
        $isLoggedInUserProfile = Profile::where('user_id', auth()->id())
            ->where('id', $request->connect_id)
            ->exists();
        if ($isLoggedInUserProfile) {
            return response()->json([
                'message' => 'Please enter valid connect Id'
            ]);
        }

        // check profile exist with connect id
        $isProflieExist = Profile::where('id', $request->connect_id)->exists();
        if (!$isProflieExist) {
            return response()->json([
                'message' => trans('backend.connection_not_found')
            ]);
        }

        // if not then check connection exist between logged in user and profile
        $isConnectionExist = DB::table('connects')
            ->where('connected_id', $request->connect_id)
            ->where('connecting_id', auth()->id())
            ->exists();
        if ($isConnectionExist) {
            return response()->json([
                'message' => trans('backend.already_connected')
            ]);
        }

        // if not exist then make connection between user and profile
        try {
            DB::table('connects')->insert([
                'connected_id' => $request->connect_id,
                'connecting_id' => auth()->id(),
                'created_at' => now()
            ]);
            return response()->json([
                'message' => trans('backend.connected_success')
            ]);
        } catch (Exception $ex) {
            return response()->json(['message' => $ex->getMessage()]);
        }

        // if ($request->connect_id == auth()->id()) {
        //     return response()->json([
        //         'message' => 'Please enter valid connect Id'
        //     ]);
        // }

        // check connection is valid
        // $connection = User::where('id', $request->connect_id)
        //     ->first();

        // if (!$connection) {
        //     return response()->json([
        //         'message' => trans('backend.connection_not_found')
        //     ]);
        // }

        // check
        // $connected = DB::table('connects')
        //     ->where('connected_id', $request->connect_id)
        //     ->where('connecting_id', auth()->id())
        //     ->first();
        // if ($connected) {
        //     return response()->json([
        //         'message' => trans('backend.already_connected')
        //     ]);
        // }

        // try {
        //     DB::table('connects')->insert([
        //         'connected_id' => $request->connect_id,
        //         'connecting_id' => auth()->id()
        //     ]);
        //     return response()->json([
        //         'message' => trans('backend.connected_success')
        //     ]);
        // } catch (Exception $ex) {
        //     return response()->json(['message' => $ex->getMessage()]);
        // }
    }

    public function disconnect(ConnectRequest $request)
    {

        if ($request->connect_id == auth()->id()) {
            return response()->json([
                'message' => 'Please enter valid connect Id'
            ]);
        }


        // check connection is valid
        $connection = User::where('id', $request->connect_id)
            ->first();

        if (!$connection) {
            return response()->json([
                'message' => trans('backend.connection_not_found')
            ]);
        }

        $connected = DB::table('connects')
            ->where('connected_id', $request->connect_id)
            ->where('connecting_id', auth()->id())
            ->first();
        if (!$connected) {
            return response()->json(['message' => trans('backend.not_connected')]);
        }

        try {
            DB::table('connects')
                ->where('connected_id', $request->connect_id)
                ->where('connecting_id', auth()->id())
                ->delete();
            return response()->json(['message' => trans('backend.connection_removed')]);
        } catch (Exception $ex) {
            return response()->json(['message' => $ex->getMessage()]);
        }
    }

    /**
     * Get all connections
     */
    public function getConnections(SearchRequest $request)
    {
        $searchQuery = $request->input('query', '');

        $connections = User::select(
            'profiles.id as connection_id',
            'profiles.name as connection_name',
            'profiles.email as connection_email',
            'profiles.username as connection_user_name',
            'profiles.job_title as connection_job_title',
            'profiles.company as connection_company',
            'profiles.photo as connection_photo',
        )
            ->join('connects', 'connects.connecting_id', 'users.id')
            ->join('profiles', 'profiles.id', 'connects.connected_id')
            ->where('users.id', auth()->id())
            ->when(!empty($searchQuery), function ($query) use ($searchQuery) {
                $query->where('connection.name', 'like', '%' . $searchQuery . '%');
            })
            ->get();

        $message = $connections->isEmpty() ? 'No connections found .' : 'Connections fetched successfully.';

        return response()->json([
            'message' => $message,
            'data' => $connections
        ]);
    }

    /**
     * Get connection profile
     */
    public function getConnectionProfile(GetConnect $request)
    {
        $user = User::where('username', $request->username)->first();

        if (!$user) {
            return response()->json(['message' => 'Please enter a valid username']);
        }

        if ($user->username == auth()->user()->username) {
            return response()->json([
                'message' => 'Please enter a valid username'
            ]);
        }

        $res['user'] = $user;

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
            )
            ->join('platforms', 'platforms.id', 'user_platforms.platform_id')
            ->where('user_id', $user->id)
            ->orderBy('user_platforms.platform_order')
            ->get();

        // Check if the current user is connected to the target user
        $isConnected = DB::table('connects')
            ->where('connecting_id', auth()->id())
            ->where('connected_id', $user->id)
            ->first();

        return response()->json([
            'profile' => $res['user'],
            'platforms' => $platforms,
            'is_connected' => $isConnected ? 1 : 0,
        ]);
    }
}
