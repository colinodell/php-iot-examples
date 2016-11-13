<?php

namespace App\Http\Controllers;

use Develpr\AlexaApp\Response\AlexaResponse;
use Develpr\AlexaApp\Response\Card;
use Develpr\AlexaApp\Response\SSML;

class HelpController extends Controller
{
    public function getHelp()
    {
        $helpText = <<<EOT
<speak>
You can give commands and ask questions like <break strength="weak"/>
Ask PHP World which talks are next?
Or <break strength="weak"/>
Ask PHP World what session is in Fairfax at eleven thirty?
<break strength="weak"/>
Go ahead, try it out!
</speak>
EOT;
        $speech = (new SSML())->setValue($helpText);
        $card = (new Card())
            ->setTitle('php[world] Skill Help')
            ->setContent('Sample commands: "Ask php[world] which talks are next?" or "Ask php[world] what session is in Fairfax at eleven thirty?"');

        $response = new AlexaResponse($speech, $card);
        $response->endSession();

        return $response;
    }
}
