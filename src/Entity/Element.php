<?php

namespace App\Entity;

use App\Repository\ElementRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\InheritanceType;
use Symfony\Component\Serializer\Annotation\DiscriminatorMap;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=ElementRepository::class)
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="type", type="string")
 * @DiscriminatorMap(
 *      typeProperty="type",
 *      mapping={"element" = "Element", "project" = "Project", "task" = "Task"}
 * )
 */
abstract class Element
{
    const STATUS = [
        "terminé", "en cours", "a faire"
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\NotBlank(
        message: 'Ce champs ne peut pas être vide'
    )]
    #[Assert\Length(
        min: 6,
        minMessage: 'Le nom du projet doit contenir au minimum {{ limit }} caractères.'
    )]
    protected $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    #[Assert\GreaterThanOrEqual(
        'today',
        message: "La date de fin ne doit pas être inférieur à : {{ compared_value }}"
    )]
    protected $limitedAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, fetch="EAGER")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $createdBy;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    #[Assert\Choice(
        choices: self::STATUS, 
        message: "Ce choix n'est pas valide."
    )]
    protected $status;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->status = 'a faire';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getLimitedAt(): ?\DateTimeInterface
    {
        return $this->limitedAt;
    }

    public function setLimitedAt(?\DateTimeInterface $limitedAt): self
    {
        $this->limitedAt = $limitedAt;

        return $this;
    }

    public function getCreatedBy(): ?UserInterface
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?UserInterface $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
