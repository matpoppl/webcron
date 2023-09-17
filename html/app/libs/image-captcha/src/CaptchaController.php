<?php

namespace matpoppl\ImageCaptcha;

use matpoppl\SmallMVC\Controller\AbstractController;
use matpoppl\SmallMVC\Message\Response;
use matpoppl\HttpMessage\InMemoryStream;

class CaptchaController extends AbstractController
{
    public function createImage(array $options, $hash)
    {
        $canvasWidth = $options['width'];
        $canvasHeight = $options['height'];
        
        $canvas = GDImage::createWithDims($canvasWidth, $canvasHeight);
        $canvas->background('#fff');
        
        $colors = [
            $canvas->addColor([255, 0, 0, 20]),
            $canvas->addColor([0, 255, 0, 20]),
            $canvas->addColor([0, 0, 255, 20]),
            $canvas->addColor([0, 0, 0, 20]),
            $canvas->addColor([255, 255, 0, 20]),
            $canvas->addColor([0, 255, 255, 20]),
            $canvas->addColor([255, 0, 255, 20]),
        ];
        
        $font = 5;
        $charWidth = imagefontwidth($font);
        $charHeight = imagefontheight($font);
        
        for ($i = 0, $z = ($canvasWidth + $canvasHeight) * 0.75; $i < $z; $i++) {
            
            $charImg = GDImage::createWithDims($charWidth, $charHeight)->background([255,255,255,127]);
            $c = str_shuffle('qwertyuiopasdfghjklzxcvbnm1234567890QWERTYUIOPASDFGHJUIKLZXCVBNM');
            $x = mt_rand(-20, $canvasWidth-9);
            $y = mt_rand(-20, $canvasHeight-9);
            imagechar($charImg->gd, 5, 0, 0, $c, $colors[array_rand($colors)]);
            
            $charImg = $charImg->scale(2);
            imagecopy($canvas->gd, $charImg->gd, $x, $y, 0, 0, $charImg->getWidth(), $charImg->getHeight());
        }
        
        $dst_x = $dst_y = 0;
        $src_x = $src_y = 0;
        
        foreach (str_split($hash) as $i => $char) {
            $charImg = GDImage::createWithDims(12, 18);
            $charBg = $charImg->addColor([0, 0, 0, 127]);
            $charImg->background($charBg);
            
            $colors = [
                $charImg->addColor('#0002'),
                $charImg->addColor('#00f2'),
                $charImg->addColor('#0f02'),
            ];
            
            imagechar($charImg->gd, 5, 0, 0, $char, $colors[0]);
            imagechar($charImg->gd, 5, 2, 2, $char, $colors[1]);
            imagechar($charImg->gd, 5, 1, 1, $char, $colors[2]);
            
            imagesetinterpolation($charImg->gd, IMG_NEAREST_NEIGHBOUR);
            $charImg = $charImg->scale(4)->affine([
                1, mt_rand(-7,7) / 10, mt_rand(-1,1) / 10,
                1, mt_rand(-7,7) / 10, 0
            ]);
            
            $dst_w = $charImg->getWidth();
            $dst_h = $charImg->getHeight();
            
            $dst_x += mt_rand(0, abs(intval($canvasWidth / 6) - $dst_w));
            $dst_y = mt_rand(0, abs($canvasHeight - $dst_h));
            
            imagecopyresampled($canvas->gd, $charImg->gd, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $charImg->getWidth(), $charImg->getHeight());
            
            $dst_x = intval($canvasWidth / 6) * ($i+1);
        }

        return $canvas->applyNoise()->applyWave();
    }
    
    public function indexAction()
    {
        $hash = $this->container->get(PhraseSourceInterface::class)->tickAndGet();
        
        $img = $this->createImage($this->container->get('image_captcha')->getOptions(), $hash);
        
        $binary = fopen('php://memory', 'w+');
        imagepng($img->gd, $binary);
        
        $ret = new Response([
            'Content-Type' => 'image/png',
            'Cache-Control' => 'no-store',
            'Expires' => '-1',
        ], InMemoryStream::fromResource($binary));
        
        return $ret;
    }
}
