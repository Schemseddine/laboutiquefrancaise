<?php

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;

class Mail {

    private $api_key ='9a0949563d581d46ddbd7a43ca4bfb3f';
    private $api_key_secrete = '287889412db8d198c8dfabd69d1dbb37';

    public function send($to_email, $to_name, $subject, $content)
    {
        $mj = new Client($this->api_key, $this->api_key_secrete, true,['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "schemseddinelarbi@orange.fr",
                        'Name' => "levidence"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 3186338,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content,
                        
                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success() ;

    }
}
