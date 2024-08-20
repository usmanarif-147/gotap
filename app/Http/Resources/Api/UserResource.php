<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $data =  [
            'id' => $this->id,
            'email' => $this->email,
            'name' => $this->name,
            'status' =>  $this->status,
            'is_suspended' =>  $this->is_suspended,
            'address' =>  $this->address,
            'gender' =>  $this->gender,
            'dob' =>  $this->dob,
            'verified' =>  $this->verified,
            'featured' =>  $this->featured,
            'deactivated_at' =>  $this->deactivated_at,
        ];

        if (request()->segment(2) != 'updateProfile') {
            $data['profiles'] = UserProfileResource::collection($this->profiles);
        }

        return $data;
    }
}
