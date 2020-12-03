<?php

namespace App\Services;

use Illuminate\Validation\ValidationException;

class File{

    public static function generateLink($name){
        if(isset($name)){
            $filename = basename($name);
            $file_extension = strtolower(substr(strrchr($filename,"."),1));

            if($file_extension != null){
                switch( $file_extension ) {
                    case "png": $ctype="image/png"; break;
                    case "jpeg": $ctype="image/jpeg"; break;
                    case "jpg": $ctype="image/jpg"; break;
                    case "PNG": $ctype="image/PNG"; break;
                    case "JPEG": $ctype="image/JPEG"; break;
                    case "JPG": $ctype="image/JPG"; break;
                    case "webp": $ctype="image/webp"; break;
                    case "WEBP": $ctype="image/WEBP"; break;
                    default: break;
                }
                
                $path = app()->basePath() .'/storage/app/public/' . $name;

                if(file_exists ($path)){
                    header('Content-type: ' . $ctype);
                    readfile($path);
                }
                
            }
        }
    }

    public static function decryptImage($request,$initial){
        if($request != null){
            $base64 = substr($request, strpos($request, ",")+1);
            $image  = base64_decode($base64);

            $f = finfo_open();
            $mimeType = finfo_buffer($f, $image, FILEINFO_MIME_TYPE);
            
            if($mimeType != null){
                $extension = isset(explode('/',$mimeType)[1]) ? explode('/',$mimeType)[1] : 'jpg';
                
                $availableExtension = ['jpg','jpeg','JPG','JPEG','png','PNG','webp','WEBP'];
                if(!in_array($extension,$availableExtension)){
                    throw ValidationException::withMessages([
                        'logo' => 'File Tidak Valid. Opsi Ekstensi : .JPG, .JPEG, .PNG atau .WEBP'
                    ]);
                }

                $originalName = time().'.'.$extension;
                $name   = $initial."-".$originalName;
                $path   = app()->basePath() .'/storage/app/public/' . $name;
                file_put_contents($path, $image);

                list($width, $height) = getimagesize($path);
                if($width < 100 || $height < 100){
                    throw ValidationException::withMessages([
                        'logo' => 'File Image Minimum 100 x 100'
                    ]);
                }

                return $name;
            }
        }
        
        return null;
    }

    public static function resizeImage($file, $w, $h, $crop=FALSE) {
        list($width, $height) = getimagesize($file);
        $r = $width / $height;
        if ($crop) {
            if ($width > $height) {
                $width = ceil($width-($width*abs($r-$w/$h)));
            } else {
                $height = ceil($height-($height*abs($r-$w/$h)));
            }
            $newwidth = $w;
            $newheight = $h;
        } else {
            if ($w/$h > $r) {
                $newwidth = $h*$r;
                $newheight = $h;
            } else {
                $newheight = $w/$r;
                $newwidth = $w;
            }
        }

        $extension = self::checkExtension($file);
        if($extension == 'png' || $extension == 'PNG'){ 
            $src = imagecreatefrompng($file); 
        }
        else if($extension == 'webp' || $extension == 'WEBP'){
            $src = imagecreatefromwebp($file);
        }
        else{
            $src = imagecreatefromjpeg($file);
        }
        
        $dst = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
    
        return $dst;
    }

    public static function checkExtension($path){
        $file = pathinfo($path);
        return $file['extension'];
    }
}