<?php

require_once dirname(__FILE__) . '/../email.php';
require_once dirname(__FILE__) . '/EmailTracking.php';

class ClickEmailTracking extends EmailTracking
{

	public function __construct($parameterHolders = NULL , $parameterNames = array())
	{
		parent::__construct($parameterHolders, $parameterNames);
	}
    //--------------------------------------------------------------------------


	public function getRedirectUrl($token)
	{
		$decodedToken = HTML::decodeToken($token);

        if (isset($decodedToken['link'])) {
		    return $decodedToken['link'];
        } else {
            return false;
        }
	}
    //--------------------------------------------------------------------------


	public function trackingEmailClick()
	{
        $db = new Database;

        $decodedToken = HTML::decodeToken($this->parameters['token']);

        if (isset($decodedToken) && is_array($decodedToken)) {
            if (Config::COUNT_SUBSEQUENT_CLICKS === false) {
                $sql  = "SELECT COUNT(*) FROM `transactions`";
                $sql .= " WHERE `email`     = '" . mysql_real_escape_string($decodedToken['email']) . "'";
                $sql .= " AND `type`        = '" . Config::TRANSACTION_CLICK . "'";
                if (isset($decodedToken['subid']) && $decodedToken['subid'] > 0) {
                    $sql .= " AND `activity_id` = '" . $decodedToken['subid'] . "'";
                }

                $existingCount = $db->getUpperLeft($sql);
            }

            if (Config::COUNT_SUBSEQUENT_CLICKS === false && isset($existingCount) && $existingCount > 0) {
                return true;
            } else {
                $sql  = "INSERT INTO `transactions` (id, type, email, activity_id, datetime) VALUES";
                $sql .= " (NULL,";
                $sql .= " '" . Config::TRANSACTION_CLICK . "',";
                $sql .= " '" . mysql_real_escape_string($decodedToken['email']) . "',";

                if (isset($decodedToken['subid']) && $decodedToken['subid'] > 0) {
                    $sql .= " '" . $decodedToken['subid'] . "',";
                } else {
                    $sql .= " NULL,";
                }

                $sql .= " NOW())";

                $db->query($sql);

                $sql = "INSERT IGNORE INTO `clickers` (email) VALUES ('" . mysql_real_escape_string($decodedToken['email']) . "')";

                $db->query($sql);

                Lead::scoreClick($decodedToken['email']);
            }
        }
	}
    //--------------------------------------------------------------------------


	public function redirect()
	{
        $redirectUrl = $this->getRedirectUrl($this->parameters['token']);

        if (empty($redirectUrl)) {
            die();
        }

	    header("Location: " . $redirectUrl);
	    die();
	}
    //--------------------------------------------------------------------------
}

$clickEmailTracking = new ClickEmailTracking($_GET, array('token'));
$clickEmailTracking->trackingEmailClick();
$clickEmailTracking->redirect();

die();