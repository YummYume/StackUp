<?php

namespace App\Manager;

use App\Entity\Request;
use App\Enum\RequestStatusEnum;
use App\Repository\RequestRepository;

final class VoteManager
{
    public function __construct(private readonly RequestRepository $requestRepository)
    {
    }

    public function handleRequestStatus(Request $request): void
    {
        if (RequestStatusEnum::Pending !== $request->getStatus()) {
            return;
        }

        $votes = $request->getDownAndUpVotes();
        $totalVotes = array_sum($votes);

        if (200 > $totalVotes) {
            return;
        }

        $votesThreshold = ($totalVotes * 80) / 100;

        if ($votes['upvotes'] >= $votesThreshold) {
            $request->setStatus(RequestStatusEnum::Accepted);
        } elseif ($votes['downvotes'] >= $votesThreshold) {
            $request->setStatus(RequestStatusEnum::Rejected);
        }

        $this->requestRepository->save($request, true);
    }
}
