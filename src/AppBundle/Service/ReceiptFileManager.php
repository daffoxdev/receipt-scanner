<?php

namespace AppBundle\Service;

use Intervention\Image\ImageManagerStatic;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ReceiptFileManager
{
    protected $newFileDirectory;
    protected $preprocessedDirectory;
    protected $scannedDirectory;
    protected $parsedDirectory;
    protected $filesystem;

    public function __construct($newFileDir, $scannedDir, $parsedDirectory, $preprocessedDirectory, Filesystem $filesystem)
    {
        $this->newFileDirectory = $newFileDir;
        $this->scannedDirectory = $scannedDir;
        $this->parsedDirectory = $parsedDirectory;
        $this->filesystem = $filesystem;
        $this->preprocessedDirectory = $preprocessedDirectory;
    }

    public function upload(UploadedFile $file)
    {
        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        $image = ImageManagerStatic::make($file->getPathInfo()->getRealPath().'/'.$file->getFilename());

        if ($image->getWidth() > 500) {
            $image->widen(500);
        }
        $image->greyscale();
        $preprocessedFilepath = $this->preprocessedDirectory.'/'.$fileName;

        $file->move($this->getTargetDirectory(), $fileName);
        $image->save($preprocessedFilepath);

        return $fileName;
    }

    public function getTargetDirectory()
    {
        return $this->newFileDirectory;
    }

    /**
     * @param $fileName
     * @return string
     */
    public function getUploadedFilePathByName(string $fileName)
    {
        $filePath = $this->newFileDirectory.'/'.$fileName;

        if (!$this->filesystem->exists($filePath)) {
            throw new FileNotFoundException();
        }

        return $filePath;
    }

    /**
     * @param $fileName
     * @return string
     */
    public function getRecognizedFilePathByName(string $filename)
    {
        $filePath = $this->createPathToReconized($filename);

        if (!$this->recognizedExits($filePath, true)) {
            throw new FileNotFoundException();
        }

        return $filePath;
    }

    public function createPathToReconized(string $filename)
    {
        $filePath = $this->scannedDirectory.'/'.$filename;
        return $filePath;
    }

    public function recognizedExits($filename, $isFullpath = false)
    {
        if (!$isFullpath) {
            $filename = $this->createPathToReconized($filename);
        }

        if (!$this->filesystem->exists($filename)) {
            return false;
        }
        return true;
    }

    /**
     * @param string $fileName
     * @param $content
     * @return File
     */
    public function createRecongized(string $fileName, $content)
    {
        $filepath = $this->createPathToReconized($fileName);

        $this->filesystem->dumpFile($filepath, $content);

        $file = new File($filepath);

        return $file;
    }

    public function getRecognizedContent($filename, $isFullpath = false)
    {
        if (!$isFullpath) {
            $filename = $this->getRecognizedFilePathByName($filename);
        }
        $content =  file_get_contents($filename);

        return $content;
    }

    public function getNewUploadDir()
    {
        return $this->newFileDirectory;
    }
}