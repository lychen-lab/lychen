<?php

namespace App\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\LandMemberInvitationCheckEmailUnicityDto;
use App\Entity\Land;
use App\Repository\LandMemberInvitationRepository;
use App\Security\Voter\LandMemberInvitationVoter;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

final readonly class LandMemberInvitationCheckEmailUnicityProvider implements ProviderInterface
{
    public function __construct(
        private LandMemberInvitationRepository $landMemberInvitationRepository,
        private ManagerRegistry $managerRegistry,
        private Security $security,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $email = $context['filters']['email'] ?? null;
        $landUlid = $context['filters']['land'] ?? null;
        if (!$email || !$landUlid) {
            throw new BadRequestHttpException('Email and land are required.');
        }

        $land = $this->managerRegistry->getRepository(Land::class)->findOneBy(['ulid' => $landUlid]);

        if (!$land) {
            throw new BadRequestHttpException('Land not found.');
        }

        if (!$this->security->isGranted(LandMemberInvitationVoter::CHECK_EMAIL_UNICITY, $land)) {
            throw new UnauthorizedHttpException('You are not allowed to check the email unicity of invitations for this land.');
        }

        $existingInvitation = $this->landMemberInvitationRepository->findOneBy([
            'email' => $email,
            'land' => $land,
        ]);

        return new LandMemberInvitationCheckEmailUnicityDto(!$existingInvitation);
    }
}
