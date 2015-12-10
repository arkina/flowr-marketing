<?php

namespace Flower\MarketingBundle\Model;

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
use JMS\Serializer\Annotation\Groups;

/**
 * CampaignMail
 *
 */
abstract class CampaignMail
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
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Groups({"public_api"})
     */
    protected $name;

    /**
     * @ManyToOne(targetEntity="\Flower\ModelBundle\Entity\User\User")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     * @Groups({"public_api"})
     * */
    protected $assignee;

    /**
     * @var integer
     *
     * @ORM\Column(name="queued", type="integer")
     * @Groups({"public_api"})
     */
    protected $queued;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     * @Groups({"public_api"})
     */
    protected $status;

    /**
     * @ManyToOne(targetEntity="\Flower\ModelBundle\Entity\Marketing\MailTemplate")
     * @JoinColumn(name="template_id", referencedColumnName="id")
     * */
    protected $template;

    /**
     * @ManyToMany(targetEntity="\Flower\ModelBundle\Entity\Marketing\ContactList")
     * @JoinTable(name="campaignmail_contactlist",
     *      joinColumns={@JoinColumn(name="campaign_mail_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="contact_list_id", referencedColumnName="id")}
     *      )
     */
    protected $contactLists;

    /**
     * @var string
     *
     * @ORM\Column(name="mailFrom", type="string", length=255)
     * @Groups({"public_api"})
     */
    protected $mailFrom;

    /**
     * @var string
     *
     * @ORM\Column(name="mailSubject", type="string", length=255)
     * @Groups({"public_api"})
     */
    protected $mailSubject;

    /**
     * @var string
     *
     * @ORM\Column(name="mailFromName", type="string", length=255)
     * @Groups({"public_api"})
     */
    protected $mailFromName;

    /**
     * @OneToMany(targetEntity="\Flower\ModelBundle\Entity\Marketing\CampaignEmailMessage", mappedBy="campaign")
     * */
    protected $messages;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    protected $enabled;

    /**
     * @var DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    protected $created;

    /**
     * @var DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated", type="datetime")
     */
    protected $updated;

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
     * @param \Flower\ModelBundle\Entity\Marketing\MailTemplate $template
     * @return CampaignMail
     */
    public function setTemplate(\Flower\ModelBundle\Entity\Marketing\MailTemplate $template = null)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template
     *
     * @return \Flower\ModelBundle\Entity\Marketing\MailTemplate
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Add contactLists
     *
     * @param \Flower\ModelBundle\Entity\Marketing\ContactList $contactLists
     * @return CampaignMail
     */
    public function addContactList(\Flower\ModelBundle\Entity\Marketing\ContactList $contactLists)
    {
        $this->contactLists[] = $contactLists;

        return $this;
    }

    /**
     * Remove contactLists
     *
     * @param \Flower\ModelBundle\Entity\Marketing\ContactList $contactLists
     */
    public function removeContactList(\Flower\ModelBundle\Entity\Marketing\ContactList $contactLists)
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
     * @param \Flower\ModelBundle\Entity\Marketing\CampaignEmailMessage $messages
     * @return CampaignMail
     */
    public function addMessage(\Flower\ModelBundle\Entity\Marketing\CampaignEmailMessage $messages)
    {
        $this->messages[] = $messages;

        return $this;
    }

    /**
     * Remove messages
     *
     * @param \Flower\ModelBundle\Entity\Marketing\CampaignEmailMessage $messages
     */
    public function removeMessage(\Flower\ModelBundle\Entity\Marketing\CampaignEmailMessage $messages)
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


    /**
     * Set assignee
     *
     * @param \Flower\ModelBundle\Entity\User\User $assignee
     * @return Account
     */
    public function setAssignee(\Flower\ModelBundle\Entity\User\User $assignee = null)
    {
        $this->assignee = $assignee;

        return $this;
    }

    /**
     * Get assignee
     *
     * @return \Flower\ModelBundle\Entity\User\User
     */
    public function getAssignee()
    {
        return $this->assignee;
    }


}
