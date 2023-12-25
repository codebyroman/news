<?php
namespace App\EventListener;

use App\Entity\News;
use App\Service\Notifier;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: News::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'postUpdate', entity: News::class)]
class NewsAuthorNotificationListener
{
    public function __construct(
        private readonly Notifier $notifier,
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

        if ($news->isApproved()) {
            $this->notifier->sendEmailNewsApproved($news->getAuthor(), $news);
        } elseif ($news->isRejected()) {
            $this->notifier->sendEmailNewsRejected($news->getAuthor(), $news);
        } elseif ($news->isBanned()) {
            $this->notifier->sendEmailNewsBanned($news->getAuthor(), $news);
        }
    }
}