<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class PlatformResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->id ?? null,
            'title' => $this->title ?? null,
            'icon' => $this->icon ?? null,
            'input' => $this->input ?? null,
            'baseUrl' => $this->baseURL ?? null,
            'placeholder_en' => $this->placeholder_en ?? null,
            'placeholder_sv' => $this->placeholder_sv ?? null,
            'description_en' => $this->description_en ?? null,
            'description_sv' => $this->description_sv ?? null,
            'path' => $this->pivot->path ?? null,
            'label' => $this->pivot->label ?? null,
            'direct' => $this->pivot->direct ?? null,
            'platform_order' => $this->pivot->platform_order ?? null,
        ];

        if (request()->segment(2) == 'profile' || request()->segment(2) == 'getProfiles') {
            $data['saved'] = 1;
        }

        return $data;
    }
}
