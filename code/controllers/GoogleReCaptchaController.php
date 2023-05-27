<?php

namespace ContextualSoftware\GoogleRecaptcha\Controllers;

use SilverStripe\Core\Config\Config;
use SilverStripe\Control\Controller;
use GuzzleHttp;

class GoogleReCaptchaController extends Controller
{
    private static $allowed_actions = [
        'verify',
    ];

    public function verify() {

        $request = $this->getRequest();
        $userResponse = $request->postVar('userResponse');

        $apiEndpoint = Config::inst()->get(GoogleReCaptchaController::class, 'verification_endpoint');

        $client = new GuzzleHttp\Client();
        $res = $client->request('GET', $apiEndpoint, [
            'query' => [
                'secret' => $this->getRecaptchaSecretKey(),
                'response' => $userResponse,
            ]
        ]);

        return $res->getBody()->getContents();
    }

    private function getRecaptchaSecretKey() {
        return Config::inst()->get('GoogleReCaptcha', 'secret_key');
    }
}
