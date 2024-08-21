<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Profile\ViewProfileRequest;
use App\Http\Resources\Api\PlatformResource;
use App\Models\Card;
use App\Models\Profile;
use App\Models\User;
use App\Services\CategoryService;
use Illuminate\Support\Facades\DB;

class ViewProfileController extends Controller
{

    public function viewUserProfile(ViewProfileRequest $request)
    {
        $res['profile'] = null;

        $card = Card::where('uuid', $request->search_profile_by)->first();
        if (!$card) {
            $res['profile'] = Profile::where('username', $request->search_profile_by)->first();
            if (!$res['profile']) {
                $res['profile'] = Profile::where('id', $request->search_profile_by)->first();
            }

            if (!$res['profile']) {
                return response()->json(['message' => 'Profile not found']);
            }
        }
        if ($card) {
            if (!$card->status) {
                return response()->json(['message' => trans('backend.card_inactive')]);
            }

            $checkCard = DB::table('user_cards')
                ->select(
                    'user_cards.user_id',
                    'user_cards.profile_id'
                )
                ->where('card_id', $card->id)
                ->where('status', 1)
                ->first();
            if (!$checkCard) {
                return response()->json(['message' => trans('backend.profile_not_accessible')]);
            }

            $res['profile'] = Profile::where('id', $checkCard->profile_id)->first();
        }


        $is_connected = 0;
        $connected = DB::table('connects')
            ->where('connecting_id', auth()->id())
            ->where('connected_id', $res['profile']->id)
            ->first();
        if ($connected) {
            $is_connected = 1;
        }

        if ($res['profile']->id != auth()->id() || $res['profile']->username != auth()->user()->username) {
            User::where('id', $res['profile']->id)->increment('tiks');
        }

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
            ->where('user_id', $res['user']->id)
            ->orderBy(('user_platforms.platform_order'))
            ->get();

        return response()->json([
            'message' => 'User profile',
            // 'profile' => new UserProfileResource(),
            // 'profile' => $res['user'],
            // 'platforms' => PlatformResource::collection($platforms),
            'is_connected' => $is_connected
        ]);
    }
}
