<?php

namespace App\Doctrine\Listener;

use App\Entity\LandMember;
use App\Entity\LandMemberInvitation;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsEntityListener(event: Events::prePersist, entity: LandMemberInvitation::class)]
#[AsEntityListener(event: Events::prePersist, entity: LandMember::class)]
final readonly class LandRolesBelongToLandValidateListener
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(LandMemberInvitation|LandMember $object, LifecycleEventArgs $event): void
    {
        $errors = $this->validator->validate($object);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            throw new \Exception('Validation failed: '.implode(', ', $errorMessages));
        }
    }
}
