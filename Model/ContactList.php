<?php

namespace Flower\MarketingBundle\Model;

use DateTime;
use Flower\MarketingBundle\Model\ContactListStatus;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ContactList
 *
 */
abstract class ContactList
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @ManyToMany(targetEntity="\Flower\ModelBundle\Entity\Clients\Contact")
     * @JoinTable(name="contactlists_contacts",
     *      joinColumns={@JoinColumn(name="contactlist_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="contact_id", referencedColumnName="id")}
     *      )
     **/
    protected $contacts;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    protected $enabled;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="last_validation", type="datetime", nullable=true)
     */
    protected $lastValidation;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="status", type="string", length=255, nullable=true)
     */
    protected $status;

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

    public function __construct() {
        $this->contacts = new ArrayCollection();
        $this->enabled = true;
        $this->status = ContactListStatus::status_validation_needed;
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
     * @return ContactList
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
     * Set enabled
     *
     * @param boolean $enabled
     * @return ContactList
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
     * @return ContactList
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
     * @return ContactList
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
     * Set lastValidation
     *
     * @param DateTime $lastValidation
     * @return ContactList
     */
    public function setLastValidation($lastValidation)
    {
        $this->lastValidation = $lastValidation;

        return $this;
    }

    /**
     * Get lastValidation
     *
     * @return DateTime
     */
    public function getLastValidation()
    {
        return $this->lastValidation;
    }

    /**
     * Add contacts
     *
     * @param \Flower\ModelBundle\Entity\Clients\Contact $contacts
     * @return ContactList
     */
    public function addContact(\Flower\ModelBundle\Entity\Clients\Contact $contacts)
    {
        $this->contacts[] = $contacts;

        return $this;
    }

    /**
     * Remove contacts
     *
     * @param \Flower\ModelBundle\Entity\Clients\Contact $contacts
     */
    public function removeContact(\Flower\ModelBundle\Entity\Clients\Contact $contacts)
    {
        $this->contacts->removeElement($contacts);
    }

    /**
     * Get contacts
     *
     * @return Collection
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return ContactList
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
        $status = $this->status;
        if($this->status == ContactListStatus::status_ready){
            if($this->lastValidation->add(new \DateInterval("P3M")) >= new \DateTime()){
                $status = $this->status;
            }else{
                $status = ContactListStatus::status_validation_needed;
            }
        }
        return $status;
    }

}
