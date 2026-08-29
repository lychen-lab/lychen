<?php

namespace App\Doctrine\Listener;

use App\Entity\AreaProposal;
use App\Entity\Person;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Bundle\SecurityBundle\Security;

#[AsEntityListener(event: Events::prePersist, entity: AreaProposal::class)]
final readonly class AreaProposalLinkProposerListener
{
    public function __construct(private Security $security)
    {
    }

    /**
     * @param LifecycleEventArgs<EntityManagerInterface> $event
     */
    public function __invoke(AreaProposal $areaProposal, LifecycleEventArgs $event): void
    {
        if ($areaProposal->getProposer()) {
            return;
        }

        $proposer = $this->security->getUser();
        if (!$proposer instanceof Person) {
            return;
        }

        $areaProposal->setProposer($proposer);
    }
}
