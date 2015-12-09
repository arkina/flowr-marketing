<?php

namespace Flower\MarketingBundle\Command;

/**
 * Description of newPHPClass
 *
 * @author Juan Manuel Agüero <jaguero@flowcode.com.ar>
 */
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Flower\ModelBundle\Entity\Marketing\CampaignMail;
use Flower\MarketingBundle\Model\ContactListStatus;
use Hip\MandrillBundle\Message;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ValidateMailCommand extends ContainerAwareCommand
{

    /**
     * Entity Manager
     * @var EntityManager
     */
    private $entityManager;

    /**
     * [$counter description]
     * @var integer
     */
    private $counter;
    private $batchSize;
    private $originalLogger;
    private $entityNameContact;

    protected function configure()
    {
        $this
                ->setName('flower:marketing:validate')
                ->setDescription('Init email campaign')
                ->addArgument(
                        'contactlist_id', InputArgument::REQUIRED, 'Contact List id'
                )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $contactlistId = $input->getArgument("contactlist_id");
        $output->writeln("Init validation for campaign " . $contactlistId);
        $this->setBatchSize(20);

        $this->setEM($this->getContainer()->get("doctrine.orm.entity_manager"));
        $this->entityNameContact = $this->getEM()->getClassMetadata("FlowerModelBundle:Clients\Contact")->getName();
        $contactList = $this->getEM()->getRepository("FlowerModelBundle:Marketing\ContactList")->find($contactlistId);

        /* mail $validator */
        $validator = $this->getContainer()->get('flower.marketing.service.mailvalidator');

        $this->disableLogging();

        $ids = array($contactList->getId());
        $pageCount = $this->getEM()->getRepository("FlowerModelBundle:Clients\Contact")->getPageCountAllByContactsLists($ids, $this->getBatchSize());
            for ($page = 0; $page < $pageCount; $page++) {
                $contacts = $this->getEM()->getRepository("FlowerModelBundle:Clients\Contact")->getByContactsLists($ids, $page, $this->getBatchSize());
                foreach ($contacts as $contact) {
                    $contactEmail = $contact->getEmail();
                    $this->counter++;

                    $this->getContainer()->get("logger")->debug("About to validate mail: " . $contactEmail);
                    $output->write("About to validate mail: " . $contactEmail . " ...");

                    $emailGrade = $validator->validate($contactEmail);
                    $contact->setEmailGrade($emailGrade);
                    $output->writeln("validated with grade: " . $emailGrade);
                }
                $this->flushAndClear();
        }

        $contactList->setLastValidation(new \DateTime());
        $contactList->setStatus(ContactListStatus::status_ready);

        $notifService = $this->getContainer()->get("flower.core.service.notification");
        $notifService->notificateContactListValidationFinished($contactList);

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
        $this->entityManager->clear($this->entityNameContact);
    }

}
