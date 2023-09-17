<?php

namespace matpoppl\ImageCaptcha;

class GDImage
{
    public $gd;
    private $width = null;
    private $height = null;
    
    private function __construct($gd, $width = null, $height = null)
    {
        if (! $gd) {
            throw new \InvalidArgumentException('Unsupported GD resource');
        }
        
        $this->gd = $gd;
        $this->width = $width;
        $this->height = $height;
    }
    
    public function __destruct()
    {
        if ($this->gd) {
            imagedestroy($this->gd);
        }
        $this->gd = null;
    }
    
    public function getWidth()
    {
        if (null === $this->width) {
            $this->width = imagesx($this->gd);
        }
        
        return $this->width;
    }
    
    public function getHeight()
    {
        if (null === $this->height) {
            $this->height = imagesy($this->gd);
        }
        
        return $this->height;
    }
    
    public function scale($ratio)
    {
        $width = $this->getWidth() * $ratio;
        $height = $this->getHeight() * $ratio;
        $gd = imagescale($this->gd, $width, $height, \IMG_NEAREST_NEIGHBOUR);
        return new static($gd, $width, $height);
    }
    
    public function affine(array $affine, $clip = null)
    {
        $gd = imageaffine($this->gd, $affine, $clip);
        return new static($gd, null, null);
    }
    
    public function addColor($color)
    {
        if (is_int($color)) {
            return $color;
        }
        
        if (is_string($color)) {
            $color = self::hex2rgb($color);
        }
        
        if (! is_array($color)) {
            throw new \InvalidArgumentException('Unsupported color definition');
        }
        
        switch (count($color)) {
            case 4:
                list($r, $g, $b, $a) = $color;
                break;
            case 3:
                $a = null;
                list($r, $g, $b) = $color;
                break;
            default:
                throw new \UnexpectedValueException('Unsupported color count');
                break;
        }
        
        $color = (null === $a) ? imagecolorallocate($this->gd, $r, $g, $b) : imagecolorallocatealpha($this->gd, $r, $g, $b, $a);
        
        return $color;
    }
    
    public function background($color)
    {
        imagefill($this->gd, 0, 0, $this->addColor($color));
        return $this;
    }
    
    public function stroked($size, $x, $y, $textcolor, $strokecolor, $str, $px)
    {
        $textcolor = $this->addColor($textcolor);
        $strokecolor = $this->addColor($strokecolor);
        
        for($c1 = ($x-abs($px)), $z1 = ($x+abs($px)); $c1 <= $z1; $c1++) {
            for($c2 = ($y-abs($px)), $z2 = ($y+abs($px)); $c2 <= $z2; $c2++) {
                imagestring($this->gd, $size, $c1, $c2, $str, $strokecolor);
            }
        }
        
        imagestring($this->gd, $size, $x, $y, $str, $textcolor);
        
        return $this;
    }
    
    public function applyNoise()
    {
        $inthalf = mt_getrandmax() / 2;
        
        for ($y1 = 0; $y1 < $this->height;) {
            $x2 = 0;
            
            for ($x1 = 0; $x1 < $this->width;) {
                
                $x2 = $x1 + mt_rand(1, 10);
                $y2 = $y1 + mt_rand(1, 10);
                
                $color = $this->addColor(self::randomRGBA(true));
                if (mt_rand() < $inthalf) {
                    imageline($this->gd, $x2, $y2, $x1, $y1, $color);
                } else {
                    imageline($this->gd, $x1, $y1, $x2, $y2, $color);
                }
                
                $x1 += mt_rand(4, 8);
            }
            
            $y1 += mt_rand(4, 8);
        }
        
        return $this;
    }
    
    public function applyWave($period = 10, $amplitude = 5)
    {
        $img = $this->gd;
        $width = $this->getWidth();
        $height = $this->getHeight();
        
        $p = $period * rand(1, 3);
        $k = mt_rand(0, 100);
        
        for ($i = 0; $i<$width; $i++) {
            $dst_y = intval( sin($k+$i/$p) * $amplitude );
            imagecopy($img, $img, $i-1, $dst_y, $i, 0, 1, $height);
        }
        
        $k = mt_rand(0,100);
        for ($i = 0; $i<$height; $i++) {
            $dst_x = intval( sin($k+$i/$p) * $amplitude );
            imagecopy($img, $img, $dst_x, $i-1, 0, $i, $width, 1);
        }
        
        return $this;
    }
    
    public static function createWithDims($width, $height)
    {
        $gd = imagecreatetruecolor($width, $height);
        return new static($gd, $width, $height);
    }
    
    public static function randomRGBA($ignoreAlpha = false)
    {
        $max = 255;
        $r = mt_rand(0, $max);
        $g = mt_rand(0, $max);
        $b = mt_rand(0, $max);
        $a = $ignoreAlpha ? null : mt_rand(0, 25);
        
        return [$r, $g, $b, $a];
    }
    
    public static function hex2rgb($hex)
    {
        $hex = trim($hex, '# ');
        
        $a = null;
        
        if (strlen($hex) < 5) {
            $parts = str_split($hex, 1);
            $parts[0] .= $parts[0];
            $parts[1] .= $parts[1];
            $parts[2] .= $parts[2];
        } else {
            $parts = str_split($hex, 2);
        }
        
        if (4 === count($parts)) {
            $a = hexdec(strlen($parts[3]) < 2 ? $parts[3].$parts[3] : $parts[3]);
            if ($a > 127) {
                $a /= 2;
            }
        }
        
        list($r, $g, $b) = array_map('hexdec', $parts);
        
        return [$r, $g, $b, $a];
    }
}
