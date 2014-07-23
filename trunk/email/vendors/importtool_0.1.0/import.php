<?php

const OFFSET_EMAIL   = 0;
const OFFSET_FIRST   = false;
const OFFSET_LAST    = false;
const OFFSET_CITY    = false;
const OFFSET_STATE   = false;
const OFFSET_COUNTRY = false;
const OFFSET_ZIP     = false;
const OFFSET_DOMAIN  = false;
const OFFSET_GENDER  = false;
const OFFSET_IP      = false;

const HYGIENE_DATE   = '2014-05-20 00:00:00';

date_default_timezone_set('UTC');

$file     = '/home/ec2-user/aol-1.2mill-verifiedscored-legacy2014.csv';
$sourceCampaign = 'AOLlegacyVerified_052014';
$start = 0;

$postUrl  = 'http://default-163604706.us-west-2.elb.amazonaws.com/email/api/lead.php?apikey=7CmCznYgpQgpOrV5PKf3RSbM98UTlZ';
$errorLog = './error_log';
$lastLog  = './last_log';

$count = 0;
$total = 0;
$success = 0;
$duplicate = 0;
$failed = 0;

if (($handle = fopen($file, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

        $count++;

        $lastHandle = fopen($lastLog, "w");
        fputs($lastHandle, $count);
        fclose($lastHandle);

        if ($count < $start) { continue; }

        $total++;

        if (OFFSET_COUNTRY !== false) {
            if(strcmp($data[OFFSET_COUNTRY], 'USA') || strcmp($data[OFFSET_COUNTRY], 'US')) {
                $packet['country'] = 'US';
            }
        }
        else {
            $packet['country'] = 'US';
        }

        $packet['email']        = (OFFSET_EMAIL  !== false) ? trim($data[OFFSET_EMAIL], "' ")  : null;
        $packet['firstname']    = (OFFSET_FIRST  !== false) ? trim($data[OFFSET_FIRST], "' ")  : null;
        $packet['lastname']     = (OFFSET_LAST   !== false) ? trim($data[OFFSET_LAST], "' ")   : null;
        $packet['city']         = (OFFSET_CITY   !== false) ? trim($data[OFFSET_CITY], "' ")   : null;
        $packet['state']        = (OFFSET_STATE  !== false) ? trim($data[OFFSET_STATE], "' ")  : null;
        $packet['postalcode']   = (OFFSET_ZIP    !== false) ? trim($data[OFFSET_ZIP], "' ")    : null;
        $packet['sourcedomain'] = (OFFSET_DOMAIN !== false) ? trim($data[OFFSET_DOMAIN], "' ") : null;

        if (OFFSET_GENDER !== false) {
            if($data[OFFSET_GENDER] == "M") {
                $packet['gender']    = '1';
            } else if($data[OFFSET_GENDER] == "F") {
                $packet['gender']    = '2';
            }
        }

        $packet['ip'] = (OFFSET_IP !== false) ? trim($data[OFFSET_IP], "' ") : null;
        $packet['sourcecampaign'] = $sourceCampaign;

        $packet = array_filter($packet);

        $xml = new SimpleXMLElement('<leads/>');
        $lead = $xml->addChild('lead');

        foreach($packet AS $key => $value) {
            $lead->addChild($key, $value);
        }

        if (HYGIENE_DATE !== false) {
            $lead->addChild('hygienedatetime', HYGIENE_DATE);
        }

        if(!sendPacket((string)$xml->asXML(), $postUrl, $duplicate, $success, $errorLog)) {
            echo "\nRetrying.";
            if(!sendPacket((string)$xml->asXML(), $postUrl, $duplicate, $success, $errorLog)) {
            
                $failed++;
            }
        } else {
            $success++;
        }
    }

    fclose($handle);
    echo "\n\nResults: $success accepted, $duplicate duplicate, $failed failed.";
}


function sendPacket($data, $postUrl, &$duplicate, &$success, $errorLog) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_URL, $postUrl);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 4);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if($httpCode == '200' && !curl_errno($ch) && $result !== false) {

        if ($result == 'LEAD REJECTED: Duplicate') {
            $duplicate++;
            $success--;
        }

        curl_close($ch);
        return true;
    } else {
        curl_close($ch);
        $errorHandle = fopen($errorLog, "a");
        fputs($errorHandle, $result);
        fclose($errorHandle);

        return false;
    }
}
//--------------------------------------------------------------------------


function getDomain($url) {
    $urlMap = array('com',
                    'net',
                    'org',
                    'co.uk');

    $urlData = parse_url($url);
    $hostData = explode('.', $urlData['host']);
    $hostData = array_reverse($hostData);

    if(array_search($hostData[1] . '.' . $hostData[0], $urlMap) !== FALSE) {
      $host = $hostData[2] . '.' . $hostData[1] . '.' . $hostData[0];
    } elseif(array_search($hostData[0], $urlMap) !== FALSE) {
      $host = $hostData[1] . '.' . $hostData[0];
    }

    return $host;
}
//--------------------------------------------------------------------------


function convertDate($date, $oldFormat, $newFormat) {
    $original = DateTime::createFromFormat($oldFormat, $date);
    $modified = $original->format($newFormat);

    return $modified;
}
//--------------------------------------------------------------------------
