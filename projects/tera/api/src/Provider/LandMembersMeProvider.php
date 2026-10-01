<?php

namespace App\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Land;
use App\Entity\Person;
use App\Entity\PersonApiKey;
use App\Repository\LandMemberRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

final readonly class LandMembersMeProvider implements ProviderInterface
{
    public function __construct(
        private LandMemberRepository $landMemberRepository,
        private Security $security,
        private ManagerRegistry $managerRegistry,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $user = $this->security->getUser();

        if (!$user instanceof Person && !$user instanceof PersonApiKey) {
            throw new BadRequestHttpException('User not found');
        }

        if ($user instanceof PersonApiKey) {
            $user = $user->getPerson();
        }

        $land = $this->managerRegistry->getRepository(Land::class)->findOneBy(['ulid' => $context['filters']['land']]);

        if (!$land instanceof Land) {
            throw new \LogicException('Land not found.');
        }

        $landMember = $this->landMemberRepository->findOneBy(['person' => $user, 'land' => $land]);

        if (!$landMember) {
            throw new HttpException(403, 'Access Denied. You are not a member of this land.');
        }

        return $landMember;
    }
}
