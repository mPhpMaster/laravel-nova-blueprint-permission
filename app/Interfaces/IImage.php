<?php

namespace App\Interfaces;

/**
 *
 */
interface IImage
{
    /**
     *
     */
    const rules = 'mimes:jpeg,jpg,png|max:1024'; //|dimensions:ratio=1/1
}
