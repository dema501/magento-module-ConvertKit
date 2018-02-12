<?php
/**
 *
 * @category   Mage
 * @package    Liftmode_ConvertKit
 * @copyright  Copyright (c)  Dmitry Bashlov, contributors.
 */

class Liftmode_ConvertKit_Model_Observer {
    const RC_MODULE_ENABLE    = 'newsletter/convertkit/enable';
    const RC_API_KEY          = 'newsletter/convertkit/api_key';
    const RC_API_SECRET       = 'newsletter/convertkit/api_secret';
    const RC_FORM_ID          = 'newsletter/convertkit/form_id';

    public function notifyConvertKitAboutNewSubscriber(Varien_Event_Observer $observer) {
        if (!Mage::getStoreConfig(self::RC_MODULE_ENABLE, Mage::app()->getStore())) {
            return $this;
        }

        $_subscriber = $observer->getEvent()->getDataObject();
        $_data = $_subscriber->getData();
        $_statusChange = $_subscriber->getIsStatusChanged();


        // Trigger if user is now subscribed and there has been a status change:
        if ($_data['subscriber_status'] === Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED && $_statusChange === true) {
            // curl -X POST https://api.convertkit.com/v3/forms/1/subscribe\
            // -H "Content-Type: application/json; charset=utf-8"\
            //     -d '{ "api_secret": "<your_secret_api_key>",\
            //           "email": "jonsnow@example.com", "first_name": "John" }'


            $rc = Mage::getModel('convertkit/convertKit');

            $params = array(
                'email'        => $_data['subscriber_email'],
                'first_name'   => $_data['subscriber_firstname'],
                'api_key'      => Mage::getStoreConfig(self::RC_API_KEY, Mage::app()->getStore()),
            );

            $return = $rc->doRequest(sprintf('forms/%s/subscribe', Mage::getStoreConfig(self::RC_FORM_ID, Mage::app()->getStore())), 'post',  $params);

            Mage::log(array($params, $return), null, 'ConvertKit.log');
        }
        elseif ($_data['subscriber_status'] === Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED) {
            // curl -x PUT https://api.convertkit.com/v3/unsubscribe
            // -H 'Content-Type: application/json'\
            //     -d '{ "api_secret": "<your_secret_api_key>",\
            //           "email": "jonsnow@example.com" }'

            $rc = Mage::getModel('convertkit/convertKit');

            $params = array(
                'email'        => $_data['subscriber_email'],
                'api_secret'      => Mage::getStoreConfig(self::RC_API_SECRET, Mage::app()->getStore()),
            );

            $return = $rc->doRequest('unsubscribe', 'put',  $params);
            Mage::log(array($params, $return), null, 'ConvertKit.log');
        }

        return $this;
    }
}
