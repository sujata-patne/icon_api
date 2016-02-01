<?php

namespace VOP\Utils;

class ImageLib {

    public static function resizeImage($curImagePath, $newImagePath, $width, $height) {
        try {
            if($curImagePath == ''){
                return false;
            }
            
            if($width =='' || $width == 0){
                $width = 90;
            }
            
            if($height =='' || $height == 0){
                $height = 90;
            }            
            
            $image = new \Imagick($curImagePath);            
            $image->adaptiveResizeImage($width, $height);
            $image->setImageFormat( "png" );
            $image->writeImage($newImagePath);
            
            return true;
            
        } catch (Exception $e) {
            //echo $e->getMessage();
            return false;
        }
    }
    
    public static function createDefaultLogo($path) {
        try {
            $image = new \Imagick();

            $image->newImage(90, 90, new \ImagickPixel("white"));

            $draw = new \ImagickDraw();

            $draw->setFillColor(new \ImagickPixel("#" . mt_rand(100000, 999999)));

            $draw->ellipse(45, 45, 45, 45, 0, 360);
            $image->drawImage($draw);
            //$draw->setFillColor( new ImagickPixel( "#".mt_rand(100000, 999999) ) );#01C3EB
            $draw->setFillColor(new \ImagickPixel("#418bc9"));
            $draw->ellipse(45, 45, 20, 20, 0, 360);
            $image->drawImage($draw);
            $image->setImageFormat("png");
            $image_name = 'image_' . time() . '.png';
            $image->writeImage($path . $image_name);

            return $image_name;
        } catch (Exception $e) {
            //echo $e->getMessage();
            return false;
        }
    }

}

