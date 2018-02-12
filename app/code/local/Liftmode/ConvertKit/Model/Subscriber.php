<?php
/**
 *
 * @category   Mage
 * @package    Liftmode_ConvertKit
 * @copyright  Copyright (c)  Dmitry Bashlov, contributors.
 */

class Liftmode_ConvertKit_Model_Subscriber extends Mage_Newsletter_Model_Subscriber
{
    public function sendConfirmationRequestEmail() {
        return $this;
    }

    public function sendConfirmationSuccessEmail() {
        return $this;
    }

    public function sendUnsubscriptionEmail() {
        return $this;
    }
}