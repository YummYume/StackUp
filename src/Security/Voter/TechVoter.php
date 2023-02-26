<?php

namespace App\Security\Voter;

use App\Entity\Tech;
use App\Entity\User;
use App\Enum\RequestStatusEnum;
use App\Enum\UserRoleEnum;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class TechVoter extends Voter
{
    public const EDIT = 'EDIT_STACK';
    public const DELETE = 'DELETE_STACK';

    public function __construct(private readonly Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return \in_array($attribute, [self::EDIT, self::DELETE], true) && $subject instanceof Tech;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            self::EDIT => $this->canActionTech($subject, $user),
            self::DELETE => $this->canActionTech($subject, $user),
            default => throw new \LogicException('TechVoter - This code should not be reached.'),
        };
    }

    /**
     * @internal the creator or a moderator can edit or delete a tech if it's pending
     */
    private function canActionTech(Tech $subject, User $user): bool
    {
        return
        RequestStatusEnum::Pending === $subject->getRequest()->getStatus() &&
        ($this->security->isGranted(UserRoleEnum::Moderator->value) || $user === $subject->getCreatedBy())
        ;
    }
}
