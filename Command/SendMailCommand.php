<?php

namespace Flower\MarketingBundle\Command;

/**
 * Description of newPHPClass
 *
 * @author Juan Manuel Agüero <jaguero@flowcode.com.ar>
 */
use Doctrine\ORM\EntityManagerInterface;
use Flower\ModelBundle\Entity\CampaignMail;
use Hip\MandrillBundle\Message;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
        $this->entityNameContact = $this->getEM()->getClassMetadata("FlowerModelBundle:Contact")->getName();
        $campaignEmail = $this->getEM()->getRepository("FlowerModelBundle:CampaignMail")->find($campaignId);
        $campaignEmailMessageRepo = $this->getEM()->getRepository("FlowerModelBundle:CampaignEmailMessage");
        //$campaignEmail = new \Flower\ModelBundle\Entity\CampaignMail();

        /* mail dispatcher */
        $dispatcher = $this->getContainer()->get('hip_mandrill.dispatcher');

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
        $pageCount = $this->getEM()->getRepository("FlowerModelBundle:Contact")->getPageCountByContactsLists($ids, $this->getBatchSize());
            for ($page = 0; $page < $pageCount; $page++) {
                $contacts = $this->getEM()->getRepository("FlowerModelBundle:Contact")->getDistinctEmailsByContactsLists($ids, $page, $this->getBatchSize());
                foreach ($contacts as $contactEmail) {
                    $contactEmail = $contactEmail["email"];
                    $this->counter++;

                    $this->getContainer()->get("logger")->debug("About to send mail to " . $contactEmail);
                    $message = new Message();
                    $message->setFromEmail($mailFrom)
                            ->setFromName($mailFromName)
                            ->addTo($contactEmail)
                            ->setSubject($mailSubject)
                            ->setHtml($campaignEmail->getTemplate()->getContent());

                    $result = $dispatcher->send($message);
                    //$result = array(array("_id"=> $contactEmail,"status" => 2) );
                    $this->getContainer()->get("logger")->debug("Result: " . json_encode($result));
                    $campaignEmailMessageRepo->rawInsert($stmt, $result[0]["_id"], $campaignId, $mailFrom,$contactEmail, $result[0]["status"]);
                }
                $this->flushAndClear();
        }

        $campaignEmail->setStatus(CampaignMail::STATUS_FINISHED);

        $this->reEnableLogging();
        $this->finish();
    }

    /**
     * Get the entity manager.
     * @return EntityManagerInterface em.
     */
    function getEM()
    {
        return $this->entityManager;
    }

    function setEM($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    function getCounter()
    {
        return $this->counter;
    }

    function getBatchSize()
    {
        return $this->batchSize;
    }

    function setCounter($counter)
    {
        $this->counter = $counter;
    }

    function setBatchSize($batchSize)
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
