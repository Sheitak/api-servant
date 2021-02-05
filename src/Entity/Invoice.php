<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\InvoiceRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=InvoiceRepository::class)
 * @ApiResource(
 *  subresourceOperations={
 *      "api_customers_invoices_get_subresource"={
 *          "normalization_context"={"groups"={"invoices_subresource"}}
 *      }
 *  },
 * itemOperations={
 *      "GET", "PUT", "DELETE", 
 *      "increment"={
 *          "method"="post", 
 *          "path"="/invoices/{id}/increment", 
 *          "controller"="App\Controller\InvoiceIncrementationController",
 *          "swagger_context"={
 *              "summary"="Invoice increment", 
 *              "description"="Invoice chrono increment"
 *          }
 *      }
 *  },
 *  attributes={
 *      "pagination_enabled"=true,
 *      "pagination_items_per_page"=20,
 *      "order": {"sentAt":"desc"}
 *  },
 *  normalizationContext={
 *      "groups"={"invoices_read"}
 *  },
 *  denormalizationContext={
 *      "disable_type_enforcement"=true
 *  }
 * )
 * @ApiFilter(OrderFilter::class, properties={"amount", "sentAt"})
 */
class Invoice
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"invoices_read", "customers_read", "invoices_subresource"})
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     * @Groups({"invoices_read", "customers_read", "invoices_subresource"})
     * @Assert\NotBlank(message="Invoice amount is required")
     * @Assert\Type(type="numeric", message="Invoice amount must be numeric character")
     */
    private $amount;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"invoices_read", "customers_read", "invoices_subresource"})
     * @Assert\Type(type="datetime", message="The date must be YYYY-MM-DD format")
     * @Assert\NotBlank(message="Sent date is required")
     */
    private $sentAt;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"invoices_read", "customers_read", "invoices_subresource"})
     * @Assert\NotBlank(message="Statut is required")
     * @Assert\Choice(
     *  choices={"SENT", "PAID", "CANCELLED"},
     *  message="Statut must be SENT, PAID or CANCELLED"
     * )
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=Customer::class, inversedBy="invoices")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"invoices_read"})
     * @Assert\NotBlank(message="Invoice customer is required")
     */
    private $customer;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"invoices_read", "customers_read", "invoices_subresource"})
     * @Assert\NotBlank(message="Chrono is required")
     * @Assert\Type(type="integer", message="Chrono must be integer type")
     */
    private $chrono;

    /**
     * Get invoice from User
     * @Groups({"invoices_read", "invoices_subresource"})
     * @return float
     */
    public function getUser(): User
    {
        return $this->customer->getUser();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount($amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getSentAt(): ?\DateTimeInterface
    {
        return $this->sentAt;
    }

    public function setSentAt($sentAt): self
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

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

    public function getChrono(): ?int
    {
        return $this->chrono;
    }

    public function setChrono($chrono): self
    {
        $this->chrono = $chrono;

        return $this;
    }
}
