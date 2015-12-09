<?php

namespace Flower\MarketingBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Flower\ModelBundle\Entity\Marketing\CampaignMail;
use Flower\ModelBundle\Entity\Marketing\MailTemplate;
use Hip\MandrillBundle\Message;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Mailgun\Mailgun;
use Flowcode\NotificationBundle\Senders\EmailSenderResponse;

/**
* Description of newPHPClass
*
* @author Juan Manuel Agüero <jaguero@flowcode.com.ar>
*/
class SendMailCommand extends ContainerAwareCommand
{
    private $entityManager;
    private $counter;
    private $batchSize;
    private $originalLogger;
    private $entityNameContact;

    protected function configure()
    {
        $this
        ->setName('flower:initcampaign')
        ->setDescription('Init email campaign')
        ->addArgument(
        'campaign_id', InputArgument::REQUIRED, 'Campaign id'
        )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $campaignId = $input->getArgument("campaign_id");
        $output->writeln("Init dispatcher for campaign " . $campaignId);
        $this->setBatchSize(20);

        $this->setEM($this->getContainer()->get("doctrine.orm.entity_manager"));
        $this->entityNameContact = $this->getEM()->getClassMetadata("FlowerModelBundle:Clients\Contact")->getName();
        $campaignEmail = $this->getEM()->getRepository("FlowerModelBundle:Marketing\CampaignMail")->find($campaignId);
        $campaignEmailMessageRepo = $this->getEM()->getRepository("FlowerModelBundle:Marketing\CampaignEmailMessage");

        /* mail sender */
        $mailgunApiKey = $this->getContainer()->getParameter('mailgun_api_key');
        $mailgunDomain = $this->getContainer()->getParameter('mailgun_domain');
        $mgClient = new Mailgun($mailgunApiKey);
        $domain = $mailgunDomain;
        $output->writeln("Using mailgun with apikey: " . $mailgunApiKey . ", and domain: " . $mailgunDomain);


        /* campaignData */
        $mailFrom = $campaignEmail->getMailFrom();
        $mailSubject = $campaignEmail->getMailSubject();
        $mailFromName = $campaignEmail->getMailFromName();

        $stmt = $campaignEmailMessageRepo->prepareRawInsert();

        $contactLists = $campaignEmail->getContactLists();

        $this->disableLogging();

        $ids = array();
        foreach ($contactLists as $list) {
            $ids[] = $list->getId();
        }
        $pageCount = $this->getEM()->getRepository("FlowerModelBundle:Clients\Contact")->getDistinctPageCountByContactsLists($ids, $this->getBatchSize());
        for ($page = 0; $page < $pageCount; $page++) {
            $contacts = $this->getEM()->getRepository("FlowerModelBundle:Clients\Contact")->getDistinctEmailsByContactsLists($ids, $page, $this->getBatchSize());
            foreach ($contacts as $contactArr) {
                $contactEmail = $contactArr["email"];
                $this->counter++;

                $this->getContainer()->get("logger")->debug("About to send mail to " . $contactEmail);

                $messageBldr = $mgClient->MessageBuilder();
                $messageBldr->setFromAddress($mailFrom, array("first" => $mailFromName));
                $messageBldr->addToRecipient($contactEmail);
                $messageBldr->setSubject($mailSubject);

                $urlBase = $this->getContainer()->getParameter("host.scheme") . "://" .  $this->getContainer()->getParameter("host.host");
                $unsuscribeUrl = $urlBase . $this->getContainer()->get("router")->generate("contactlist_unsuscribe_confirm", array("id" => $contactArr["id"]));

                $messageBldr->addCustomHeader("List-Unsubscribe", $unsuscribeUrl);

                $body = $campaignEmail->getTemplate()->getEmailContent(array("unsuscribeUrl" => $unsuscribeUrl, "email" => $contactEmail));
                if ($campaignEmail->getTemplate()->getType() == MailTemplate::TYPE_HTML) {
                    $messageBldr->setHtmlBody($body);
                } else {
                    $messageBldr->setTextBody($body);
                }

                $message = $messageBldr->getMessage();

                # Make the call to the client.
                $resultRaw = $mgClient->sendMessage($domain, $message);
                $result = $resultRaw->http_response_body;

                $externalId = null;
                if ($result->id) {
                    $externalId = $result->id;
                    $status = EmailSenderResponse::status_sent;
                } else {
                    $status = EmailSenderResponse::status_error;
                }

                $campaignEmailMessageRepo->rawInsert($stmt, $externalId, $campaignId, $mailFrom, $contactEmail, $status);
            }
            $this->flushAndClear();
        }

        $campaignEmail->setStatus(CampaignMail::STATUS_FINISHED);

        $this->reEnableLogging();
        $this->finish();

        /* notifications */
        $notifService = $this->getContainer()->get("flower.core.service.notification");
        $notifService->notificateEmailCampaignFinished($campaignEmail);

    }

    /**
    * Get the entity manager.
    * @return EntityManagerInterface em.
    */
    public function getEM()
    {
        return $this->entityManager;
    }

    public function setEM($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getCounter()
    {
        return $this->counter;
    }

    public function getBatchSize()
    {
        return $this->batchSize;
    }

    public function setCounter($counter)
    {
        $this->counter = $counter;
    }

    public function setBatchSize($batchSize)
    {
        $this->batchSize = $batchSize;
    }

    /**
    * Disable Doctrine logging
    */
    protected function disableLogging()
    {
        $config = $this->entityManager->getConnection()->getConfiguration();
        $this->originalLogger = $config->getSQLLogger();
        $config->setSQLLogger(null);
    }

    /**
    * Re-enable Doctrine logging
    */
    protected function reEnableLogging()
    {
        $config = $this->entityManager->getConnection()->getConfiguration();
        $config->setSQLLogger($this->originalLogger);
    }

    /**
    * Do ending process tasks.
    *
    */
    public function finish()
    {
        $this->flushAndClear();

        $this->reEnableLogging();
    }

    /**
    * Flush and clear the entity manager
    */
    protected function flushAndClear()
    {
        $this->entityManager->flush();
    }
}
