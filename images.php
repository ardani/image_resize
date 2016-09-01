<?php
require 'vendor/autoload.php';
// import the Intervention Image Manager Class
use Intervention\Image\ImageManager;
use Intervention\Image\ImageManagerStatic as Image;
/**
 * Created by PhpStorm.
 * User: ardani
 * Date: 9/1/16
 * Time: 06:52
 */
if (!isset($_GET['image']))
{
    header('HTTP/1.1 400 Bad Request');
    echo 'Error: no image was specified';
    exit();
}

$image_file = $_GET['image'];
$image_width = array_key_exists('width',$_GET) ? $_GET['width'] : null;
$image_height = array_key_exists('height',$_GET) ? $_GET['height'] : null;
if (is_null($image_height) && is_null($image_width)) {
    list($width, $height) = getimagesize($image_file);
    $image_width = $width;
    $image_height = $height;
}
$filename = basename($image_file);
$file_extension = strtolower(substr(strrchr($filename,"."),1));

switch( $file_extension ) {
    case "gif": $ctype="image/gif"; break;
    case "png": $ctype="image/png"; break;
    case "jpeg":
    case "jpg": $ctype="image/jpeg"; break;
    default:
}

try {
    $img = Image::cache(function ($image) use ($image_file, $image_width, $image_height) {
        $image->make($image_file)
            ->resize($image_width, $image_height, function ($constraint) {
                $constraint->aspectRatio();
            });;
    });
    header('Content-type: ' . $ctype);
    echo $img;
} catch (Exception $e) {
    echo $e->getMessage();
}