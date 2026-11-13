<?php

namespace App\Entity;

/**
 * What an area proposal and an area request share: a listing its owner submits to
 * moderation, which publishes it or sends it back with a reason.
 */
interface AreaListingInterface
{
    public function getPlace(): ?string;

    public function getRejectionReason(): ?string;

    public function setRejectionReason(?string $rejectionReason): static;

    public function getArchivedAt(): ?\DateTimeImmutable;

    public function setArchivedAt(?\DateTimeImmutable $archivedAt): static;
}
