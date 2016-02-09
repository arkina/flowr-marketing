<?php

namespace Flower\MarketingBundle\Service;


use Flower\ModelBundle\Entity\Marketing\CampaignMail;

/**
 * Class CampaignMailService
 * @package Flower\MarketingBundle\Service
 */
class CampaignMailService
{
    private $campaignMailRepository;
    private $campaignMailMessageRepository;

    public function __construct($campaignMailRepository, $campaignMailMessageRepository)
    {
        $this->campaignMailRepository = $campaignMailRepository;
        $this->campaignMailMessageRepository = $campaignMailMessageRepository;
    }


    public function getAllPaged($filter = array(), $sort = array(), $max = 20, $first = 0)
    {
        return $this->campaignMailRepository->getAll($filter, $max, $first);
    }

    public function getCountAll(){
        return $this->campaignMailRepository->getCountAll();
    }

    public function getStatsCountsByCampaign(CampaignMail $campaign, $from, $to){

            return $this->campaignMailMessageRepository->getOpensCount($campaign->getId(), $from, $to);

    }
}