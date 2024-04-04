<?php
namespace Craft;
$path = craft()->path->getPluginsPath();

require $path .'notification/aws/aws-autoloader.php';
use Aws\Sns\Message;
use Aws\Sns\MessageValidator;
use Aws\Sns\Exception\InvalidSnsMessageException;
use Aws\Sns\SnsClient;


/**
 * NotificationService
 */
class NotificationService extends BaseApplicationComponent
{

    /**
     *  Send_message
     */
    public function sendMessage()
    {

        echo 'sendMessage';

        $params = array(
            'credentials' => array(
                'key'  =>  'AKIAJKRN5KHZK22VGFWQ',
                'secret' => 'AkBs7X0by4e4ZPhHy9oIuuQJCiWm1UcSvpC1WtO5pMH2'
            ),
            'region' => 'us-east-1',
            'version' => 'latest'
        );

        $sns = new \Aws\Sns\SnsClient($params);

        $args = array(
            "SenderID" => "Vetter",
            "SMSType"  => "Transational",
            "Message"  => "This is a test from Craft",
            "PhoneNumber"  => "+1 8565998285"
        );

        $result = $sns->publish($args);
        var_dump($result);
    }



}
