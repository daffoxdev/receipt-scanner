<?php

namespace AppBundle\Service\Recognizer;

use AppBundle\Service\Recognizer\Interfaces\RecognizerInterface;
use thiagoalessio\TesseractOCR\TesseractOCR;

class TesseractRecognizer implements RecognizerInterface
{
    protected $recongizer;

    public function recognize(string $filePath): string
    {
        $tesseract = new TesseractOCR($filePath);

        $result = $tesseract->run();

        return $result;
    }
}