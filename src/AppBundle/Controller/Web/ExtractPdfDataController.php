<?php
/**
 * Created by PhpStorm.
 * User: evis
 * Date: 7/17/17
 * Time: 10:50 AM
 */

namespace AppBundle\Controller\Web;


use AppBundle\Entity\ImportRequest;
use AppBundle\Form\ImportForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Smalot\PdfParser\Parser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ExtractPdfDataController extends Controller
{
    /**
     * @Route("upload_pdf", name="upload_excel_file")
     * @param Request $request
     * @return Response
     */
    public function excelToDoctrineAction(Request $request)
    {
        $importRequest = new ImportRequest();
        $form = $this->createForm(ImportForm::class, $importRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $file stores the uploaded excel file
            /** @var UploadedFile $file */
            $file = $importRequest->getFile();

            $ext = $file->getClientOriginalExtension();
            // Generate a unique name for the file before saving it
            $fileName = md5(uniqid()).'.'.$ext;

            // Move the file to the directory where brochures are stored
            $file->move($this->getParameter('rate_files_path'), $fileName);

            // Update the 'file' property to store the PDF file name
            // instead of its contents
            $importRequest->setFile($fileName);
            $importRequest->setRequestDateTime(new \DateTime());

            // Parse pdf file and build necessary objects.
            $parser = new Parser();
            $pdf    = $parser->parseFile($this->getParameter('rate_files_path')."/".$importRequest->getFile());

            // Retrieve all pages from the pdf file.
            $pages  = $pdf->getPages();

            $response = "";
            // Loop over each page to extract text.
            $results = array();
            foreach ($pages as $page) {
                $result = array();
                $data = explode(PHP_EOL, $page->getText());

                $result["shipToName"] = $data[6];
                $result["shipToPhone"] = str_replace("Phone: ", "", $data[7]);
                $result["shipToAddress"] = $data[8]."<br>".$data[9];

                if($data[14] == "Item"){
                    $poNumber = substr($data[13], strpos($data[13], " "), (strpos($data[13], "Date")-strpos($data[13], " ")));

                    $result["po"] = $poNumber;

                    $result["itemSku"] = $data[15];
                    $result["qty"] = $data[17];

                    if($data[21] == "Rate"){
                        $result["description"] = $data[19]." ".$data[20];
                        $result["rate"] = $data[22];
                        $result["amount"] = $data[24];
                        $result["total"] = str_replace("Total$", "", $data[25]);
                    }else{
                        $result["description"] = $data[19];
                        $result["rate"] = $data[21];
                        $result["amount"] = $data[23];
                        $result["total"] = str_replace("Total$", "", $data[24]);
                    }
                }elseif($data[15] == "Item"){
                    $poNumber = substr($data[14], strpos($data[14], " "), (strpos($data[14], "Date")-strpos($data[14], " ")));

                    $result["po"] = $poNumber;

                    $result["itemSku"] = $data[16];
                    $result["qty"] = $data[18];

                    if($data[22] == "Rate"){
                        $result["description"] = $data[20]." ".$data[21];
                        $result["rate"] = $data[23];
                        $result["amount"] = $data[25];
                        $result["total"] = str_replace("Total$", "", $data[26]);
                    }else{
                        $result["description"] = $data[20];
                        $result["rate"] = $data[22];
                        $result["amount"] = $data[24];
                        $result["total"] = str_replace("Total$", "", $data[25]);
                    }
                }

                array_push($results, $result);
            }

            return $this->render("import/results.html.twig", ["results" => $results]);
            //return new Response($response);
            //return $this->redirect($this->generateUrl('homepage'));
        }

        return $this->render(
            'import/new.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }
}