<?php

namespace Flower\MarketingBundle\Model;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * MailTemplate
 *
 */
abstract class MailTemplate
{

    const TYPE_PLAIN = 'plain';
    const TYPE_HTML = 'html';


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
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    protected $type;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    protected $content;

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

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    protected $enabled;

    function __construct()
    {
        $this->enabled = true;
    }


    /**
     * Set id
     *
     * @param string $id
     * @return MailTemplate
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * @return MailTemplate
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
     * Set type
     *
     * @param string $type
     * @return MailTemplate
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set created
     *
     * @param DateTime $created
     * @return MailTemplate
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
     * @return MailTemplate
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
     * Set enabled
     *
     * @param boolean $enabled
     * @return MailTemplate
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
     * Set content
     *
     * @param string $content
     * @return MailTemplate
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getHeader(){
        $header = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> <title>Flowr Template</title> <meta name="viewport" content="width=device-width, initial-scale=1.0"/> </head><body style="margin: 0; padding: 0;">';
        return $header;
    }
    public function getFooter($unsuscribeUrl = null, $email = null){
        $footer = '<table style=";width:100%;">';
        $footer .= '<tr style="height:30px;">';
        $footer .= '<td style="width: 100%; background-color: rgb(255, 255, 255); margin-top 20px; padding-top: 10px; border-top: solid 1px #ddd; text-align:center;">';
        $footer .= '<div style="text-align:center; color: #333; font-size:10px;">';
        $footer .= 'Este email fue enviado a ' . $email . '. Click <a href="' . $unsuscribeUrl . '">acá</a> para desuscribirse.';
        $footer .= '</div>';
        $footer .= '</td>';
        $footer .= '</tr>';
        $footer .= '</table>';
        $footer .= '</body></html>';
        return $footer;
    }

    public function getEmailContent(array $params = null){
        $emailContent = $this->getHeader();
        $emailContent .= $this->getSanitizedContent();
        $emailContent .= $this->getFooter($params["unsuscribeUrl"], $params["email"]);
        return $emailContent;
    }

    public function getSanitizedContent(){
        $raw = $this->getContent();
        $sanitized = str_replace("contenteditable='true'", '', $raw);
        $sanitized = str_replace('contenteditable="true"', '', $sanitized);
        $sanitized = str_replace('class="editable cke_editable cke_editable_inline cke_contents_ltr cke_show_borders"', '', $sanitized);
        return $sanitized;
    }

}
