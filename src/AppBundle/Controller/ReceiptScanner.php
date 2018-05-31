<?php

namespace AppBundle\Controller;

use AppBundle\DataView\ExtractedDataView;
use AppBundle\Entity\Receipt;
use AppBundle\Form\ReceiptType;
use AppBundle\Service\Matchers\DateAndTimeDefault;
use AppBundle\Service\Matchers\TotalMatcher;
use AppBundle\Service\ReceiptFileManager;
use AppBundle\Service\ReceiptRecognizer;
use AppBundle\Service\Recognizer\Interfaces\RecognizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ReceiptScanner extends Controller
{
    /**
     * @Route("/receipt/scan/{fileName}", name="reciept_scan")
     *
     * @param Request $request
     * @return Response
     */
    public function recognizeAction(Request $request, ReceiptRecognizer $receiptRecognizer, ReceiptFileManager $fileManager, $fileName)
    {
        $generateNew = $request->get('new') ? true : false;

        $content = $receiptRecognizer->recongizeUploaded($fileName, $generateNew);

        return $this->render('receipt/scanned.html.twig', [
            'scanned_data' => $content,
            'file_name' => $fileName
        ]);
    }

    /**
     * @Route("/receipt/extracted/{fileName}", name="reciept_extracted")
     *
     * @param Request $request
     * @return Response
     */
    public function dataExtractionAction(Request $request, ReceiptRecognizer $receiptRecognizer, $fileName)
    {
        $matches = $receiptRecognizer->findDataInFile($fileName);

        return $this->render('receipt/extracted.html.twig', [
            'data' => $matches->getValues(),
        ]);
    }

    /**
     * @Route("/receipt/new", name="app_receipt_new")
     */
    public function newAction(Request $request, ReceiptFileManager $receiptFileManager)
    {
        $receipt = new Receipt();
        $form = $this->createForm(ReceiptType::class, $receipt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $receipt->getFile();
            $savedFileName = $receiptFileManager->upload($file);

            return $this->redirectToRoute('app_receipt_overview', ['name' => $savedFileName]);
        }

        return $this->render('receipt/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/receipt/list", name="app_receipt_list")
     * @param ReceiptFileManager $receiptFileManager
     * @return Response
     */
    public function receiptsList(ReceiptFileManager $receiptFileManager)
    {
        $dir = $receiptFileManager->getNewUploadDir();
        $finder = new Finder();
        $files = $finder->in($dir)->files();

        $list = [];

        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            $list[] = [
                'name' => $file->getFilename(),
                'size' => $file->getSize(),
            ];
        }

        return $this->render('receipt/list.html.twig', [
            'list' => $list,
        ]);
    }


    /**
     * @Route("/receipt/overview/{name}", name="app_receipt_overview")
     * @param string $name
     */
    public function overviewAction(
        Request $request,
        string $name,
        ReceiptRecognizer $receiptRecognizer,
        ReceiptFileManager $receiptFileManager,
        ExtractedDataView $extractedDataView
    ) {
        $receipt = new Receipt();
        $form = $this->createForm(ReceiptType::class, $receipt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $receipt->getFile();
            $savedFileName = $receiptFileManager->upload($file);

            $name = $savedFileName;

            return $this->redirectToRoute('app_receipt_overview', ['name' => $savedFileName]);
        }

        $content = $receiptRecognizer->recongizeUploaded($name, false);
        $matches = $receiptRecognizer->findData($content);

        return $this->render('receipt/overview.html.twig', [
            'scanned_data' => $content,
            'matches' => $extractedDataView->prepareDataForView($matches->toArray()),
            'form' => $form->createView(),
            'filename' => $name,
        ]);
    }

    /**
     * @Route("/receipt/{name}", name="app_receipt_uploaded")
     * @param string $name
     * @return BinaryFileResponse
     */
    public function getReceiptImage(string $name, ReceiptFileManager $receiptFileManager)
    {
        $file = $receiptFileManager->getUploadedFilePathByName($name);

        return new BinaryFileResponse($file);
    }

}