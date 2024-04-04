<?php
namespace Craft;

/**
 * NotificationController
 */
class NotificationController extends BaseController {


    public function actionSendMessage(){
        craft()->notification->sendMessage();
    }


}