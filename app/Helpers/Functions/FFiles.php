<?php
/*
 * Copyright © 2022. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */


if (!function_exists('saveImage')) {
    function saveImage($picture, $path = '/', $driver = null)
    {
        $driver ??= getDefaultDiskDriver();

        $fullPath  = null;
        if ($picture &&  !is_string($picture)) {
            $extension = $picture->extension();
            $imageName = time() . '_' . uniqid();
            $fullPath = $picture->storeAs($path, $imageName . '.' . $extension, ['disk' => $driver]);
        }

        return $fullPath ?? $picture;
    }
}
