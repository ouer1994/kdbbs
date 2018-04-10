<?php

namespace App\Handles;

use Image;

class ImageUploadHander
{
    protected $allowed_ext = ['png', 'jpg', 'jpeg', 'gif'];

    public function upload_image($file, $folder, $prefix, $max_width = false)
    {
        $folder_name = "uploads/images/$folder/" . date('Ym/d', time());
        $upload_path = public_path() . '/' . $folder_name;
        $extension = strtolower($file->getClientOriginalExtension()) ?: 'png';

        $file_name = $prefix . '_' . time() . '_' . str_random(10) . '.' . $extension;

        if (!in_array($extension, $this->allowed_ext)) {
            return false;
        }

        $file->move($upload_path, $file_name);

        if ($max_width && $extension != 'gif') {
            $this->reduce_image($upload_path . '/' . $file_name, $max_width);
        }

        return [
            'path' => config('app.url') . '/' . $folder_name . '/' . $file_name
        ];

    }

    public function reduce_image($file_path, $max_width)
    {
        $image = Image::make($file_path);
        $image->resize($max_width, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $image->save();
    }
}