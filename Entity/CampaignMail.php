<?php

namespace Flower\ModelBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * CampaignMail
 *
 * @ORM\Table(name="campaign_mail")
 * @ORM\Entity(repositoryClass="Flower\ModelBundle\Repository\CampaignMailRepository")
 */
class CampaignMail
{

    const STATUS_DRAFT = "status_draft";
    const STATUS_IN_PROGRESS = "status_in_progress";
    const STATUS_FINISHED = "status_finished";

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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="queued", type="integer")
     */
    private $queued;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @ManyToOne(targetEntity="MailTemplate")
     * @JoinColumn(name="template_id", referencedColumnName="id")
     * */
    private $template;

    /**
     * @ManyToMany(targetEntity="ContactList")
     * @JoinTable(name="campaignmail_contactlist",
     *      joinColumns={@JoinColumn(name="campaign_mail_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="contact_list_id", referencedColumnName="id")}
     *      )
     */
    private $contactLists;

    /**
     * @var string
     *
     * @ORM\Column(name="mailFrom", type="string", length=255)
     */
    private $mailFrom;

    /**
     * @var string
     *
     * @ORM\Column(name="mailSubject", type="string", length=255)
     */
    private $mailSubject;

    /**
     * @var string
     *
     * @ORM\Column(name="mailFromName", type="string", length=255)
     */
    private $mailFromName;

    /**
     * @OneToMany(targetEntity="CampaignEmailMessage", mappedBy="campaign")
     * */
    private $messages;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;

    /**
     * @var DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;

    public function __construct()
    {
        $this->status = self::STATUS_DRAFT;
        $this->contactLists = new ArrayCollection();
        $this->enabled = true;
        $this->messages = new ArrayCollection();
        $this->queued = 0;
    }

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
     * Set name
     *
     * @param string $name
     * @return CampaignMail
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set mailFrom
     *
     * @param string $mailFrom
     * @return CampaignMail
     */
    public function setMailFrom($mailFrom)
    {
        $this->mailFrom = $mailFrom;

        return $this;
    }

    /**
     * Get mailFrom
     *
     * @return string 
     */
    public function getMailFrom()
    {
        return $this->mailFrom;
    }

    /**
     * Set mailSubject
     *
     * @param string $mailSubject
     * @return CampaignMail
     */
    public function setMailSubject($mailSubject)
    {
        $this->mailSubject = $mailSubject;

        return $this;
    }

    /**
     * Get mailSubject
     *
     * @return string 
     */
    public function getMailSubject()
    {
        return $this->mailSubject;
    }

    /**
     * Set mailFromName
     *
     * @param string $mailFromName
     * @return CampaignMail
     */
    public function setMailFromName($mailFromName)
    {
        $this->mailFromName = $mailFromName;

        return $this;
    }

    /**
     * Get mailFromName
     *
     * @return string 
     */
    public function getMailFromName()
    {
        return $this->mailFromName;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return CampaignMail
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set created
     *
     * @param DateTime $created
     * @return CampaignMail
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param DateTime $updated
     * @return CampaignMail
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set template
     *
     * @param MailTemplate $template
     * @return CampaignMail
     */
    public function setTemplate(MailTemplate $template = null)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template
     *
     * @return MailTemplate 
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Add contactLists
     *
     * @param ContactList $contactLists
     * @return CampaignMail
     */
    public function addContactList(ContactList $contactLists)
    {
        $this->contactLists[] = $contactLists;

        return $this;
    }

    /**
     * Remove contactLists
     *
     * @param ContactList $contactLists
     */
    public function removeContactList(ContactList $contactLists)
    {
        $this->contactLists->removeElement($contactLists);
    }

    /**
     * Get contactLists
     *
     * @return Collection 
     */
    public function getContactLists()
    {
        return $this->contactLists;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return CampaignMail
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Add messages
     *
     * @param CampaignEmailMessage $messages
     * @return CampaignMail
     */
    public function addMessage(CampaignEmailMessage $messages)
    {
        $this->messages[] = $messages;

        return $this;
    }

    /**
     * Remove messages
     *
     * @param CampaignEmailMessage $messages
     */
    public function removeMessage(CampaignEmailMessage $messages)
    {
        $this->messages->removeElement($messages);
    }

    /**
     * Get messages
     *
     * @return Collection 
     */
    public function getMessages()
    {
        return $this->messages;
    }


    /**
     * Set queued
     *
     * @param integer $queued
     * @return CampaignMail
     */
    public function setQueued($queued)
    {
        $this->queued = $queued;

        return $this;
    }

    /**
     * Get queued
     *
     * @return integer 
     */
    public function getQueued()
    {
        return $this->queued;
    }
}
