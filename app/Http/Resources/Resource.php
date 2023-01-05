<?php

namespace App\Http\Resources;

use App\Http\Resources\Abstracts\AbstractResource;

/**
 *
 */
class Resource extends AbstractResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
