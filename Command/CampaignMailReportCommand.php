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

class CampaignMailReportCommand extends ContainerAwareCommand
{

    private $entityManager;
    private $counter;
    private $batchSize;
    private $originalLogger;
    private $entityNameMessage;

    protected function configure()
    {
        $this
                ->setName('flower:campaignreport')
                ->setDescription('Init email campaign')
                ->addArgument(
                        'campaign_id', InputArgument::REQUIRED, 'Campaign id'
                )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $campaignId = $input->getArgument("campaign_id");
        $output->writeln("Init report for campaign " . $campaignId);
        $this->setBatchSize(20);

        $this->setEM($this->getContainer()->get("doctrine.orm.entity_manager"));
        $this->entityNameMessage = $this->getEM()->getClassMetadata("FlowerModelBundle:CampaignEmailMessage")->getName();
        $campaignEmailMessageRepo = $this->getEM()->getRepository("FlowerModelBundle:CampaignEmailMessage");

        /* mail dispatcher */
        $provider = $this->getContainer()->get('hip_mandrill.dispatcher')->getService();

        $this->disableLogging();
        $pageCount = $campaignEmailMessageRepo->getPageCountByCampaignId($campaignId, $this->getBatchSize());
        for ($page = 0; $page < $pageCount; $page++) {
            $messages = $campaignEmailMessageRepo->getByCampaignPaged($campaignId, $page, $this->getBatchSize());
            foreach ($messages as $msg) {
                $this->counter++;

                try {
                    /* get info */
                    $result = $provider->messages->info($msg->getProviderId());

                    /* update data */
                    $msg->setOpens($result['opens']);
                    $msg->setClicks($result['clicks']);
                    $msg->setState($result['state']);
                } catch (\Exception $exc) {
                    
                }
            }
            $this->flushAndClear();
        }

        $this->reEnableLogging();
        $this->finish();

        $output->writeln("Done.");
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
