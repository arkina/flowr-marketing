<?php

namespace Flower\MarketingBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View as FOSView;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of MandrillWebhookController
 *
 * @author Juan Manuel Agüero <jaguero@flowcode.com.ar>
 */
class MandrillWebhookController extends FOSRestController
{

    public function disableCampaignsAction(Request $request)
    {
        $events = $request->get("mandrill_events");
        $this->get("logger")->info("WebHook: \n".json_encode($events));
        foreach ($events as $event) {

            /* every dangerous event */
            if ($event["event"] == "spam" || $event["event"] == "unsub" || $event["event"] == "reject" || $event["event"] == "hard_bounce" || $event["event"] == "soft_bounce") {

                /* the unique identifier assigned to each email sent via Mandrill */
                $providerId = $event["_id"];

                $this->get("flower.contactlist")->disableCampaignMail($providerId);
            }
        }

        $view = FOSView::create(array(), Codes::HTTP_OK)->setFormat('json');
        return $this->handleView($view);
    }

}
