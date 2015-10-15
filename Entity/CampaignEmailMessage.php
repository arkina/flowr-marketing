<?php

namespace Flower\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * CampaignEmailMessage
 *
 * @ORM\Table(name="campaign_mail_message")
 * @ORM\Entity(repositoryClass="Flower\ModelBundle\Repository\CampaignEmailMessageRepository")
 */
class CampaignEmailMessage
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="providerId", type="string", length=255)
     */
    private $providerId;

    /**
     * @ManyToOne(targetEntity="CampaignMail", inversedBy="messages")
     * @JoinColumn(name="campaign_mail_id", referencedColumnName="id")
     * */
    private $campaign;

    /**
     * @var string
     *
     * @ORM\Column(name="sender", type="string", length=255)
     */
    private $sender;

    /**
     * @var string
     *
     * @ORM\Column(name="toemail", type="string", length=255)
     */
    private $toemail;

    /**
     * @var integer
     *
     * @ORM\Column(name="opens", type="integer")
     */
    private $opens;

    /**
     * @var integer
     *
     * @ORM\Column(name="clicks", type="integer")
     */
    private $clicks;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=255)
     */
    private $state;

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
     * @param \Flower\ModelBundle\Entity\CampaignMail $campaign
     * @return CampaignEmailMessage
     */
    public function setCampaign(\Flower\ModelBundle\Entity\CampaignMail $campaign = null)
    {
        $this->campaign = $campaign;

        return $this;
    }

    /**
     * Get campaign
     *
     * @return \Flower\ModelBundle\Entity\CampaignMail 
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
