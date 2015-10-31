<?php

namespace Flower\MarketingBundle\Controller;

use Ddeboer\DataImport\Reader\CsvReader;
use Doctrine\ORM\QueryBuilder;
use Flower\MarketingBundle\Form\Type\ContactListFilterType;
use Flower\MarketingBundle\Form\Type\ContactListType;
use Flower\ModelBundle\Entity\Marketing\ContactList;
use Flower\ModelBundle\Entity\Marketing\ImportProcess;
use Flower\ModelBundle\Entity\Clients\Contact;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SplFileObject;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ContactList controller.
 *
 * @Route("/contactlist")
 */
class ContactListController extends Controller
{

    /**
     * Lists all ContactList entities.
     *
     * @Route("/", name="contactlist")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new ContactListFilterType());
        if (!is_null($response = $this->saveFilter($form, 'contactlist', 'contactlist'))) {
            return $response;
        }
        $qb = $em->getRepository('FlowerModelBundle:Marketing\ContactList')->createQueryBuilder('c');

        $paginator = $this->filter($form, $qb, 'contactlist');

        return array(
            'form' => $form->createView(),
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a ContactList entity.
     *
     * @Route("/{id}/bulk_move", name="contactlist_bulk_move", requirements={"id"="\d+"})
     * @Method("POST")
     */
    public function bulkMoveToAction(ContactList $contactlist, Request $request)
    {
        $contacts = $request->get("contacts");
        $destListId = $request->get("list_dest");

        $em = $this->getDoctrine()->getManager();
        $contactListDest = $em->getRepository('FlowerModelBundle:Marketing\ContactList')->find($destListId);
        foreach ($contacts as $contactId) {
            $contact = $em->getRepository('FlowerModelBundle:Clients\Contact')->find($contactId);

            $contactlist->removeContact($contact);
            $contact->removeContactList($contactlist);

            $contact->addContactList($contactListDest);
            $contactListDest->addContact($contact);
        }
        $em->flush();


        return new JsonResponse(null, 200);
    }

    /**
     * Finds and displays a ContactList entity.
     *
     * @Route("/{id}/bulk_copy", name="contactlist_bulk_copy", requirements={"id"="\d+"})
     * @Method("POST")
     */
    public function bulkCopyToAction(ContactList $contactlist, Request $request)
    {
        $contacts = $request->get("contacts");
        $destListId = $request->get("list_dest");

        $em = $this->getDoctrine()->getManager();
        $contactListDest = $em->getRepository('FlowerModelBundle:Marketing\ContactList')->find($destListId);
        foreach ($contacts as $contactId) {
            $contact = $em->getRepository('FlowerModelBundle:Clients\Contact')->find($contactId);

            $contact->addContactList($contactListDest);
            $contactListDest->addContact($contact);

            $em->flush();
        }



        return new JsonResponse(null, 200);
    }

    /**
     * Finds and displays a ContactList entity.
     *
     * @Route("/{id}/show", name="contactlist_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(ContactList $contactlist, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $contacts = $em->getRepository('FlowerModelBundle:Clients\Contact')->getByContactList($contactlist->getId(), (($request->query->get('page', 1)-1)*50), 50);
        $availableLists = $em->getRepository('FlowerModelBundle:Marketing\ContactList')->findAll();

        return array(
            'contactlist' => $contactlist,
            'availablelists' => $availableLists,
            'contacts' => $contacts
        );
    }

    /**
     * Finds and displays a ContactList entity.
     *
     * @Route("/{id}/imports", name="contactlist_imports")
     * @Method("GET")
     * @Template()
     */
    public function importsAction(ContactList $contactList)
    {
        $em = $this->getDoctrine()->getManager();
        $proceses = $em->getRepository("FlowerModelBundle:Marketing\ImportProcess")->findByContactList($contactList);

        return array(
            'contactlist' => $contactList,
            'proceses' => $proceses
        );
    }

    /**
     * Finds and displays a ContactList entity.
     *
     * @Route("/{id}/import/launch", name="contactlist_import_launch")
     * @Method("POST")
     */
    public function launchImportAction(ContactList $contactList, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $filename = $request->get("filename");

        //$tempDir = $this->get('kernel')->getRootDir() . "/../web/uploads/tmp/";

        $colDef = $request->get("col_def");

        $importProcess = new ImportProcess();
        $importProcess->setFileName($filename);
        $importProcess->setStatus(ImportProcess::STATUS_IN_PROGRESS);
        $importProcess->setColdef(json_encode($colDef, true));
        $importProcess->setContactList($contactList);

        $em->persist($importProcess);

        $em->flush();

        /* launch process */
        $rootDir = $this->get('kernel')->getRootDir();
        $env = $this->container->get('kernel')->getEnvironment();
        $commandCall = "php " . $rootDir . "/console flower:import --env=" . $env . "  " . $importProcess->getId() . " > /dev/null &";
        exec($commandCall);

        return new JsonResponse(array(
            "result" => "ok"
        ));
    }

    /**
     * Finds and displays a ContactList entity.
     *
     * @Route("/{id}/import", name="contactlist_import", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function importAction(ContactList $contactlist)
    {

        return array(
            'contactlist' => $contactlist
        );
    }

    /**
     * Finds and displays a ContactList entity.
     *
     * @Route("/upload", name="contactlist_import_upload")
     * @Method("POST")
     */
    public function uploadAction(Request $request)
    {
        $uploadedFile = $request->files->get('file');

        $fileCode = md5($uploadedFile->getFilename() . time());

        $tempDir = $this->get('kernel')->getRootDir() . "/../web/uploads/tmp/";
        $filename = $fileCode . ".csv";
        $uploadedFile->move($tempDir, $filename);



        $file = new SplFileObject($tempDir . $filename);
        $reader = new CsvReader($file);

        foreach ($reader as $row) {
            $fileFirstRow = $row;
            break;
        }

        return new JsonResponse(array(
            "filename" => $filename,
            "first_row" => $fileFirstRow,
        ));
    }

    /**
     * Displays a form to create a new ContactList entity.
     *
     * @Route("/new", name="contactlist_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $contactlist = new ContactList();
        $form = $this->createForm(new ContactListType(), $contactlist);

        return array(
            'contactlist' => $contactlist,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new ContactList entity.
     *
     * @Route("/create", name="contactlist_create")
     * @Method("POST")
     * @Template("FlowerMarketingBundle:ContactList:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $contactlist = new ContactList();
        $form = $this->createForm(new ContactListType(), $contactlist);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($contactlist);
            $em->flush();

            return $this->redirect($this->generateUrl('contactlist_show', array('id' => $contactlist->getId())));
        }

        return array(
            'contactlist' => $contactlist,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing ContactList entity.
     *
     * @Route("/{id}/edit", name="contactlist_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(ContactList $contactlist)
    {
        $editForm = $this->createForm(new ContactListType(), $contactlist, array(
            'action' => $this->generateUrl('contactlist_update', array('id' => $contactlist->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($contactlist->getId(), 'contactlist_delete');

        return array(
            'contactlist' => $contactlist,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing ContactList entity.
     *
     * @Route("/{id}/update", name="contactlist_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerMarketingBundle:ContactList:edit.html.twig")
     */
    public function updateAction(ContactList $contactlist, Request $request)
    {
        $editForm = $this->createForm(new ContactListType(), $contactlist, array(
            'action' => $this->generateUrl('contactlist_update', array('id' => $contactlist->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('contactlist_show', array('id' => $contactlist->getId())));
        }
        $deleteForm = $this->createDeleteForm($contactlist->getId(), 'contactlist_delete');

        return array(
            'contactlist' => $contactlist,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="contactlist_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('contactlist', $field, $type);

        return $this->redirect($this->generateUrl('contactlist'));
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
     * Save filters
     *
     * @param  FormInterface $form
     * @param  string        $name   route/entity name
     * @param  string        $route  route name, if different from entity name
     * @param  array         $params possible route parameters
     * @return Response
     */
    protected function saveFilter(FormInterface $form, $name, $route = null, array $params = null)
    {
        $request = $this->getRequest();
        $url = $this->generateUrl($route ? : $name, is_null($params) ? array() : $params);
        if ($request->query->has('submit-filter') && $form->handleRequest($request)->isValid()) {
            $request->getSession()->set('filter.' . $name, $request->query->get($form->getName()));

            return $this->redirect($url);
        } elseif ($request->query->has('reset-filter')) {
            $request->getSession()->set('filter.' . $name, null);

            return $this->redirect($url);
        }
    }

    /**
     * Filter form
     *
     * @param  FormInterface                                       $form
     * @param  QueryBuilder                                        $qb
     * @param  string                                              $name
     * @return PaginationInterface
     */
    protected function filter(FormInterface $form, QueryBuilder $qb, $name)
    {
        if (!is_null($values = $this->getFilter($name))) {
            if ($form->submit($values)->isValid()) {
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($form, $qb);
            }
        }

        // possible sorting
        $this->addQueryBuilderSort($qb, $name);
        return $this->get('knp_paginator')->paginate($qb, $this->getRequest()->query->get('page', 1), 20);
    }

    /**
     * Get filters from session
     *
     * @param  string $name
     * @return array
     */
    protected function getFilter($name)
    {
        return $this->getRequest()->getSession()->get('filter.' . $name);
    }

    /**
     * Deletes a ContactList entity.
     *
     * @Route("/{id}/delete", name="contactlist_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(ContactList $contactlist, Request $request)
    {
        $form = $this->createDeleteForm($contactlist->getId(), 'contactlist_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($contactlist);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('contactlist'));
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
     * Displays a form to create a new Contact entity.
     *
     * @Route("/{id}/contact_new", name="contactlist_contact_new")
     * @Method("GET")
     * @Template("FlowerMarketingBundle:Contact:new.html.twig")
     */
    public function newContactAction(ContactList $contactList)
    {
        $contact = new Contact();
        $form = $this->createForm($this->get('form.type.contact'), $contact);

        return array(
            'contact' => $contact,
            'contactList' => $contactList,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Contact entity.
     *
     * @Route("/{id}/contact_create", name="contactlist_contact_create")
     * @Method("POST")
     * @Template("FlowerMarketingBundle:Contact:new.html.twig")
     */
    public function createContactteAction(Request $request, ContactList $contactList)
    {
        $contact = new Contact();
        $contactList->addContact($contact);
        $form = $this->createForm($this->get('form.type.contact'), $contact);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();


            $em->persist($contact);
            $em->flush();

            return $this->redirect($this->generateUrl('contactlist_show', array('id' => $contactList->getId())));
        }

        return array(
            'contact' => $contact,
            'contactList' => $contactList,
            'form' => $form->createView(),
        );
    }

    /**
     * Unsuscribe confirmation.
     *
     * @Route("/unsuscribe/{id}", name="contactlist_unsuscribe_confirm")
     * @Method("GET")
     * @Template()
     */
    public function unsuscribeConfirmAction(Request $request, Contact $contact){
        $em = $this->getDoctrine()->getManager();
        $contactLists = $em->getRepository("FlowerModelBundle:Marketing\ContactList")->getByContactId($contact->getId());
        return array(
            'contact' => $contact,
            'contactLists' => $contactLists,
        );
    }

    /**
     * Unsuscribe confirmation.
     *
     * @Route("/unsuscribe/{id}", name="contactlist_unsuscribe")
     * @Method("POST")
     * @Template("")
     */
    public function unsuscribeAction(Request $request, Contact $contact){
        $contactLists = $request->get('contactlists');
        $em = $this->getDoctrine()->getManager();
        if($contactLists){
            foreach ($contactLists as $contactListId) {
                $em->getRepository("FlowerModelBundle:Marketing\ContactList")->removeContact($contactListId, $contact->getId());
            }
            $this->addFlash('success', 'unsuscribed_succesfully');
        }else{
            $this->addFlash('warning', 'unsuscribed_failed');
        }
        return array(
            'contact' => $contact,
        );
    }

}
