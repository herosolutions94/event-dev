<?php
use Illuminate\Support\Facades\Storage;

function pr($data){
    print_r($data);die;
}
function get_site_image_src($path, $image, $type = '', $user_image = false)
{
    $filepath = Storage::url($path.'/'.$type.$image);
    if (!empty($image) && @file_exists(".".Storage::url($path.'/'.$type.$image))) {
    // if (!empty($image) && @getimagesize($filepath)) {
        return url($filepath);
    }
    return empty($user_image) ? asset('images/no-image.svg') : asset('images/no-user.svg');
}
?>