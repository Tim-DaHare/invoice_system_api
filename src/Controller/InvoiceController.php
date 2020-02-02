<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\InvoiceRow;
use App\Entity\Customer;
use App\Form\InvoiceType;
use App\Repository\InvoiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @Route("/invoices")
 */
class InvoiceController extends AbstractController
{
    /**
     * @Route("/", name="invoice_index", methods={"GET"})
     */
    public function index(InvoiceRepository $invoiceRepository, SerializerInterface $serializer): Response
    {
        $invoices = $invoiceRepository->findBy([], ["id" => "DESC"]);

        // Fix circular reference
        // $jsonData = $serializer->serialize($invoices, 'json', [AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true]);

        $invoicesArray = [];

        foreach($invoices as $invoice) {
            $invoiceArray = [];
            $invoiceArray["id"] = $invoice->getId();
            $invoiceArray["invoice_number"] = $invoice->getInvoiceNumber();
            $invoiceArray["invoice_type"] = $invoice->getInvoiceType();
            $invoiceArray["delivery_date"] = $invoice->getDeliveryDate();
            $invoiceArray["btw_percentage"] = $invoice->getBtwPercentage();
            $invoiceArray["ubn_number"] = $invoice->getUbnNumber();
            $invoiceArray["customer_id"] = $invoice->getCustomer()->getId();
            $invoiceArray["invoice_flavour"] = $invoice->getFlavour();
            $invoiceArray["regarding"] = $invoice->getRegarding();
            $invoiceArray["invoice_rows"] = [];

            foreach($invoice->getInvoiceRows() as $invoiceRow) {
                $invoiceRowArray = [];
                $invoiceRowArray["id"] = $invoiceRow->getId();
                $invoiceRowArray["description"] = $invoiceRow->getDescription();
                $invoiceRowArray["amount"] = $invoiceRow->getAmount();
                $invoiceRowArray["work_number"] = $invoiceRow->getAmount();
                $invoiceRowArray["price"] = $invoiceRow->getPrice();
                $invoiceRowArray["earbrand"] = $invoiceRow->getEarbrand();
                $invoiceRowArray["weight_kg"] = $invoiceRow->getWeightKg();
                $invoiceRowArray["price_kg"] = $invoiceRow->getPriceKg();
                $invoiceRowArray["costs"] = $invoiceRow->getCosts();
                $invoiceRowArray["delivery_date"] = $invoiceRow->getDeliveryDate();

                $invoiceArray["invoice_rows"][] = $invoiceRowArray;
            }

            $invoicesArray[] = $invoiceArray;
        }

        return $this->json($invoicesArray);
    }

    /**
     * @Route("/new", name="invoice_new", methods={"POST"})
     */
    public function new(Request $request, SerializerInterface $serializer): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $customerRepo = $entityManager->getRepository(Customer::class);

        $body = json_decode($request->getContent(), true);

        $customer = $customerRepo->find($body["customer_id"]);
        if (!$customer) {
            $this->createNotFoundException("No customer found with that id");
        }

        $invoice = new Invoice;
        $invoice->setInvoiceNumber($body["invoice_number"]);
        $invoice->setCustomer($customer);
        $invoice->setInvoiceType($body["invoice_type"]);
        $invoice->setDeliveryDate(new \DateTime($body["delivery_date"]));
        $invoice->setBtwPercentage($body["btw_percentage"]);
        $invoice->setUbnNumber($body["ubn_number"] ?? null);
        $invoice->setFlavour($body["invoice_flavour"]);
        $invoice->setRegarding($body["regarding"] ?? null);

        foreach($body["invoice_rows"] as $row) {
            $invoiceRow = new InvoiceRow;
            $invoiceRow->setDescription($row["description"] ?? null);
            $invoiceRow->setAmount($row["amount"] ?? null);
            $invoiceRow->setWorkNumber($row["work_number"] ?? null);
            $invoiceRow->setPrice($row["price"]);
            $invoiceRow->setEarbrand($row["earbrand"] ?? null);
            $invoiceRow->setWeightKg($row["weight_kg"] ?? null);
            $invoiceRow->setPriceKg($row["price_kg"] ?? null);
            $invoiceRow->setCosts($row["costs"] ?? null);
            $invoiceRow->setDeliveryDate(new \DateTime($row["delivery_date"]) ?? null);

            $entityManager->persist($invoiceRow);

            $invoice->addInvoiceRow($invoiceRow);
        }

        $entityManager->persist($invoice);

        $entityManager->flush();

        // fix the serializer with entityrelations
        // $jsonData = $serializer->serialize($invoice, 'json', [AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true]);

        return $this->json(["status" => "success"]);
    }

    /**
     * @Route("/{id}/edit", name="invoice_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Invoice $invoice): Response
    {
        // $form = $this->createForm(InvoiceType::class, $invoice);
        // $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {
        //     $this->getDoctrine()->getManager()->flush();

        //     return $this->redirectToRoute('invoice_index');
        // }

        // return $this->render('invoice/edit.html.twig', [
        //     'invoice' => $invoice,
        //     'form' => $form->createView(),
        // ]);
    }

    /**
     * @Route("/{id}", name="invoice_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Invoice $invoice): Response
    {
        // if ($this->isCsrfTokenValid('delete'.$invoice->getId(), $request->request->get('_token'))) { // ?
        //     $entityManager = $this->getDoctrine()->getManager();
        //     $entityManager->remove($invoice);
        //     $entityManager->flush();
        // }

        // return $this->redirectToRoute('invoice_index');
    }

    /**
     * @Route("/pdf/{id}", name="invoice_pdf", methods={"GET"})
     */
    public function pdf(Invoice $invoice, KernelInterface $kernel): Response
    {
        $projectDir = $kernel->getProjectDir();
        $tempPdfDir = $projectDir."/var/temp/pdf";

        $html = $this->renderView("invoice_pdf.html.twig", compact("invoice"));
        // return $this->render("invoice_pdf.html.twig", compact("invoice"));

        $compressedHtml = gzdeflate($html, 9);
        $encodedHtml = base64_encode($compressedHtml);
        $desiredFilename = $invoice->getInvoiceNumber().".pdf";

        $output;
        $result;

        exec("node $projectDir/scripts/generate_pdf.js $encodedHtml $desiredFilename", $output, $result);

        // 0 as exit code means the program ran without any errors
        if ($result !== 0) {
            $response = new Response(
                json_encode(["status" => "error", "message" => "an error occured when generating the pdf"]),
                500,
                ['Content-Type' => 'application/json']
            );
            return $response;
        }

        $filename = $output[0];
        $filePath = $tempPdfDir."/".$filename;
        $pdfContent = \file_get_contents($tempPdfDir."/".$filename);
        // Todo: Delete the temporary pdf file

        $response = new Response($pdfContent);
        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_INLINE,
            $invoice->getInvoiceNumber().".pdf"
        );

        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', "application/pdf");

        return $response;
    }
}
