<?php

namespace AppBundle\Service\Recognizer\Interfaces;

interface RecognizerInterface
{
    public function recognize(string $filePath): string;
}