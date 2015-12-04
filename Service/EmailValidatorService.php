<?php

namespace Flower\MarketingBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Flower\MarketingBundle\Model\EmailGrade;
use Mailgun\Mailgun;

/**
 * EmailValidatorService.
 */
class EmailValidatorService
{

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Validate single email.

     * @param  string  $email The email to be validated.
     * @return integer        return the email grade.
     */
    public function validate($email)
    {
        $valid = false;
        $emailGrade = EmailGrade::grade_a;
        $mailgunApiKey = $this->container->getParameter('mailgun_api_key');

        # Instantiate the client.
        $mgClient = new Mailgun("pubkey-5ogiflzbnjrljiky49qxsiozqef5jxp7");
        $validateAddress = $email;

        # Issue the call to the client.
        $result = $mgClient->get("address/validate", array('address' => $validateAddress));
        if(!$result->http_response_body->is_valid){
            $emailGrade = EmailGrade::grade_d;
        }

        return $emailGrade;
    }
}
