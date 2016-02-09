<?php

namespace Flower\MarketingBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Flower\MarketingBundle\Form\Type\CampaignMailType;
use Flower\MarketingBundle\Model\ContactListStatus;
use Flower\ModelBundle\Entity\Board\History;
use Flower\ModelBundle\Entity\Marketing\CampaignMail;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * CampaignMail reports.
 *
 * @Route("/campaignmail/report")
 */
class CampaignMailReportController extends Controller
{

    /**
     * Lists all CampaignMail entities.
     *
     * @Route("/", name="campaignmail_report")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {

        return array();
    }

    /**
     * Get all.
     *
     * @Route("/api/getall", name="campaignmail_report_getall")
     * @Method("GET")
     */
    public function getAllAction(Request $request)
    {
        $search = $request->get('search');
        $campaigns = $this->get('flower.marketing.service.campaignmail')->getAllPaged(
            array("name" => $search['value']),
            array(),
            $request->get('length'),
            $request->get('start'));

        $recordsTotal = $this->get('flower.marketing.service.campaignmail')->getCountAll();
        $recordsFiltered = $this->get('flower.marketing.service.campaignmail')->getCountAll();


        /* prepare data for datatables */
        $campaignsArr = array();
        foreach ($campaigns as $cam) {
            $camUrl = $this->generateUrl('campaignmail_report_campaign', array('id' => $cam->getId()));
            $camArr = array(
                "<a href='" . $camUrl . "'>" . $cam->getName() . "</a>",
                $cam->getLaunched()->format('Y-m-d H:i'),
            );
            $campaignsArr[] = $camArr;
        }

        return new JsonResponse(array(
            "draw" => $request->get("draw"),
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => $campaignsArr
        ), 200);
    }


    /**
     * Finds and displays a CampaignMail entity.
     *
     * @Route("/campaign/{id}", name="campaignmail_report_campaign", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showMessagesAction(CampaignMail $campaignmail)
    {
        $em = $this->getDoctrine()->getManager();
        $messages = $em->getRepository('FlowerModelBundle:Marketing\CampaignEmailMessage')->getByCampaignPaged($campaignmail->getId(), 0, 20);

        return array(
            'campaign' => $campaignmail,
            'messages' => $messages,
        );
    }

    /**
     * Get all.
     *
     * @Route("/api/campaign/{id}/messages", name="campaignmail_report_campaign_messages")
     * @Method("GET")
     */
    public function getMessagesAction(Request $request, CampaignMail $campaignmail)
    {
        $search = $request->get('search');

        $em = $this->getDoctrine()->getManager();
        $campaigns = $em->getRepository('FlowerModelBundle:Marketing\CampaignEmailMessage')->getByCampaignFilteredPaged(
            $campaignmail->getId(),
            array("name" => $search['value']),
            $request->get('start'), $request->get('length')
        );

        $recordsTotal = $em->getRepository('FlowerModelBundle:Marketing\CampaignEmailMessage')->getCountByCampaign($campaignmail->getId());
        $recordsFiltered = $em->getRepository('FlowerModelBundle:Marketing\CampaignEmailMessage')->getCountByCampaign($campaignmail->getId());

        $campaignsArr = array();
        foreach ($campaigns as $message) {

            $camArr = array(
                $message->getToemail(),
                $message->getOpens(),
                $message->getClicks(),
                $message->getState(),
            );
            $campaignsArr[] = $camArr;
        }

        return new JsonResponse(array(
            "draw" => $request->get("draw"),
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => $campaignsArr
        ), 200);
    }

    /**
     * Get all.
     *
     * @Route("/api/stats", name="campaignmail_report_stats")
     * @Method("GET")
     */
    public function getStatsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campaigns = $em->getRepository('FlowerModelBundle:Marketing\CampaignMail')->findBy(array(), array("launched" => "DESC"), 5, 0);

        $months = array(
            array('label' => "Dic 2015", "from" => "2015-12-01", "to" => "2015-12-31"),
            array('label' => "Ene 2016", "from" => "2016-01-01", "to" => "2016-01-31"),
            array('label' => "Feb 2016", "from" => "2016-02-01", "to" => "2016-02-28"),
        );

        $dataset = array();
        foreach ($campaigns as $campaign) {

            $datasetEntry = array(
                "label" => $campaign->getName(),
                "strokeColor" => $this->getRandomColor(),
            );
            foreach ($months as $month) {

                $data = $this->get('flower.marketing.service.campaignmail')->getStatsCountsByCampaign($campaign, $month['from'], $month['to']);
                $datasetEntry['data'][] = !is_null($data) ? $data : 0;
            }

            $dataset[] = $datasetEntry;
        }

        return new JsonResponse(array(
            "labels" => array("Dic 2015", "Ene 2016", "Feb 2016"),
            "datasets" => $dataset,
        ), 200);
    }

    /**
     * @return string
     */
    private function getRandomColor()
    {
        //$colorPalette = array('#4b9632', '#28b4dc', '#f00078', '#77bf43', '#dc6e28', '#ffbe00');
        //$color = $colorPalette[rand(0, count($colorPalette) - 1)];
        //return $color;

        return '#' . strtoupper(dechex(rand(0,10000000)));
    }


}
