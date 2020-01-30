<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\InvoiceRow;
use App\Entity\Customer;
use App\Form\InvoiceType;
use App\Repository\CustomerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

/**
 * @Route("/customers")
 */
class CustomerController extends AbstractController
{
    /**
     * @Route("/", name="customer_index", methods={"GET"})
     */
    public function index(CustomerRepository $customerRepository, SerializerInterface $serializer): Response
    {
        $customers = $customerRepository->findAll();

        // Fix circular reference
        // $jsonData = $serializer->serialize($invoices, 'json', [AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true]);

        $customersArray = [];

        foreach($customers as $customer) {
            $customerArray = [];
            $customerArray["id"] = $customer->getId();
            $customerArray["company_name"] = $customer->getCompanyName();
            $customerArray["street"] = $customer->getStreet();
            $customerArray["house_number"] = $customer->getHouseNumber();
            $customerArray["postal_code"] = $customer->getPostalCode();
            $customerArray["city"] = $customer->getCity();
            $customerArray["btw_number"] = $customer->getBtwNumber();
            $customerArray["email"] = $customer->getEmail();

            $customersArray[] = $customerArray;
        }

        return $this->json($customersArray);
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

        $invoice = new Invoice();
        $invoice->setInvoiceNumber($body["invoice_number"]);
        $invoice->setCustomer($customer);
        $invoice->setInvoiceType($body["invoice_type"]);
        $invoice->setDeliveryDate(new \DateTime($body["delivery_date"]));
        $invoice->setBtwPercentage($body["btw_percentage"]);
        $invoice->setUbnNumber($body["ubn_number"] ?? null);
        $invoice->setFlavour($body["invoice_flavour"]);

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
}
