<?php

namespace Flower\MarketingBundle\Service;

use Flowcode\NotificationBundle\Senders\EmailSenderResponse;
use Flowcode\NotificationBundle\Senders\EmailSenderInterface;

/**
 * EmailDispatcherService.
 */
class EmailDispatcherService
{
    /**
     * Email Sender.
     * @var EmailSenderInterface sender.
     */
    protected $sender;

    public function __construct(EmailSenderInterface $sender)
    {
        $this->sender = $sender;
    }

    /**
     * Dispatch mail message.
     * @param  string  $toEmail   mail info
     * @param  string  $toName    mail info
     * @param  string  $fromEmail mail info
     * @param  string  $fromName  mail info
     * @param  string  $subject   mail info
     * @param  string  $body      mail info
     * @param  boolean $isHTML    ishtml.
     * @return EmailSenderResponse             response.
     */
    public function dispatch($toEmail, $toName, $fromEmail, $fromName, $subject, $body, $isHTML = false)
    {
        return $this->sender->send($toEmail, $toName, $fromEmail, $fromName, $subject, $body, $isHTML);
    }
}
