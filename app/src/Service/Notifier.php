<?php

namespace App\Service;

use App\Entity\News;
use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\User\UserInterface;


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

    public function sendEmailNewsApproved(UserInterface $author, News $news): void
    {
        $this->sendEmail($author, $news, $this->approvedEmailSubject, $this->approvedEmailTemplate);
    }

    public function sendEmailNewsRejected(UserInterface $author, News $news): void
    {
        $this->sendEmail($author, $news, $this->rejectedEmailSubject, $this->rejectedEmailTemplate);
    }

    public function sendEmailNewsBanned(UserInterface $author, News $news): void
    {
        $this->sendEmail($author, $news, $this->bannedEmailSubject, $this->bannedEmailTemplate);
    }

    public function sendEmailNewsToModerate(UserInterface $moderator, News $news): void
    {
        $this->sendEmail($moderator, $news, $this->moderatingEmailSubject, $this->moderatingEmailTemplate);
    }

    protected function sendEmail(UserInterface $user, News $news, string $subject, string $template): void
    {
        $this->mailer->send($this->composeEmail($user, $news, $subject, $template));
    }

    protected function composeEmail(UserInterface $user, News $news, string $subject, string $template): TemplatedEmail
    {
        return (new TemplatedEmail())
            ->to(new Address($user->getEmail()))
            ->subject($subject)
            ->htmlTemplate($template)
            ->context([
                'news' => $news,
                'user' => $user,
            ]);
    }
}