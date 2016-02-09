<?php

namespace Flower\MarketingBundle\Controller\Api;

use Flower\ModelBundle\Entity\Marketing\ContactList;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View as FOSView;
use Symfony\Component\HttpFoundation\Request;

/**
 * ContactList controller.
 */
class ContactListController extends FOSRestController
{
    /**
     * @param Request $request
     * @param ContactList $contactList
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getByContactListAction(Request $request, ContactList $contactList)
    {
        $em = $this->getDoctrine()->getManager();

        $contacts = $em->getRepository('FlowerModelBundle:Clients\Contact')->getByContactList($contactList->getId(), (($request->get('page', 1)-1)*50), 50, $request->get("search"));

        $view = FOSView::create($contacts, Codes::HTTP_OK)->setFormat('json');
        $view->getSerializationContext()->setGroups(array('private_api'));
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAvailablesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $contactLists = $em->getRepository('FlowerModelBundle:Marketing\ContactList')->findBy(array("enabled" => true), array("updated" => "DESC"), 20);

        $view = FOSView::create($contactLists, Codes::HTTP_OK)->setFormat('json');
        $view->getSerializationContext()->setGroups(array('private_api'));
        return $this->handleView($view);
    }

}
