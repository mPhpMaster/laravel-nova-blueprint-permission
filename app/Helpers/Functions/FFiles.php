<?php
/*
 * Copyright © 2025. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

if(!function_exists('storeImage')) {

	/**
	 * @param \Illuminate\Http\File|\Illuminate\Http\UploadedFile|string|null $image
	 * @param string                                                          $path
	 * @param string|null                                                     $driver
	 *
	 * @return string
	 */
	function storeImage(\Illuminate\Http\File|\Illuminate\Http\UploadedFile|string|null $image, string $path = '/', ?string $driver = 'public')
	{
		$driver ??= getDefaultDiskDriver('public');

		if($image && !is_string($image)) {
			$fullImageName = time().'_'.uniqid().'.'.$image->extension();
			$image = $image->storeAs($path, $fullImageName, [ 'disk' => $driver ]);
		}

		return $image ?: '';
	}
}

if(!function_exists('retrieveImage')) {
	/**
	 * @param string      $image
	 * @param string|null $path
	 * @param string|null $driver
	 *
	 * @return string
	 */
	function retrieveImage(?string $image, ?string $path = '/', ?string $driver = 'public'): string
	{
		if(!($image = trim($image ?? ''))) {
			return '';
		}

		if($image && isUrl($image)) {
			return $image;
		}

		$path = trim($path);
		$driver ??= getDefaultDiskDriver('public');

		return $image ? url(
			\Storage::disk($driver)
				->url(
					trimDirectorySeparator(implode(DIRECTORY_SEPARATOR, [ trim($path), $image ]))
				)
		) : '';
	}
}
