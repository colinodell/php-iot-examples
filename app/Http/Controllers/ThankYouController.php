<?php

namespace App\Http\Controllers;

use Develpr\AlexaApp\Response\AlexaResponse;
use Develpr\AlexaApp\Response\Card;
use Develpr\AlexaApp\Response\SSML;

class ThankYouController extends Controller
{
    public function sayThanks()
    {
        $finalWords = <<<EOT
<speak>
Thanks for attending my talk!
You can provide feedback, get the slides, and download the source code online by visiting joined <break strength="weak"/> dot in <break strength="weak"/> slash  one <break strength="weak"/> eight <break strength="weak"/> six <break strength="weak"/> seven <break strength="weak"/> six.
</speak>
EOT;
        $speech = (new SSML())->setValue($finalWords);
        $card = (new Card())
            ->setTitle('php[world] Feedback')
            ->setContent('Thanks for attending my talk! You can provide feedback, get the slides, and download the source code online at https://joind.in/18676');

        $response = new AlexaResponse($speech, $card);
        $response->endSession();

        return $response;
    }
}
