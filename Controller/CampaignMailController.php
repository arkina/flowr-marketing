<?php

namespace Flower\MarketingBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Flower\MarketingBundle\Form\Type\CampaignMailType;
use Flower\ModelBundle\Entity\Marketing\CampaignMail;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * CampaignMail controller.
 *
 * @Route("/campaignmail")
 */
class CampaignMailController extends Controller
{

    /**
     * Lists all CampaignMail entities.
     *
     * @Route("/", name="campaignmail")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $qb = $em->getRepository('FlowerModelBundle:Marketing\CampaignMail')->createQueryBuilder('c');
        
        $this->addQueryBuilderSort($qb, 'campaignmail');
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20, array(
            'defaultSortFieldName' => 'c.created',
            'defaultSortDirection' => 'DESC'
        ));

        return array(
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a CampaignMail entity.
     *
     * @Route("/{id}/show", name="campaignmail_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(CampaignMail $campaignmail)
    {
        $deleteForm = $this->createDeleteForm($campaignmail->getId(), 'campaignmail_delete');
        $em = $this->getDoctrine()->getManager();
        $procesed = $em->getRepository("FlowerModelBundle:Marketing\CampaignEmailMessage")->getCountByCampaign($campaignmail->getId());
        $opens = $em->getRepository("FlowerModelBundle:Marketing\CampaignEmailMessage")->getOpensByCampaign($campaignmail->getId());
        $clicks = $em->getRepository("FlowerModelBundle:Marketing\CampaignEmailMessage")->getClicksByCampaign($campaignmail->getId());

        if (!is_null($campaignmail->getQueued()) && $campaignmail->getQueued() > 0) {
            $emailSentRatio = ($procesed * 100) / $campaignmail->getQueued();
        } else {
            $emailSentRatio = 0;
        }
        if (!is_null($campaignmail->getQueued()) && $campaignmail->getQueued() > 0) {
            $emailOpensRatio = ($opens * 100) / $campaignmail->getQueued();
        } else {
            $emailOpensRatio = 0;
        }
        if (!is_null($campaignmail->getQueued()) && $campaignmail->getQueued() > 0) {
            $emailClicksRatio = ($clicks * 100) / $campaignmail->getQueued();
        } else {
            $emailClicksRatio = 0;
        }

        return array(
            'emailSent' => $procesed,
            'emailSentRatio' => $emailSentRatio,
            'emailOpens' => $opens,
            'emailOpensRatio' => $emailOpensRatio,
            'emailClicks' => $clicks,
            'emailClicksRatio' => $emailClicksRatio,
            'campaignmail' => $campaignmail,
            
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new CampaignMail entity.
     *
     * @Route("/new", name="campaignmail_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $campaignmail = new CampaignMail();
        $form = $this->createForm(new CampaignMailType(), $campaignmail);

        return array(
            'campaignmail' => $campaignmail,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new CampaignMail entity.
     *
     * @Route("/create", name="campaignmail_create")
     * @Method("POST")
     * @Template("FlowerMarketingBundle:CampaignMail:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $campaignmail = new CampaignMail();
        $form = $this->createForm(new CampaignMailType(), $campaignmail);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($campaignmail);
            $em->flush();

            return $this->redirect($this->generateUrl('campaignmail_show', array('id' => $campaignmail->getId())));
        }

        return array(
            'campaignmail' => $campaignmail,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing CampaignMail entity.
     *
     * @Route("/{id}/edit", name="campaignmail_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(CampaignMail $campaignmail)
    {
        $editForm = $this->createForm(new CampaignMailType(), $campaignmail, array(
            'action' => $this->generateUrl('campaignmail_update', array('id' => $campaignmail->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($campaignmail->getId(), 'campaignmail_delete');

        return array(
            'campaignmail' => $campaignmail,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing CampaignMail entity.
     *
     * @Route("/{id}/update", name="campaignmail_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerMarketingBundle:CampaignMail:edit.html.twig")
     */
    public function updateAction(CampaignMail $campaignmail, Request $request)
    {
        $editForm = $this->createForm(new CampaignMailType(), $campaignmail, array(
            'action' => $this->generateUrl('campaignmail_update', array('id' => $campaignmail->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('campaignmail_show', array('id' => $campaignmail->getId())));
        }
        $deleteForm = $this->createDeleteForm($campaignmail->getId(), 'campaignmail_delete');

        return array(
            'campaignmail' => $campaignmail,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing CampaignMail entity.
     *
     * @Route("/{id}/copy", name="campaignmail_copy", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function copyAction(CampaignMail $campaignmail)
    {

        $campaignmailCopy = new CampaignMail();
        $campaignmailCopy->setName($campaignmail->getName() . " (Copy)");
        $campaignmailCopy->setMailFrom($campaignmail->getMailFrom());
        $campaignmailCopy->setMailFromName($campaignmail->getMailFromName());
        $campaignmailCopy->setMailSubject($campaignmail->getMailSubject());
        $campaignmailCopy->setTemplate($campaignmail->getTemplate());

        foreach ($campaignmail->getContactLists() as $contactList) {
            $campaignmailCopy->addContactList($contactList);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($campaignmailCopy);
        $em->flush();


        return $this->redirect($this->generateUrl('campaignmail_show', array("id" => $campaignmailCopy->getId())));
    }

    /**
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="campaignmail_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('campaignmail', $field, $type);

        return $this->redirect($this->generateUrl('campaignmail'));
    }

    /**
     * Displays a form to edit an existing CampaignMail entity.
     *
     * @Route("/{id}/launch", name="campaignmail_launch", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function launchAction(CampaignMail $campaign)
    {
        /* launch process */
        $rootDir = $this->get('kernel')->getRootDir();
        $env = $this->container->get('kernel')->getEnvironment();
        $commandCall = "php " . $rootDir . "/console flower:initcampaign --env=" . $env . "  " . $campaign->getId() . " > /dev/null &";
        exec($commandCall);
        $this->get("logger")->info("Run: " . $commandCall);
        $em = $this->getDoctrine()->getManager();
        
        $contactLists = $campaign->getContactLists();
        $queued = 0;
        $ids = array();
        foreach ($contactLists as $contactlist) {
            $ids[] = $contactlist->getId();
        }
        $queued = $em->getRepository("FlowerModelBundle:Clients\Contact")->getCountByContactsLists($ids);

        $campaign->setStatus(CampaignMail::STATUS_IN_PROGRESS);
        $campaign->setQueued($queued);
        
        $em->flush();

        return $this->redirect($this->generateUrl('campaignmail_show', array("id" => $campaign->getId())));
    }

    /**
     * @param string $name  session name
     * @param string $field field name
     * @param string $type  sort type ("ASC"/"DESC")
     */
    protected function setOrder($name, $field, $type = 'ASC')
    {
        $this->getRequest()->getSession()->set('sort.' . $name, array('field' => $field, 'type' => $type));
    }

    /**
     * @param  string $name
     * @return array
     */
    protected function getOrder($name)
    {
        $session = $this->getRequest()->getSession();

        return $session->has('sort.' . $name) ? $session->get('sort.' . $name) : null;
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $name
     */
    protected function addQueryBuilderSort(QueryBuilder $qb, $name)
    {
        $alias = current($qb->getDQLPart('from'))->getAlias();
        if (is_array($order = $this->getOrder($name))) {
            $qb->orderBy($alias . '.' . $order['field'], $order['type']);
        }
    }

    /**
     * Deletes a CampaignMail entity.
     *
     * @Route("/{id}/delete", name="campaignmail_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(CampaignMail $campaignmail, Request $request)
    {
        $form = $this->createDeleteForm($campaignmail->getId(), 'campaignmail_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($campaignmail);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('campaignmail'));
    }

    /**
     * Create Delete form
     *
     * @param integer                       $id
     * @param string                        $route
     * @return Form
     */
    protected function createDeleteForm($id, $route)
    {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'delete')))
                        ->setAction($this->generateUrl($route, array('id' => $id)))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}
