<?php

namespace App\Entity;

use App\Repository\AreaProposalRepository;
use App\Workflow\AreaProposal\AreaProposalWorkflow;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Lychen\UtilModel\Abstract\AbstractIdOrmAndUuidApiIdentified;
use Lychen\UtilModel\Trait\CreatedAtTrait;
use Symfony\Component\Validator\Constraints\Choice;

#[ORM\Entity(repositoryClass: AreaProposalRepository::class)]
#[ORM\HasLifecycleCallbacks]
class AreaProposal extends AbstractIdOrmAndUuidApiIdentified
{
    use CreatedAtTrait;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Choice(AreaProposalWorkflow::PLACES)]
    private ?string $place = AreaProposalWorkflow::PLACE_DRAFT;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $archivedAt = null;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPlace(): ?string
    {
        return $this->place;
    }

    public function setPlace(string $place): static
    {
        $this->place = $place;

        return $this;
    }

    public function getArchivedAt(): ?DateTimeImmutable
    {
        return $this->archivedAt;
    }

    public function setArchivedAt(?DateTimeImmutable $archivedAt): static
    {
        $this->archivedAt = $archivedAt;

        return $this;
    }
}
