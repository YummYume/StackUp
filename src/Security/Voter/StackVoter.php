<?php

namespace App\Security\Voter;

use App\Entity\Stack;
use App\Entity\User;
use App\Enum\UserRoleEnum;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class StackVoter extends Voter
{
    public const EDIT = 'EDIT_STACK';
    public const DELETE = 'DELETE_STACK';

    public function __construct(private readonly Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return \in_array($attribute, [self::EDIT, self::DELETE], true) && $subject instanceof Stack;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            self::EDIT => $this->canEditStack($subject, $user),
            self::DELETE => $this->canEditStack($subject, $user),
            default => throw new \LogicException('StackVoter - This code should not be reached.'),
        };
    }

    /**
     * @internal the creator or a moderator can edit stack
     */
    private function canEditStack(Stack $subject, User $user): bool
    {
        return $this->security->isGranted(UserRoleEnum::Moderator->value) || ($subject->getProfile() === $user->getProfile());
    }
}
