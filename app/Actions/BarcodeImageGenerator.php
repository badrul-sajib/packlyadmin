<?php

namespace App\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Picqer\Barcode\BarcodeGeneratorPNG;

class BarcodeImageGenerator
{
    public static function execute(Model $model): bool|string
    {
        try {
            // Check GD extension
            if (! extension_loaded('gd') || ! function_exists('imagecreatefromstring')) {
                throw new \RuntimeException('GD extension not available');
            }

            // Check barcode generator
            if (! class_exists('Picqer\Barcode\BarcodeGeneratorPNG')) {
                throw new \RuntimeException('Barcode generator package not installed');
            }

            // Validate barcode
            if (empty($model->barcode) || ! preg_match('/^[0-9]+$/', $model->barcode)) {
                throw new \InvalidArgumentException('Invalid barcode format');
            }

            $generator   = new BarcodeGeneratorPNG;
            $barcodeData = $generator->getBarcode($model->barcode, $generator::TYPE_CODE_128);

            if (empty($barcodeData)) {
                throw new \RuntimeException('Barcode generation failed');
            }

            $barcodeImage = imagecreatefromstring($barcodeData);
            if ($barcodeImage === false) {
                throw new \RuntimeException('Failed to create image from barcode data');
            }

            $width       = imagesx($barcodeImage);
            $height      = imagesy($barcodeImage);
            $extraHeight = 20;
            $finalImage  = imagecreatetruecolor($width, $height + $extraHeight);

            if ($finalImage === false) {
                throw new \RuntimeException('Failed to create final image');
            }

            $white = imagecolorallocate($finalImage, 255, 255, 255);
            imagefill($finalImage, 0, 0, $white);
            imagecopy($finalImage, $barcodeImage, 0, 0, 0, 0, $width, $height);

            $color     = imagecolorallocate($finalImage, 0, 0, 0);
            $font      = 3;
            $text      = implode(' ', str_split($model->barcode));
            $charWidth = imagefontwidth($font);
            $textWidth = $charWidth * strlen($text);
            $x         = ($width - $textWidth) / 2;
            $y         = $height + 2;

            imagestring($finalImage, $font, $x, $y, $text, $color);

            ob_start();
            $success   = imagepng($finalImage);
            $imageData = ob_get_clean();

            if (! $success || empty($imageData)) {
                throw new \RuntimeException('Failed to generate PNG image');
            }

            return base64_encode($imageData);
        } catch (\Exception $e) {
            Log::error('Barcode generation failed: '.$e->getMessage());

            return false;
        }

    }
}
