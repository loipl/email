<?php

require_once dirname(__FILE__) . '/../email.php';
require_once dirname(__FILE__) . '/EmailTracking.php';

class OpenEmailTracking extends EmailTracking
{
	
	private $image;

	public function __construct($parameterHolders = NULL , $parameterNames = array() , $image = NULL)
	{
		parent::__construct($parameterHolders, $parameterNames);
		$this->image = $image;
	}
    //--------------------------------------------------------------------------


	public function trackEmailOpen()
	{
        $db = new Database;

        $decodedToken = HTML::decodeToken($this->parameters['token']);

        if (isset($decodedToken) && is_array($decodedToken)) {
            if (Config::COUNT_SUBSEQUENT_OPENS === false) {
                $sql  = "SELECT COUNT(*) FROM `transactions`";
                $sql .= " WHERE `email`     = '" . mysql_real_escape_string($decodedToken['email']) . "'";
                $sql .= " AND `type`        = '" . Config::TRANSACTION_OPEN . "'";

                if (isset($decodedToken['subid']) && $decodedToken['subid'] > 0) {
                    $sql .= " AND `activity_id` = '" . $decodedToken['subid'] . "'";
                }

                $existingCount = $db->getUpperLeft($sql);
            }

            if (Config::COUNT_SUBSEQUENT_OPENS === false && isset($existingCount) && $existingCount > 0) {
                return true;
            } else {

                $sql  = "INSERT INTO `transactions` (id, type, email, activity_id, datetime) VALUES";
                $sql .= " (NULL,";
                $sql .= " '" . Config::TRANSACTION_OPEN. "',";
                $sql .= " '" . mysql_real_escape_string($decodedToken['email']) . "',";

                if (isset($decodedToken['subid']) && $decodedToken['subid'] > 0) {
                    $sql .= " '" . $decodedToken['subid'] . "',";
                } else {
                    $sql .= " NULL,";
                }

                $sql .= " NOW())";

                $db->query($sql);

                $sql = "INSERT IGNORE INTO `openers` (email) VALUES ('" . mysql_real_escape_string($decodedToken['email']) . "')";

                $db->query($sql);

                Lead::scoreOpen($decodedToken['email']);
            }
        }
	}
    //--------------------------------------------------------------------------


	public function outputBlankImage()
	{
		if (!empty($this->image) && is_file($this->image)) {
			readfile($this->image);
		}
	}
    //--------------------------------------------------------------------------
}

$openEmailTracking = new OpenEmailTracking($_GET, array('token'), '1x1EmailTrackingImage.png');

header("Content-Type: image/png");
$openEmailTracking->outputBlankImage();
$openEmailTracking->trackEmailOpen();

die();