<?php

namespace App\Entity;

use App\Repository\TypePropertyRepository;
use App\Utils\TraitClasses\EntityTimestampableTrait;
use App\Utils\TraitClasses\EntityUserOperation;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypePropertyRepository::class)]
class TypeProperty
{
    use EntityTimestampableTrait;
    use EntityUserOperation;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
