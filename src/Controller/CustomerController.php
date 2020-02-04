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
     * @Route("/new", name="customer_new", methods={"POST"})
     */
    public function new(Request $request, SerializerInterface $serializer): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $customerRepo = $entityManager->getRepository(Customer::class);

        $body = json_decode($request->getContent(), true);

        $customer = new Customer();
        $customer->setCompanyName($body['company_name']);
        $customer->setStreet($body['street'] ?? "");
        $customer->setHouseNumber($body['house_number'] ?? "");
        $customer->setPostalCode($body['postal_code'] ?? "");
        $customer->setCity($body['city'] ?? "");
        $customer->setBtwNumber($body['btw_number'] ?? "");
        $customer->setEmail($body['email'] ?? "");

        $entityManager->persist($customer);

        $entityManager->flush();

        // fix the serializer with entityrelations
        // $jsonData = $serializer->serialize($invoice, 'json', [AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true]);

        return $this->json(["status" => "success"]);
    }

    /**
     * @Route("/{id}/edit", name="customer_edit", methods={"POST"})
     */
    public function edit(Request $request, Customer $customer): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $customerRepo = $entityManager->getRepository(Customer::class);

        $body = json_decode($request->getContent(), true);

        $customer->setCompanyName($body['company_name']);
        $customer->setStreet($body['street']  ?? "");
        $customer->setHouseNumber($body['house_number'] ?? "");
        $customer->setPostalCode($body['postal_code'] ?? "");
        $customer->setCity($body['city'] ?? "");
        $customer->setBtwNumber($body['btw_number'] ?? "");
        $customer->setEmail($body['email'] ?? "");

        $entityManager->flush();

        return $this->json(["status" => "success"]);
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
