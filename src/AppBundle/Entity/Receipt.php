<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class Receipt
{
    /**
     * @var UploadedFile|string|null $filename
     *
     * @Assert\NotBlank(message="Please, upload the receipt image.")
     * @Assert\File(mimeTypes={"image/jpeg", "image/png"})
     */
    protected $file;

    /**
     * @param UploadedFile|string $file
     * @return Receipt
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    /**
     * @return UploadedFile|string|null
     */
    public function getFile()
    {
        return $this->file;
    }
}