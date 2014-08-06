<?php

class Config
{

    const INDUSTRY_FINANCIAL      =     1;
    const INDUSTRY_DATING         =     2;

    const TRANSACTION_OPEN        =     1;
    const TRANSACTION_CLICK       =     2;
    const TRANSACTION_UNSUBSCRIBE =     3;

    const CREATIVE_ADKI           =     1;
    const CREATIVE_OBMEDIA        =     2;

    const SCOREMOD_NEW            =    30;
    const SCOREMOD_SEND           =    -5;
    const SCOREMOD_OPEN           =    20;
    const SCOREMOD_CLICK          =    40;
    const SCOREMOD_COMPLAINT      =  -999;
    const SCOREMOD_SOFTBOUNCE     =    -5;
    const SCOREMOD_HARDBOUNCE     =  -999;
    const SCOREMOD_UNSUBSCRIBE    =  -999;
    const SCOREMOD_HYGIENEFAIL    =  -999;
    
    const TRANSACTION_TYPE_OPEN         =  1;   // open
    const TRANSACTION_TYPE_CLICK        =  2;   // click
    const TRANSACTION_TYPE_UNSUB        =  3;   // unsubcribe
    const TRANSACTION_TYPE_SOFTBOUNCE   =  4;   // soft bounce
    const TRANSACTION_TYPE_COMPLAINT    =  5;   // complaint
    const TRANSACTION_TYPE_HARDBOUNCE   =  6;   // hardbounce
    const TRANSACTION_TYPE_CONVERSION   = 10;   // conversion
    
    const SUPRESS_REASON_HARDBOUNCE     = 1;    // hard bounce
    const SUPRESS_REASON_PREVHARDBOUNCE = 2;    // previously hard bounce
    const SUPRESS_REASON_COMPLAINT      = 3;    // complaint
    const SUPRESS_REASON_PREVCOMPLAINT  = 4;    // previously complaint
    const SUPRESS_REASON_IMPORTED       = 5;    // imported data
    const SUPRESS_REASON_UNSUB          = 6;    // unsubcribe
    const SUPRESS_REASON_HYGIENEFAIL    = 7;    // hygiene fail
    
    const SUPPRESSION_SOURCE            = 3;
    
    const COMPLAINT_DELAY_SECONDS       = 720;
    const HARD_BOUNCE_DELAY_SECONDS     = 60;
    const SOFT_BOUNCE_DELAY_SECONDS     = 10;
    const THRESHOLD_DELAY_SECONDS       = 1200;   // ignore leads which delay time > 20 minutes
    
    const PURGE_THROTTLE_THRESHOLD      = 60;    // purge throttle which older than this threshold (in minutes)
    
    const AOL_TLD_LIST                  = 'aol';
    const MICROSOFT_TLD_LIST            = 'microsoft';
    const GMAIL_TLD_LIST                = 'gmail';
    const UNITED_ONLINE_TLD_LIST        = 'united_online';
    const CABLE_TLD_LIST                = 'cable';
    const YAHOO_TLD_LIST                = 'yahoo';
    
    const AOL_TLD_LIST_DELAY_SECONDS            = 1;
    const MICROSOFT_TLD_LIST_DELAY_SECONDS      = 2;
    const GMAIL_TLD_LIST_DELAY_SECONDS          = 3;
    const UNITED_ONLINE_TLD_LIST_DELAY_SECONDS  = 4;
    const CABLE_TLD_LIST_DELAY_SECONDS          = 5;
    const YAHOO_TLD_LIST_DELAY_SECONDS          = 6;
    
    const SECRET_CODE             = 'Email-will-become-superstar';
    
    const SEPARATOR_EMAIL         = 'dRm415';
    const SEPARATOR_CAMPAIGN      = 'bMm207';
    const SEPARATOR_OFFER         = 'hRp113';
    const SEPARATOR_LINK          = 'dRp511';
    const SEPARATOR_SUBID         = 'fLp293';
    
    const DEFAULT_SENDER          =     1;
    const DEFAULT_CHANNEL         =     1;

    const MAX_BATCH_SIZE          =   400;
    const MAX_CRON_RETRIES        =     5;

    const CRON_TIMEOUT            =  1200;
    const LEAD_TIMEOUT            =  3600;
    const CAMPAIGN_TIMEOUT        =  3600;

    const INTERVAL_VERIFIED       =    30;
    const INTERVAL_OPENER         =     5;
    const INTERVAL_CLICKER        =     1;

    const COUNT_SUBSEQUENT_CLICKS = false;
    const COUNT_SUBSEQUENT_OPENS  = false;

    public static $apiKey         = '7CmCznYgpQgpOrV5PKf3RSbM98UTlZ';

    public static $installedPath   = 'http://ec2-54-214-45-138.us-west-2.compute.amazonaws.com/email';
    public static $unsubscribeUrl  = null;
    public static $unsubscribeText = 'You have been unsubscribed';

    public static $emailTests      = "dom@leadwrench.com";

    public static $debugLevel      = 0;

    public static $subdomains      = array(
        'images' => 'i',
        'clicks' => 'c'
    );

    public static $defaultCountryList = array(
        'US'
    );

    public static $fromDomains = array(
        array(
              'sender' => 'jason',
              'domain' => 'matchquota.com'
             )
    );

    public static $tierDays = array(
        'tier1' => '30',
        'tier2' => '7',
        'tier3' => '1'
    );

    public static $espCredentials = array(
        'sendgrid' => array(
            'username' => 'leadwrench',
            'password' => false,
            'apikey'   => 'souther'
                           ),

        'dynect'   => array(
            'username' => false,
            'password' => false,
            'apikey'   => '9521fce7c379a791a451d42e384591db'
                           ),
        
        'smtpcom'  => array(
            'username' => 'leadwrenchtest',
            'password' => 'leadwrenchtest',
            'host'     => 'smtp.com',
            'port'     => '2525',
            'apikey'   => 'abde5e1060ff70398b64b0ee15d162a1c7d80271'
                           ),
        'mailgun'   => array(
            'username' => false,
            'password' => false,
            'apikey'   => 'key-5jee36xnpfb2f0xtgjw9y426vod5v7e3',
            'domain' => 'https://api.mailgun.net/v2/datequota.com/'
                            )
    );

    public static $adNetCredentials = array(
        'adki' => array(
            'username'   => 'username22',
            'password'   => 'password22',
            'apikey'     => 'apikey22',
            'sendDomain' => 'matchquota.com',
            'token'      => '34690a5c1ae7f4e7f888887894106c5d'
                       ),
        'obmedia' => array(
            'username'   => 'username33',
            'password'   => 'password33',
            'apikey'     => 'apikey33',
            'sendDomain' => 'matchquota.com',
            'token'      => 'ac64aacc-8624-4895-a50d-eae0709c174a'
                       )
    );

    public static $dataAppendCredentials = array(
        'rapleaf' => array(
            'username' => false,
            'password' => false,
            'apikey'   => '83ea2d2fdf35e1c8cbbc78e056f50798'
                          )
    );

    public static $hygieneCredentials = array(
        'impressionwise' => array(
            'username' => '853001',
            'password' => 'LdWre',
            'apikey'   => false
                                 )
    );

    public static $verificationCredentials = array(
        'leadspend' => array(
            'username' => false,
            'password' => false,
            'apikey'   => 'MQVzOtsf3tUqRhgBkVUbyuCqtabKTUa59omoa6wcBhT'
                            )
    );

    public static $smtp = array(
        'host'     => "smtp.gmail.com",
        'user'     => "nsp.submit@gmail.com",
        'password' => "k42SLhhC",
        'port'     => 465,
        'timeout'  => 10,
        'newline'  => "\r\n",
        'crypto'   => 'ssl',
        'myHost'   => 'localhost'
    );

    public static $database = array(
        'host'     => 'localhost',
        'database' => 'email',
        'username' => 'root',
        'password' => ''
    );
    
    public static $testDatabase = array(
        'host'     => 'localhost',
        'database' => 'emailtest',
        'username' => 'root',
        'password' => ''
    );

    public static $validMetrics = array(
        1 => 'campaign',
        2 => 'creative',
        3 => 'sender',
        4 => 'channel',
        5 => 'recipientdomain',
        6 => 'listid',
        7 => 'category'
    );

    public static $validTypes = array(
        0 => 'total',
        1 => 'open',
        2 => 'click',
        3 => 'unsubscribe',
        4 => 'softbounce',
        5 => 'complaint',
        6 => 'hardbounce'
    );

    public static $validIntervals = array(
        'year',
        'month',
        'day',
        'hour',
        'minute'
    );
    // Setting the timezone similar to what it is on the DB server
    // mysql> show variables like 'time_zone';
    // +---------------+-------+
    // | Variable_name | Value |
    // +---------------+-------+
    // | time_zone     | UTC   |
    // +---------------+-------+
    public static $db_timezone = 'UTC';

    // Where to send email about failures to
    public static $error_email_from = 'stats_aggregator@leadwrench';
    public static $error_email_to = 'ovais.tariq@percona.com';

    // logging and miscellaneous
    public static $log_file = "/var/log/stats_aggregator.log";
    public static $log_level = 'debug'; // one of debug or error
    public static $pid_file = "/var/run/stats_aggregator.pid";

    // purging thresholds
    public static $age_minutely_stats = "P1D"; // purge all minutely records greater than 1 day old
    public static $age_hourly_stats = "P7D"; // purge all hourly records greater than 7 days old
    public static $age_daily_stats = "P1M"; // purge all daily records greater than 1 month old
    public static $age_monthly_stats = "P50Y"; // dont purge the monthly records
}
