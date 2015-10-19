<?php

namespace Flower\MarketingBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * CampaignEmailMessage
 *
 */
abstract class CampaignEmailMessage
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="providerId", type="string", length=255)
     */
    protected $providerId;

    /**
     * @ManyToOne(targetEntity="\Flower\ModelBundle\Entity\Marketing\CampaignMail", inversedBy="messages")
     * @JoinColumn(name="campaign_mail_id", referencedColumnName="id")
     * */
    protected $campaign;

    /**
     * @var string
     *
     * @ORM\Column(name="sender", type="string", length=255)
     */
    protected $sender;

    /**
     * @var string
     *
     * @ORM\Column(name="toemail", type="string", length=255)
     */
    protected $toemail;

    /**
     * @var integer
     *
     * @ORM\Column(name="opens", type="integer")
     */
    protected $opens;

    /**
     * @var integer
     *
     * @ORM\Column(name="clicks", type="integer")
     */
    protected $clicks;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=255)
     */
    protected $state;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set providerId
     *
     * @param string $providerId
     * @return CampaignEmailMessage
     */
    public function setProviderId($providerId)
    {
        $this->providerId = $providerId;

        return $this;
    }

    /**
     * Get providerId
     *
     * @return string 
     */
    public function getProviderId()
    {
        return $this->providerId;
    }

    /**
     * Set sender
     *
     * @param string $sender
     * @return CampaignEmailMessage
     */
    public function setSender($sender)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get sender
     *
     * @return string 
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set opens
     *
     * @param integer $opens
     * @return CampaignEmailMessage
     */
    public function setOpens($opens)
    {
        $this->opens = $opens;

        return $this;
    }

    /**
     * Get opens
     *
     * @return integer 
     */
    public function getOpens()
    {
        return $this->opens;
    }

    /**
     * Set clicks
     *
     * @param integer $clicks
     * @return CampaignEmailMessage
     */
    public function setClicks($clicks)
    {
        $this->clicks = $clicks;

        return $this;
    }

    /**
     * Get clicks
     *
     * @return integer 
     */
    public function getClicks()
    {
        return $this->clicks;
    }

    /**
     * Set state
     *
     * @param string $state
     * @return CampaignEmailMessage
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set campaign
     *
     * @param \Flower\ModelBundle\Entity\Marketing\CampaignMail $campaign
     * @return CampaignEmailMessage
     */
    public function setCampaign(\Flower\ModelBundle\Entity\Marketing\CampaignMail $campaign = null)
    {
        $this->campaign = $campaign;

        return $this;
    }

    /**
     * Get campaign
     *
     * @return \Flower\ModelBundle\Entity\Marketing\CampaignMail 
     */
    public function getCampaign()
    {
        return $this->campaign;
    }


    /**
     * Set toemail
     *
     * @param string $toemail
     * @return CampaignEmailMessage
     */
    public function setToemail($toemail)
    {
        $this->toemail = $toemail;

        return $this;
    }

    /**
     * Get toemail
     *
     * @return string 
     */
    public function getToemail()
    {
        return $this->toemail;
    }
}
