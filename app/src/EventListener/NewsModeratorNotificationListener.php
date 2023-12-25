<?php
namespace App\EventListener;

use App\Entity\News;
use App\Service\ModeratorResolver;
use App\Service\Notifier;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: News::class)]
class NewsModeratorNotificationListener
{
    public function __construct(
        private readonly Notifier $notifier,
        private readonly ModeratorResolver $moderatorResolver,
    )
    {
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