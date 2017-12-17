<?php

/**
 * Created by IntelliJ IDEA.
 * User: davidc
 * Date: 8/18/17
 * Time: 12:00 PM
 */
class GoogleReCaptchaController extends Controller
{
    public static $allowed_actions = [
        'verify',
    ];

    public function verify() {

        $request = $this->getRequest();
        $userResponse = $request->postVar('userResponse');

        $apiEndpoint = Config::inst()->get('GoogleReCaptchaController', 'verification_endpoint');

        $service = new RestfulService($apiEndpoint, 60); //domain, cache duration
        $service->setQueryString([
            'secret'	=> $this->getRecaptchaSecretKey(),
            'response'	=> $userResponse
        ]);

        $response = $service->request('',"POST");
        $body = $response->getBody();

        return $body;
    }

    private function getRecaptchaSecretKey() {
        return Config::inst()->get('GoogleReCaptcha', 'secret_key');
    }
}