<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserProfileController extends Controller
{

    public function index() {}


    public function getProfileByUuid()
    {
        $uuid = request()->uuid;
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
    }


    public function getProfileByUsername()
    {
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
    }
}
