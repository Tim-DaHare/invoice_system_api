<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InvoiceRepository")
 */
class Invoice
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $invoice_number;

    /**
     * @ORM\Column(type="integer")
     */
    private $invoice_type;

    /**
     * @ORM\Column(type="date")
     */
    private $delivery_date;

    /**
     * @ORM\Column(type="integer")
     */
    private $btw_percentage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ubn_number;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\InvoiceRow", mappedBy="invoice")
     * MaxDepth(1)
     */
    private $invoice_rows;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Customer", inversedBy="invoices")
     * @ORM\JoinColumn(nullable=false)
     * @MaxDepth(1)
     */
    private $customer;

    /**
     * @ORM\Column(type="integer")
     */
    private $flavour;

    public function __construct()
    {
        $this->invoiceRows = new ArrayCollection();
        $this->invoice_rows = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInvoiceNumber(): ?string
    {
        return $this->invoice_number;
    }

    public function setInvoiceNumber(string $invoice_number): self
    {
        $this->invoice_number = $invoice_number;

        return $this;
    }

    public function getInvoiceType(): ?int
    {
        return $this->invoice_type;
    }

    public function setInvoiceType(int $invoice_type): self
    {
        $this->invoice_type = $invoice_type;

        return $this;
    }

    public function getDeliveryDate(): ?\DateTimeInterface
    {
        return $this->delivery_date;
    }

    public function setDeliveryDate(\DateTimeInterface $delivery_date): self
    {
        $this->delivery_date = $delivery_date;

        return $this;
    }

    public function getBtwPercentage(): ?int
    {
        return $this->btw_percentage;
    }

    public function setBtwPercentage(int $btw_percentage): self
    {
        $this->btw_percentage = $btw_percentage;

        return $this;
    }

    public function getUbnNumber(): ?string
    {
        return $this->ubn_number;
    }

    public function setUbnNumber(?string $ubn_number): self
    {
        $this->ubn_number = $ubn_number;

        return $this;
    }

    /**
     * @return Collection|InvoiceRow[]
     */
    public function getInvoiceRows(): Collection
    {
        return $this->invoice_rows;
    }

    public function addInvoiceRow(InvoiceRow $invoiceRow): self
    {
        if (!$this->invoice_rows->contains($invoiceRow)) {
            $this->invoice_rows[] = $invoiceRow;
            $invoiceRow->setInvoice($this);
        }

        return $this;
    }

    public function removeInvoiceRow(InvoiceRow $invoiceRow): self
    {
        if ($this->invoiceRows->contains($invoiceRow)) {
            $this->invoiceRows->removeElement($invoiceRow);
            // set the owning side to null (unless already changed)
            if ($invoiceRow->getInvoice() === $this) {
                $invoiceRow->setInvoice(null);
            }
        }

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getFlavour(): ?int
    {
        return $this->flavour;
    }

    public function setFlavour(int $flavour): self
    {
        $this->flavour = $flavour;

        return $this;
    }
}
