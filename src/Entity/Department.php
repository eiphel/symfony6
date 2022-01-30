<?php

namespace App\Entity;

use App\Repository\DepartmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DepartmentRepository::class)]
class Department
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 12, nullable: true)]
    private $number;

    #[ORM\ManyToOne(targetEntity: Region::class, inversedBy: 'departments')]
    private $Region;

    #[ORM\OneToMany(mappedBy: 'department', targetEntity: Add::class)]
    private $adds;

    public function __construct()
    {
        $this->adds = new ArrayCollection();
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

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getRegion(): ?Region
    {
        return $this->Region;
    }

    public function setRegion(?Region $Region): self
    {
        $this->Region = $Region;

        return $this;
    }

    /**
     * @return Collection|Add[]
     */
    public function getAdds(): Collection
    {
        return $this->adds;
    }

    public function addAdd(Add $add): self
    {
        if (!$this->adds->contains($add)) {
            $this->adds[] = $add;
            $add->setDepartment($this);
        }

        return $this;
    }

    public function removeAdd(Add $add): self
    {
        if ($this->adds->removeElement($add)) {
            // set the owning side to null (unless already changed)
            if ($add->getDepartment() === $this) {
                $add->setDepartment(null);
            }
        }

        return $this;
    }
}
