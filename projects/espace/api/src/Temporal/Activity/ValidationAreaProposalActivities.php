<?php

namespace App\Temporal\Activity;

use App\Enum\AreaProposalStatus;
use App\Repository\AreaProposalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Temporal\Activity\ActivityInterface;
use Temporal\Activity\ActivityMethod;
use Temporal\Client\WorkflowClientInterface;

#[ActivityInterface(prefix: 'ValidationAreaProposal.')]
final class ValidationAreaProposalActivities implements ValidationAreaProposalActivitiesInterface
{
    public function __construct(
        private readonly AreaProposalRepository $areaProposalRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly WorkflowClientInterface $workflowClient,
        private readonly string $novuApiUrl,
        private readonly string $novuApiKey,
    ) {
    }

    #[ActivityMethod]
    public function runAutomatedChecks(string $proposalId): array
    {
        $proposal = $this->areaProposalRepository->findOneBy(['uuid' => $proposalId]);

        if (null === $proposal) {
            return ['passed' => false, 'reason' => 'Proposal not found'];
        }

        if (empty($proposal->getTitle())) {
            return ['passed' => false, 'reason' => 'Title is required'];
        }

        if (empty($proposal->getDescription())) {
            return ['passed' => false, 'reason' => 'Description is required'];
        }

        if (null === $proposal->getSurfaceTotal() || $proposal->getSurfaceTotal() <= 0) {
            return ['passed' => false, 'reason' => 'Surface total must be positive'];
        }

        if (null === $proposal->getSurfaceToShare() || $proposal->getSurfaceToShare() <= 0) {
            return ['passed' => false, 'reason' => 'Surface to share must be positive'];
        }

        if ($proposal->getSurfaceToShare() > $proposal->getSurfaceTotal()) {
            return ['passed' => false, 'reason' => 'Surface to share cannot exceed total surface'];
        }

        if (empty($proposal->getCity())) {
            return ['passed' => false, 'reason' => 'City is required'];
        }

        return ['passed' => true];
    }

    #[ActivityMethod]
    public function markApproved(string $proposalId): void
    {
        $proposal = $this->areaProposalRepository->findOneBy(['uuid' => $proposalId]);

        if (null === $proposal) {
            return;
        }

        $proposal->setStatus(AreaProposalStatus::Active);
        $this->entityManager->flush();
    }

    #[ActivityMethod]
    public function markRejected(string $proposalId, string $reason): void
    {
        $proposal = $this->areaProposalRepository->findOneBy(['uuid' => $proposalId]);

        if (null === $proposal) {
            return;
        }

        $proposal->setStatus(AreaProposalStatus::Rejected);
        $this->entityManager->flush();
    }

    #[ActivityMethod]
    public function queueForModeration(string $proposalId): void
    {
        $proposal = $this->areaProposalRepository->findOneBy(['uuid' => $proposalId]);

        if (null === $proposal) {
            return;
        }

        $proposal->setStatus(AreaProposalStatus::AwaitingModeration);
        $this->entityManager->flush();
    }

    #[ActivityMethod]
    public function notifyViaWebhook(string $event, string $proposalId): void
    {
        $proposal = $this->areaProposalRepository->findOneBy(['uuid' => $proposalId]);

        if (null === $proposal) {
            return;
        }

        $payload = [
            'name' => $event,
            'payload' => [
                'proposalId' => $proposalId,
                'title' => $proposal->getTitle(),
                'city' => $proposal->getCity(),
                'surfaceTotal' => $proposal->getSurfaceTotal(),
                'surfaceToShare' => $proposal->getSurfaceToShare(),
                'soilType' => $proposal->getSoilType(),
                'status' => $proposal->getStatus()->value,
            ],
            'to' => [
                'subscriberId' => $proposalId,
            ],
        ];

        $client = HttpClient::create();
        $client->request('POST', $this->novuApiUrl . '/v1/events/trigger', [
            'headers' => [
                'Authorization' => 'ApiKey ' . $this->novuApiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => $payload,
        ]);
    }

    #[ActivityMethod]
    public function signalOpenMatchingWorkflows(string $proposalId): void
    {
        // Signal all open MatchingWorkflows that a new proposal is available.
        // MatchingWorkflow IDs follow the convention: matching-{requestId}
        // We use a prefix-based list to find running workflows.
        try {
            $listResponse = $this->workflowClient->listWorkflowExecutions(
                "WorkflowType='MatchingWorkflow' AND ExecutionStatus='Running'"
            );

            foreach ($listResponse as $execution) {
                $handle = $this->workflowClient->newRunningWorkflowStub(
                    \Temporal\Client\WorkflowStubInterface::class,
                    $execution->execution->workflowId,
                    $execution->execution->runId ?? null,
                );
                $handle->signal('newProposalAvailable', $proposalId);
            }
        } catch (\Throwable) {
            // Best-effort signal; matching workflows may not exist yet
        }
    }
}
