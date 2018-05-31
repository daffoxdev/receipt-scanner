<?php

namespace AppBundle\Service;

use AppBundle\Service\Matchers\DateAndTimeDefault;
use AppBundle\Service\Matchers\TotalMatcher;
use AppBundle\Service\Recognizer\Interfaces\RecognizerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class ReceiptRecognizer
{
    protected $recongizer;
    protected $receiptFileManager;
    protected $dataFinder;

    public function __construct(RecognizerInterface $recongizer, ReceiptFileManager $receiptFileManager, ReceiptDataFinder $dataFinder)
    {
        $this->recongizer = $recongizer;
        $this->receiptFileManager = $receiptFileManager;
        $this->dataFinder = $dataFinder;
    }

    public function recongizeUploaded(string $fileName, $generateNew = true)
    {
        if (!$generateNew && $this->receiptFileManager->recognizedExits($fileName)) {
            $recognizedContent = $this->receiptFileManager->getRecognizedContent($fileName);
        } else {
            $filePath = $this->receiptFileManager->getUploadedFilePathByName($fileName);
            $recognizedContent = $this->recongizer->recognize($filePath);
            $this->receiptFileManager->createRecongized($fileName, $recognizedContent);
        }

        return $recognizedContent;
    }


    public function findDataInFile(string $fileName)
    {
        $content = $this->recongizeUploaded($fileName, false);
        $matches = $this->findData($content);

        return $matches;
    }
    public function findData($content)
    {
        $dataFinder = $this->dataFinder;

        $dataFinder
            ->addMatcher(new DateAndTimeDefault())
            ->addMatcher(new TotalMatcher())
        ;

        $matches = $this->dataFinder->findMatches($content);

        return $matches;
    }

}