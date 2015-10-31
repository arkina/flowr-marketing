<?php

namespace Flower\MarketingBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Flower\MarketingBundle\Form\Type\MailTemplateType;
use Flower\ModelBundle\Entity\Marketing\MailTemplate;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * MailTemplate controller.
 *
 * @Route("/mailtemplate")
 */
class MailTemplateController extends Controller
{

    /**
     * Lists all MailTemplate entities.
     *
     * @Route("/", name="mailtemplate")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $qb = $em->getRepository('FlowerModelBundle:Marketing\MailTemplate')->createQueryBuilder('m');

        $this->addQueryBuilderSort($qb, 'mailtemplate');
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);

        return array(
            'paginator' => $paginator,
        );
    }

    /**
     * Lists all MailTemplate entities.
     *
     * @Route("/{id}/editor", name="mailtemplate_editor")
     * @Method("GET")
     * @Template()
     */
    public function editorAction(MailTemplate $template)
    {

        return array(
            "mailtemplate" => $template
        );
    }

    /**
     * Lists all MailTemplate entities.
     *
     * @Route("/{id}/editor_save", name="mailtemplate_editor_save")
     * @Method("POST")
     */
    public function editorSaveAction(MailTemplate $template, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $template->setContent($request->get("content"));

        $em->flush();
        return new JsonResponse();
    }

    /**
     * Lists all MailTemplate entities.
     *
     * @Route("/{id}/editor_get", name="mailtemplate_editor_get")
     * @Method("GET")
     */
    public function editorGetAction(MailTemplate $template)
    {

        return new JsonResponse(array(
            "content" => $template->getContent()
        ));
    }

    /**
     * Finds and displays a MailTemplate entity.
     *
     * @Route("/{id}/show", name="mailtemplate_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(MailTemplate $mailtemplate)
    {
        $deleteForm = $this->createDeleteForm($mailtemplate->getId(), 'mailtemplate_delete');

        return array(
            'mailtemplate' => $mailtemplate,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new MailTemplate entity.
     *
     * @Route("/new", name="mailtemplate_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $mailtemplate = new MailTemplate();
        $form = $this->createForm(new MailTemplateType(), $mailtemplate);

        return array(
            'mailtemplate' => $mailtemplate,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new MailTemplate entity.
     *
     * @Route("/create", name="mailtemplate_create")
     * @Method("POST")
     * @Template("FlowerMarketingBundle:MailTemplate:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $mailtemplate = new MailTemplate();
        $form = $this->createForm(new MailTemplateType(), $mailtemplate);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($mailtemplate);
            $em->flush();

            return $this->redirect($this->generateUrl('mailtemplate_show', array('id' => $mailtemplate->getId())));
        }

        return array(
            'mailtemplate' => $mailtemplate,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing MailTemplate entity.
     *
     * @Route("/{id}/edit", name="mailtemplate_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(MailTemplate $mailtemplate)
    {
        $editForm = $this->createForm(new MailTemplateType(), $mailtemplate, array(
            'action' => $this->generateUrl('mailtemplate_update', array('id' => $mailtemplate->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($mailtemplate->getId(), 'mailtemplate_delete');

        return array(
            'mailtemplate' => $mailtemplate,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing MailTemplate entity.
     *
     * @Route("/{id}/update", name="mailtemplate_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerMarketingBundle:MailTemplate:edit.html.twig")
     */
    public function updateAction(MailTemplate $mailtemplate, Request $request)
    {
        $editForm = $this->createForm(new MailTemplateType(), $mailtemplate, array(
            'action' => $this->generateUrl('mailtemplate_update', array('id' => $mailtemplate->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('mailtemplate_show', array('id' => $mailtemplate->getId())));
        }
        $deleteForm = $this->createDeleteForm($mailtemplate->getId(), 'mailtemplate_delete');

        return array(
            'mailtemplate' => $mailtemplate,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="mailtemplate_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('mailtemplate', $field, $type);

        return $this->redirect($this->generateUrl('mailtemplate'));
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
     * Deletes a MailTemplate entity.
     *
     * @Route("/{id}/delete", name="mailtemplate_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(MailTemplate $mailtemplate, Request $request)
    {
        $form = $this->createDeleteForm($mailtemplate->getId(), 'mailtemplate_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($mailtemplate);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('mailtemplate'));
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
    /**
     * Clonar la entidad
     *
     * @Route("/{id}/clone", name="mailtemplate_clone")
     */
    public function cloneAction(MailTemplate $template){
        $templateCloned = clone $template;
        $templateCloned->setId(null);
        $templateCloned->setName($templateCloned->getName()."(copiado)");
        $em = $this->getDoctrine()->getManager();
        $em->persist($templateCloned);
        $em->flush();
        return $this->redirect($this->generateUrl("mailtemplate_show", array(
            'id'  => $templateCloned->getId()
                )));
    }
}
