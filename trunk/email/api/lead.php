<?php

require_once dirname(__FILE__) . '/core.php';

const MYSQL_ERROR_DUPLICATE_KEY = 1062;

$xmlData = file_get_contents('php://input');

if ($xmlData) {
    $xml = new SimpleXMLElement($xmlData);

    if (!filter_var($xml->lead->email[0], FILTER_VALIDATE_EMAIL)) {
        die("LEAD REJECTED: The email address provided ({$xml->lead->email[0]}) is invalid.");
    }

    $parts = explode('@', $xml->lead->email[0]);

    $email = $xml->lead->email[0];
    $domain = $parts[1];
    $md5_email = md5($email);
    $md5_domain = md5($domain);
    $address = (string)$xml->lead->address[0];
    $firstName = (string)$xml->lead->firstname[0];
    $lastName = (string)$xml->lead->lastname[0];
    $country = (string)$xml->lead->country[0];
    $phone = (string)$xml->lead->phone[0];
    $os = (string)$xml->lead->os[0];
    $language = (string)$xml->lead->language[0];
    $state = (string)$xml->lead->state[0];
    $city = (string)$xml->lead->city[0];
    $postalCode = (string)$xml->lead->postalcode[0];
    $sourceDomain = (string)$xml->lead->sourcedomain[0];
    $sourceUrl = (string)$xml->lead->sourceurl[0];
    $sourceCampaign = (string)$xml->lead->sourcecampaign[0];
    $sourceUsername = (string)$xml->lead->sourceusername[0];
    $ip = (string)$xml->lead->ip[0];
    $subscribeDate = (string)$xml->lead->subscribedate[0];
    $birthDay = (string)$xml->lead->birthday[0];
    $birthMonth = (string)$xml->lead->birthmonth[0];
    $birthYear = (string)$xml->lead->birthyear[0];
    $gender = (string)$xml->lead->gender[0];
    $seeking = (string)$xml->lead->seeking[0];
    $hygieneDate = (string)$xml->lead->hygienedatetime[0];

    if (!empty($xml->lead->score[0])) {
        $feedScore = (int)$xml->lead->score[0];

        if (($feedScore <= 100) && ($feedScore >= 0)) {
            $score = $feedScore;
        } else {
            $score = Config::SCOREMOD_NEW;
        }
    } else {
        $score = Config::SCOREMOD_NEW;
    }

    $db = new Database;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $sql = "INSERT INTO `leads` (email, domain, score, md5_email, md5_domain";

        if (!empty($address)) {
            $sql .= ", address";
        }

        if (!empty($firstName)) {
            $sql .= ", first_name";
        }

        if (!empty($lastName)) {
            $sql .= ", last_name";
        }

        if (!empty($country)) {
            $sql .= ", country";
        }

        if (!empty($phone)) {
            $sql .= ", phone";
        }

        if (!empty($os)) {
            $sql .= ", os";
        }

        if (!empty($language)) {
            $sql .= ", language";
        }

        if (!empty($state)) {
            $sql .= ", state";
        }

        if (!empty($city)) {
            $sql .= ", city";
        }

        if (!empty($postalCode)) {
            $sql .= ", postal_code";
        }

        if (!empty($sourceDomain)) {
            $sql .= ", source_domain";
        }

        if (!empty($sourceUrl)) {
            $sql .= ", source_url";
        }

        if (!empty($sourceCampaign)) {
            $sql .= ", source_campaign";
        }

        if (!empty($sourceUsername)) {
            $sql .= ", source_username";
        }

        if (!empty($ip)) {
            $sql .= ", ip";
        }

        if (!empty($subscribeDate)) {
            $sql .= ", subscribe_datetime";
        }

        if (!empty($birthDay)) {
            $sql .= ", birth_day";
        }

        if (!empty($birthMonth)) {
            $sql .= ", birth_month";
        }

        if (!empty($birthYear)) {
            $sql .= ", birth_year";
        }

        if (!empty($gender)) {
            $sql .= ", gender";
        }

        if (!empty($seeking)) {
            $sql .= ", seeking";
        }

        if (!empty($hygieneDate)) {
            $sql .= ", hygiene_datetime";
        }

        $sql .= ") VALUES ";
        $sql .= "('" . $email . "',";
        $sql .= " '" . $domain . "',";
        $sql .= " '" . $score . "',";
        $sql .= " '" . $md5_email . "',";
        $sql .= " '" . $md5_domain . "'";

        if (!empty($address)) {
            $sql .= ", '" . mysql_real_escape_string($address) . "'";
        }

        if (!empty($firstName)) {
            $sql .= ", '" . mysql_real_escape_string($firstName) . "'";
        }

        if (!empty($lastName)) {
            $sql .= ", '" . mysql_real_escape_string($lastName) . "'";
        }

        if (!empty($country)) {
            $sql .= ", '" . mysql_real_escape_string($country) . "'";
        }

        if (!empty($phone)) {
            $sql .= ", '" . mysql_real_escape_string($phone) . "'";
        }

        if (!empty($os)) {
            $sql .= ", '" . mysql_real_escape_string($os) . "'";
        }

        if (!empty($language)) {
            $sql .= ", '" . mysql_real_escape_string($language) . "'";
        }

        if (!empty($state)) {
            $sql .= ", '" . mysql_real_escape_string($state) . "'";
        }

        if (!empty($city)) {
            $sql .= ", '" . mysql_real_escape_string($city) . "'";
        }

        if (!empty($postalCode)) {
            $sql .= ", '" . mysql_real_escape_string($postalCode) . "'";
        }

        if (!empty($sourceDomain)) {
            $sql .= ", '" . mysql_real_escape_string($sourceDomain) . "'";
        }

        if (!empty($sourceUrl)) {
            $sql .= ", '" . mysql_real_escape_string($sourceUrl) . "'";
        }

        if (!empty($sourceCampaign)) {
            $sql .= ", '" . mysql_real_escape_string($sourceCampaign) . "'";
        }

        if (!empty($sourceUsername)) {
            $sql .= ", '" . mysql_real_escape_string($sourceUsername) . "'";
        }

        if (!empty($ip)) {
            $sql .= ", '" . mysql_real_escape_string($ip) . "'";
        }

        if (!empty($subscribeDate)) {
            $sql .= ", '" . mysql_real_escape_string($subscribeDate) . "'";
        }

        if (!empty($birthDay)) {
            $sql .= ", '" . mysql_real_escape_string($birthDay) . "'";
        }

        if (!empty($birthMonth)) {
            $sql .= ", '" . mysql_real_escape_string($birthMonth) . "'";
        }

        if (!empty($birthYear)) {
            $sql .= ", '" . mysql_real_escape_string($birthYear) . "'";
        }

        if (!empty($gender)) {
            $sql .= ", '" . mysql_real_escape_string($gender) . "'";
        }

        if (!empty($seeking)) {
            $sql .= ", '" . mysql_real_escape_string($seeking) . "'";
        }

        if (!empty($hygieneDate)) {
            $sql .= ", '" . mysql_real_escape_string($hygieneDate) . "'";
        }

        $sql .= ");";

        $result = $db->query($sql);

        if (is_array($result)) {
            if ($result['error_number'] == MYSQL_ERROR_DUPLICATE_KEY) {
                echo "LEAD REJECTED: Duplicate";
            } else {
                echo "LEAD REJECTED: " . $result['error'];
            }
        } else {
            echo "LEAD ACCEPTED";
        }
    } else if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $sql = "UPDATE `leads` SET";
        $update = false;
        if (!empty($score)) {
            $sql .= " `score` = '" . mysql_real_escape_string($score) . "'";
            $update = true;
        }
        if (!empty($address)) {
            $sql .= (($update) ? "," : "") . " `address` = '" . mysql_real_escape_string($address) . "'";
            $update = true;
        }
        if (!empty($firstName)) {
            $sql .= (($update) ? "," : "") . " `first_name` = '" . mysql_real_escape_string($firstName) . "'";
            $update = true;
        }
        if (!empty($lastName)) {
            $sql .= (($update) ? "," : "") . " `last_name` = '" . mysql_real_escape_string($lastName) . "'";
            $update = true;
        }
        if (!empty($country)) {
            $sql .= (($update) ? "," : "") . " `country` = '" . mysql_real_escape_string($country) . "'";
            $update = true;
        }
        if (!empty($phone)) {
            $sql .= (($update) ? "," : "") . " `phone` = '" . mysql_real_escape_string($phone) . "'";
            $update = true;
        }
        if (!empty($os)) {
            $sql .= (($update) ? "," : "") . " `os` = '" . mysql_real_escape_string($os) . "'";
            $update = true;
        }
        if (!empty($language)) {
            $sql .= (($update) ? "," : "") . " `language` = '" . mysql_real_escape_string($language) . "'";
            $update = true;
        }
        if (!empty($state)) {
            $sql .= (($update) ? "," : "") . " `state` = '" . mysql_real_escape_string($state) . "'";
            $update = true;
        }
        if (!empty($city)) {
            $sql .= (($update) ? "," : "") . " `city` = '" . mysql_real_escape_string($city) . "'";
            $update = true;
        }
        if (!empty($postalCode)) {
            $sql .= (($update) ? "," : "") . " `postal_code` = '" . mysql_real_escape_string($postalCode) . "'";
            $update = true;
        }
        if (!empty($sourceDomain)) {
            $sql .= (($update) ? "," : "") . " `source_domain` = '" . mysql_real_escape_string($sourceDomain) . "'";
            $update = true;
        }
        if (!empty($sourceCampaign)) {
            $sql .= (($update) ? "," : "") . " `source_campaign` = '" . mysql_real_escape_string($sourceCampaign) . "'";
            $update = true;
        }
        if (!empty($sourceUsername)) {
            $sql .= (($update) ? "," : "") . " `source_username` = '" . mysql_real_escape_string($sourceUsername) . "'";
            $update = true;
        }
        if (!empty($ip)) {
            $sql .= (($update) ? "," : "") . " `ip` = '" . mysql_real_escape_string($ip) . "'";
            $update = true;
        }
        if (!empty($birthDay)) {
            $sql .= (($update) ? "," : "") . " `birth_day` = '" . mysql_real_escape_string($birthDay) . "'";
            $update = true;
        }
        if (!empty($birthMonth)) {
            $sql .= (($update) ? "," : "") . " `birth_month` = '" . mysql_real_escape_string($birthMonth) . "'";
            $update = true;
        }
        if (!empty($birthYear)) {
            $sql .= (($update) ? "," : "") . " `birth_year` = '" . mysql_real_escape_string($birthYear) . "'";
            $update = true;
        }
        if (!empty($gender)) {
            $sql .= (($update) ? "," : "") . " `gender` = '" . mysql_real_escape_string($gender) . "'";
            $update = true;
        }
        if (!empty($seeking)) {
            $sql .= (($update) ? "," : "") . " `seeking` = '" . mysql_real_escape_string($seeking) . "'";
            $update = true;
        }
        if (!empty($hygieneDate)) {
            $sql .= (($update) ? "," : "") . " `hygiene_datetime` = '" . mysql_real_escape_string($hygieneDate) . "'";
            $update = true;
        }
        $sql .= " WHERE `email` = '" . $email . "';";

        if ($update) {
            $result = $db->query($sql);

            if (is_array($result)) {
                if ($result['error_number'] == MYSQL_ERROR_DUPLICATE_KEY) {
                    echo "LEAD REJECTED: Duplicate";
                } else {
                    echo "LEAD REJECTED: " . $result['error'];
                }
            } else {
                echo "LEAD UPDATED";
            }
        } else {
            die("LEAD REJECTED: No Data.");
        }
    }
} else if ($_GET['id']) {
    if (!filter_var($_GET['id'], FILTER_VALIDATE_EMAIL)) {
        die("LEAD REJECTED: The email address provided is invalid.");
    }

    $lead = new Lead($_GET['id']);

    echo "<lead>\n";
    echo "\t<email>" . $lead->getEmail() . "</email>\n";
    echo "\t<address>" . $lead->getAddress() . "</address>\n";
    echo "\t<first_name>" . $lead->getFirstName() . "</first_name>\n";
    echo "\t<last_name>" . $lead->getLastName() . "</last_name>\n";
    echo "\t<country>" . $lead->getCountry() . "</country>\n";
    echo "\t<phone>" . $lead->getPhone() . "</phone>\n";
    echo "\t<os>" . $lead->getOS() . "</os>\n";
    echo "\t<language>" . $lead->getLanguage() . "</language>\n";
    echo "\t<state>" . $lead->getState() . "</state>\n";
    echo "\t<city>" . $lead->getCity() . "</city>\n";
    echo "\t<postal_code>" . $lead->getPostalCode() . "</postal_code>\n";
    echo "\t<sourcedomain>" . $lead->getDomainName() . "</sourcedomain>\n";
    echo "\t<sourceurl>" . $lead->getSourceUrl() . "</sourceurl>\n";
    echo "\t<sourcecampaign>" . $lead->getCampaign() . "</sourcecampaign>\n";
    echo "\t<sourceusername>" . $lead->getUsername() . "</sourceusername>\n";
    echo "\t<ip>" . $lead->getIP() . "</ip>\n";
    echo "\t<subscribedate>" . $lead->getSubscribeDate() . "</subscribedate>\n";
    echo "\t<birthday>" . $lead->getBirthDay() . "</birthday>\n";
    echo "\t<birthmonth>" . $lead->getBirthMonth() . "</birthmonth>\n";
    echo "\t<birthyear>" . $lead->getBirthYear() . "</birthyear>\n";
    echo "\t<gender>" . $lead->getGender() . "</gender>\n";
    echo "\t<seeking>" . $lead->getSeeking() . "</seeking>\n";
    echo "</lead>\n";
}
