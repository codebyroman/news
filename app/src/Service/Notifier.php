<?php

namespace App\Service;

use App\Entity\News;
use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;


class Notifier
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly string $approvedEmailSubject,
        private readonly string $rejectedEmailSubject,
        private readonly string $bannedEmailSubject,
        private readonly string $moderatingEmailSubject,
        private readonly string $approvedEmailTemplate,
        private readonly string $rejectedEmailTemplate,
        private readonly string $bannedEmailTemplate,
        private readonly string $moderatingEmailTemplate,
    )
    {
    }

    public function sendEmailNewsApproved(User $author, News $news): void
    {
        $this->mailer->send($this->composeEmail($author, $this->approvedEmailSubject, $this->approvedEmailTemplate,
            ['news' => $news]));
    }

    public function sendEmailNewsRejected(User $author, News $news): void
    {
        $this->mailer->send($this->composeEmail($author, $this->rejectedEmailSubject, $this->rejectedEmailTemplate,
            ['news' => $news]));
    }

    public function sendEmailNewsBanned(User $author, News $news): void
    {
        $this->mailer->send($this->composeEmail($author, $this->bannedEmailSubject, $this->bannedEmailTemplate,
            ['news' => $news]));
    }

    public function sendEmailNewsToModerate(User $moderator, News $news): void
    {
        $this->mailer->send($this->composeEmail($moderator, $this->moderatingEmailSubject, $this->moderatingEmailTemplate,
            ['news' => $news]));
    }

    protected function composeEmail(User $user, string $subject, string $template, array $context = []): TemplatedEmail
    {
        return (new TemplatedEmail())
            ->to(new Address($user->getEmail()))
            ->subject($subject)
            ->htmlTemplate($template)
            ->context(array_merge(['user' => $user], $context));
    }
}