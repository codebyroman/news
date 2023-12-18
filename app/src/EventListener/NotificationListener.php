<?php
namespace App\EventListener;

use App\Entity\News;
use App\Service\ModeratorResolver;
use App\Service\Notifier;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: News::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'postUpdate', entity: News::class)]
#[AsEntityListener(event: Events::prePersist, method: 'postPersist', entity: News::class)]
class NotificationListener
{
    public function __construct(
        private readonly Notifier $notifier,
        private readonly ModeratorResolver $moderatorResolver,
    )
    {
    }

    public function preUpdate(News $news, PreUpdateEventArgs $event): void
    {
        if (is_null($news->isNeedToNotifyAboutStatus()) && $event->hasChangedField('status')) {
            $news->setNeedToNotifyAboutStatus(true);
        }
    }

    public function postUpdate(News $news, PostUpdateEventArgs $event): void
    {
        if (!$news->isNeedToNotifyAboutStatus()) {
            return;
        }

        if ($news->isActive()) {
            $this->notifier->sendEmailNewsRejected($news->getAuthor(), $news);
        } elseif ($news->isRejected()) {
            $this->notifier->sendEmailNewsRejected($news->getAuthor(), $news);
        } elseif ($news->isBanned()) {
            $this->notifier->sendEmailNewsBanned($news->getAuthor(), $news);
        }

    }

    public function postPersist(News $news, PostPersistEventArgs $event): void
    {
        if ($news->isNeedToNotifyAboutStatus() === false) {
            return;
        }

        if ($news->isModerating()) {
            $moderator = $this->moderatorResolver->resolve($news);

            $news->setModerator($moderator);

            $this->notifier->sendEmailNewsToModerate($moderator, $news);
        }
    }
}