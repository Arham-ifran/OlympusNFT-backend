<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Hashids;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Storage;

class BlogsResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request)
    {

        if (Storage::disk(config('constants.FILESYSTEM_DEFAULT_DISK'))->exists('uploads/blogs/' . $this->id . '/' . $this->image)) {

            $image = Storage::disk(config('constants.FILESYSTEM_DEFAULT_DISK'))->url('uploads/blogs/' . $this->id . '/' . $this->image);
        } else {
            $image = asset('backend/images/no_image.jpg');
        }
        return [
            'id'                      => Hashids::encode($this->id),
            'categoryId'              =>  Hashids::encode($this->category_id),
            'categoryTitle'           => $this->blogCategory->title,
            'title'                   => $this->title,
            'description'             => Str::limit($this->description, 250, '...'),
            'image'                   => $image,
            'slug'                    => $this->slug,
            'createdAt'               =>  Carbon::parse($this->created_at)->format('d M, Y'),

        ];
    }
}
