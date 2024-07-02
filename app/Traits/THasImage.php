<?php

namespace App\Traits;

/**
 * @mixin \App\Models\Model
 */
trait THasImage
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\UrlGenerator|string|null
     */
    public function getImageUrlAttribute(): string|\Illuminate\Contracts\Routing\UrlGenerator|\Illuminate\Contracts\Foundation\Application|null
    {
        return retrieveImage($this->image);
    }

    /**
     * @param \Illuminate\Http\File|\Illuminate\Http\UploadedFile|string $image
     *
     * @return void
     */
    public function setImageAttribute($image): void
    {
        $this->attributes[ 'image' ] = storeImage($image);
    }
}
