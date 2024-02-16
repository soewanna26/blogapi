<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_name' => optional($this->user)->name ?? 'Unknown user',
            'titel' => $this->title,
            'description' => Str::limit($this->description, 100),
            'category_name' => optional($this->category)->name ?? 'Unknow category',
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d h:i:s A'),
            'created_at_readable' => Carbon::parse($this->created_at)->diffForHumans(),
            'image_path' => $this->image ? asset('storage/media/' . $this->image->file_name) : null,
        ];
    }
}
