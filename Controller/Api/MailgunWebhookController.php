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
        $this->get("logger")->info("mailgun webhook >>>>> ".json_encode($request->request->all(), true));

        $event = $request->get("event");
        $email = $request->get("recipient");
        $messageId = "<".$request->get("message-id").">";

        if($event){

            $em = $this->getDoctrine()->getManager();
            $campaignEmailMessageRepo = $em->getRepository("FlowerModelBundle:Marketing\CampaignEmailMessage");
            $message = $campaignEmailMessageRepo->findOneBy(array("providerId" => $messageId));

            switch ($event) {
                case 'opened':
                    if($message){
                        $message->setOpens($message->getOpens()+1);
                        $em->flush();
                    }
                    break;

                case 'clicked':
                    if($message){
                        $message->setClicks($message->getClicks()+1);
                        $em->flush();
                    }

                    break;
                default:
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
