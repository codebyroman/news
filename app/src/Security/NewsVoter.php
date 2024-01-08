<?php
namespace App\Security;

use App\Entity\News;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;


class NewsVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::VIEW, self::EDIT])) {
            return false;
        }

        if (!$subject instanceof News) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        /** @var News $news */
        $news = $subject;

        return match($attribute) {
            self::VIEW => $this->canView($news, $user),
            self::EDIT => $this->canEdit($news, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canView(News $news, ?User $user): bool
    {
        if ($this->canEdit($news, $user)) {
            return true;
        }

        return $news->isApproved();
    }

    private function canEdit(News $news, ?User $user): bool
    {
        if (!$user || !$user->isActive()) {
            return false;
        }

        if ($user->isAdmin()
            || ($user->isModerator() && $user === $news->getModerator())
            || ($user->isAuthor() && $user === $news->getAuthor())
        ) {
            return true;
        }

        return false;
    }
}
