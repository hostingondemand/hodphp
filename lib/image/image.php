<?php

namespace framework\lib\image;

use framework\core\Lib;

class Image extends Lib
{
    var $resource;
    function load($file,$extension=false)
    {
        if(!$extension){
            $extension=explode(".",$file)[count(explode(".",$file))-1];
        }

        if(strtolower($extension)=="jpg" || strtolower($extension)=="jpeg"){
            $this->resource=imagecreatefromjpeg($file);
        }

        if(strtolower($extension)=="png"){
            $this->resource=imagecreatefrompng($file);
        }

        if(strtolower($extension)=="bmp"){
            $this->resource=imagecreatefromwbmp($file);
        }

        if(strtolower($extension)=="gif"){
            $this->resource=imagecreatefromgif($file);
        }
    }

    function resize($maxWidth,$maxHeight){
        $oldWidth=imageSX($this->resource);
        $oldHeight=imageSY($this->resource);

        if($oldWidth > $oldHeight)
        {
            $newWidth    =   $maxWidth;
            $newHeight    =   $oldHeight*($maxHeight/$oldWidth);
        }

        if($oldHeight < $oldWidth)
        {
            $newWidth    =   $oldWidth*($maxWidth/$oldHeight);
            $newHeight    =   $maxHeight;
        }

        if($oldWidth == $oldHeight)
        {
            $newWidth    =   $maxWidth;
            $newHeight    =   $maxHeight;
        }

        $newImage=imagecreatetruecolor($newWidth,$newHeight);
        imagecopyresampled($newImage,$this->resource,0,0,0,0,$newWidth,$newHeight,$oldWidth,$oldHeight);
        $this->resource=$newImage;
        return $this;
    }

    function save($file){
        $extension=explode(".",$file)[count(explode(".",$file))-1];

        if(strtolower($extension)=="jpg" || strtolower($extension)=="jpeg"){
            $this->resource=imagejpeg($this->resource,$this->filesystem->calculatePath($file));
        }

        if(strtolower($extension)=="png"){
            $this->resource=imagepng($this->resource,$this->filesystem->calculatePath($file));
        }

        if(strtolower($extension)=="bmp"){
            $this->resource=imagewbmp($this->resource,$this->filesystem->calculatePath($file));
        }

        if(strtolower($extension)=="gif"){
            $this->resource=imagegif($this->resource,$this->filesystem->calculatePath($file));
        }
        return $this;
    }
}