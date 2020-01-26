<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InvoiceRowRepository")
 */
class InvoiceRow
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $work_number;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $earbrand;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $weight_kg;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $price_kg;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $costs;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Invoice", inversedBy="invoice_rows")
     * @ORM\JoinColumn(nullable=false)
     */
    private $invoice;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getWorkNumber(): ?string
    {
        return $this->work_number;
    }

    public function setWorkNumber(string $work_number): self
    {
        $this->work_number = $work_number;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getEarbrand(): ?string
    {
        return $this->earbrand;
    }

    public function setEarbrand(?string $earbrand): self
    {
        $this->earbrand = $earbrand;

        return $this;
    }

    public function getWeightKg(): ?float
    {
        return $this->weight_kg;
    }

    public function setWeightKg(?float $weight_kg): self
    {
        $this->weight_kg = $weight_kg;

        return $this;
    }

    public function getPriceKg(): ?float
    {
        return $this->price_kg;
    }

    public function setPriceKg(?float $price_kg): self
    {
        $this->price_kg = $price_kg;

        return $this;
    }

    public function getCosts(): ?float
    {
        return $this->costs;
    }

    public function setCosts(?float $costs): self
    {
        $this->costs = $costs;

        return $this;
    }

    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(?Invoice $invoice): self
    {
        $this->invoice = $invoice;

        return $this;
    }
}
