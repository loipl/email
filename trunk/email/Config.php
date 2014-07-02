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

    public static $debugLevel      = 2;

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
}
