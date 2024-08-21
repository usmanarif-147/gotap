<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'username'  => $this->username ?? null,
            'work_position' => $this->work_position ?? null,
            'job_title'  => $this->job_title ?? null,
            'company' => $this->company ?? null,
            'address' => $this->address ?? null,
            'bio'  => $this->bio ?? null,
            'phone' => $this->phone ?? null,
            'active' => $this->active,
            'photo' => $this->photo ?? null,
            'cover_photo' => $this->cover_photo ?? null,
            'user_direct' =>  $this->user_direct ?? null,
            'tiks' => $this->tiks ?? null,
            'private' => $this->private ?? null,
            'created_at' => defaultDateFormat($this->created_at),
            'platforms' => PlatformResource::collection($this->platforms)
        ];
    }
}
