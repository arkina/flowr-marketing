<?php

namespace Flower\MarketingBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View as FOSView;
use Symfony\Component\HttpFoundation\Request;

/**
* Description of MailgunWebhookController
*
* @author Juan Manuel Agüero <jaguero@flowcode.com.ar>
*/
class MailgunWebhookController extends FOSRestController
{
    public function trackEventAction(Request $request)
    {
        $event = $request->get("event");
        if($event){

            $em = $this->getDoctrine()->getManager();
            $campaignEmailMessageRepo = $em->getRepository("FlowerModelBundle:Marketing\CampaignEmailMessage");

            switch ($event) {
                case 'opened':
                    $email = $request->get("recipient");
                    $campaignId = $request->get("campaign-id");

                    $message = $campaignEmailMessageRepo->findOneBy(array("toemail" => $email, "campaign" => $campaignId));
                    if($message){
                        $message->setOpens($message->getOpens()+1);
                        $em->flush();
                    }
                    break;

                case 'clicked':
                    $email = $request->get("recipient");
                    $campaignId = $request->get("campaign-id");

                    $message = $campaignEmailMessageRepo->findOneBy(array("toemail" => $email, "campaign" => $campaignId));
                    if($message){
                        $message->setClicks($message->getClicks()+1);
                        $em->flush();
                    }

                    break;
                default:
                    $email = $request->get("recipient");
                    $campaignId = $request->get("campaign-id");

                    $message = $campaignEmailMessageRepo->findOneBy(array("toemail" => $email, "campaign" => $campaignId));
                    if($message){
                        $this->get("flower.contactlist")->disableCampaignMail($message->getProviderId());
                    }
                    break;
            }

        }

        $view = FOSView::create(array(), Codes::HTTP_OK)->setFormat('json');
        return $this->handleView($view);
    }


}
