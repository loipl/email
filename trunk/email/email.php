<?php

date_default_timezone_set('UTC');

if (!defined('CURLE_OPERATION_TIMEDOUT')) {
    define('CURLE_OPERATION_TIMEDOUT', 30);
}

define('_SCRIPT_INIT_', 1);

require_once dirname(__FILE__) . '/utilities.php';

require_once dirname(__FILE__) . '/ESP.php';
require_once dirname(__FILE__) . '/ESPs/sendgrid.php';
require_once dirname(__FILE__) . '/ESPs/dynect.php';
require_once dirname(__FILE__) . '/ESPs/mailgun.php';

require_once dirname(__FILE__) . '/AdNet.php';
require_once dirname(__FILE__) . '/AdNets/adknowledge.php';
require_once dirname(__FILE__) . '/AdNets/obmedia.php';

require_once dirname(__FILE__) . '/DataAppend.php';
require_once dirname(__FILE__) . '/DataAppend/rapleaf.php';

require_once dirname(__FILE__) . '/SMTP.php';
require_once dirname(__FILE__) . '/ESPs/gsmtp.php';
require_once dirname(__FILE__) . '/ESPs/smtpcom.php';

require_once dirname(__FILE__) . '/Hygiene.php';
require_once dirname(__FILE__) . '/Hygiene/impressionwise.php';

require_once dirname(__FILE__) . '/Verification.php';
require_once dirname(__FILE__) . '/Verification/leadspend.php';

require_once dirname(__FILE__) . '/RSS.php';
require_once dirname(__FILE__) . '/RSS/Vendor_SimplePie.php';

require_once dirname(__FILE__) . '/Helpers/builder.php';
require_once dirname(__FILE__) . '/Helpers/cron.php';
require_once dirname(__FILE__) . '/Helpers/dates.php';
require_once dirname(__FILE__) . '/Helpers/html.php';
require_once dirname(__FILE__) . '/Helpers/logging.php';
require_once dirname(__FILE__) . '/Helpers/random.php';

require_once dirname(__FILE__) . '/Config.php';

if (getenv('DOM_DEV_MACHINE')) {

    Config::$database['host']     = 'localhost';
    Config::$database['database'] = 'email';
    Config::$database['username'] = 'emailuser';
    Config::$database['password'] = 'emailpass';

    Config::$installedPath   = 'http://74.178.32.249/email';
    Config::$emailTests      = 'dom@leadwrench.com';
}

if (getenv('IMRAN_DEV_MACHINE')) {
    Config::$database['host']     = 'localhost';
    Config::$database['database'] = 'email';
    Config::$database['username'] = 'root';
    Config::$database['password'] = '';

    Config::$installedPath   = 'http://localhost/Email/trunk/email';
    Config::$emailTests      = 'imran@leadwrench.com';
}

require_once dirname(__FILE__) . '/Database.php';

require_once dirname(__FILE__) . '/Models/Lead.php';
require_once dirname(__FILE__) . '/Models/Sender.php';
require_once dirname(__FILE__) . '/Models/Statistic.php';
require_once dirname(__FILE__) . '/Models/Creative.php';
require_once dirname(__FILE__) . '/Models/Campaign.php';
require_once dirname(__FILE__) . '/Models/Channel.php';
require_once dirname(__FILE__) . '/Models/Footer.php';
require_once dirname(__FILE__) . '/Models/Activity.php';
require_once dirname(__FILE__) . '/Models/Probability.php';
require_once dirname(__FILE__) . '/Models/CacheDataAppend.php';
require_once dirname(__FILE__) . '/Models/TldList.php';
require_once dirname(__FILE__) . '/Models/Transaction.php';
require_once dirname(__FILE__) . '/Models/Throttle.php';
require_once dirname(__FILE__) . '/Models/LogScheduler.php';

require_once dirname(__FILE__) . '/Models/Suppression/Email.php';
require_once dirname(__FILE__) . '/Models/Suppression/Domain.php';
require_once dirname(__FILE__) . '/Models/Suppression/Email/Md5.php';
require_once dirname(__FILE__) . '/Models/Suppression/Domain/Md5.php';

require_once dirname(__FILE__) . '/Models/Locks/Cron.php';

require_once dirname(__FILE__) . '/Models/Queue/Send.php';
require_once dirname(__FILE__) . '/Models/Queue/Build.php';
require_once dirname(__FILE__) . '/Models/Seed.php';

require_once dirname(__FILE__) . '/Engine/Sender.php';
require_once dirname(__FILE__) . '/Engine/Scheduler.php';
require_once dirname(__FILE__) . '/Engine/Scheduler/Batches.php';
require_once dirname(__FILE__) . '/Engine/Scheduler/Channels.php';
require_once dirname(__FILE__) . '/Engine/Scheduler/Creatives.php';
require_once dirname(__FILE__) . '/Engine/Scheduler/Leads.php';


function coreErrorHandler($errno, $errstr, $errfile, $errline)
{
    $db = new Database;

    $sql  = "INSERT INTO `error_log_php` (id, datetime, error_number, error_string, error_file, error_line) VALUES ";
    $sql .= " (NULL,";
    $sql .= " NOW(),";
    $sql .= " '" . mysql_real_escape_string($errno) . "',";
    $sql .= " '" . mysql_real_escape_string($errstr) . "',";
    $sql .= " '" . mysql_real_escape_string($errfile) . "',";
    $sql .= " '" . mysql_real_escape_string($errline)  . "')";

    $db->query($sql);

    return true;
}
//--------------------------------------------------------------------------

set_error_handler("coreErrorHandler");