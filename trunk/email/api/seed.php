<?php

require_once dirname(__FILE__) . '/core.php';

        const MYSQL_ERROR_DUPLICATE_KEY = 1062;

$xmlData = file_get_contents('php://input');

if ($xmlData) {

    $xml = new SimpleXMLElement($xmlData);

    if (!filter_var($xml->seed->email[0], FILTER_VALIDATE_EMAIL)) {
        die("SEED REJECTED: The email address provided ({$xml->seed->email[0]}) is invalid.");
    }

    $parts        = explode('@', $xml->seed->email[0]);
    // seed information
    $id           = (int) $xml->seed->id[0];
    $email        = $xml->seed->email[0];
    $domain       = $parts[1];
    $password     = (string) $xml->seed->password[0];
    // server credentials
    $host         = $xml->seed->host[0];
    $user         = $xml->seed->user[0];
    $hostPassword = (string) $xml->seed->hostPassword[0];
    $port         = $xml->seed->port[0];

    $method = $_SERVER['REQUEST_METHOD'];

    if ($method == 'POST') {

        if ($email != '' && $password != '' && $host != '' && $user != '' && $hostPassword != '' && $port != '') {

            $result = Seed::getSeed($email);

            if ($result) {
                echo "SEED REJECTED: Duplicate";
            } else {

                $addResult = Seed::addSeed($email, $password, $host, $user, $hostPassword, $port);

                if (is_array($addResult)) {
                    if ($addResult['error_number'] == MYSQL_ERROR_DUPLICATE_KEY) {
                        echo "SEED REJECTED: Duplicate";
                    } else {
                        echo "SEED REJECTED: " . $result['error'];
                    }
                } else {
                    echo "SEED ADDED SUCCESSFULLY";
                }
            }
        } else {
            echo "SEED EMAIL ID, PASSWORD, HOST, USER, HOST PASSWORD AND PORT REQUIRED";
        }
    } else if ($method == 'PUT') {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            die("SEED REJECTED: The email address provided is invalid.");
        }

        if ($email != '' && $password != '' && $host != '' && $user != '' && $hostPassword != '' && $port != '') {

            $result = Seed::updateSeed($email, $password, $host, $user, $hostPassword, $port);

            if (is_array($result)) {
                echo "SEED INFORMATION UPDATE FAILED : " . $result['error'];
            } else {
                echo "SEED INFORMATION UPDATED SUCCESSFULLY";
            }
        } else {
            echo "SEED EMAIL ID, PASSWORD, HOST, USER, HOST PASSWORD AND PORT  REQUIRED";
        }
    } else if ($method == 'DELETE') {

        if (is_numeric($id) && $id > 0) {

            $result = Seed::deleteSeed($id);

            if (is_array($result)) {
                echo "SEED DELETION FAILED : " . $result['error'];
            } else {
                echo "SEED DELETED SUCCESSFULLY";
            }
        } else {
            echo "SEED ID REQUIRED";
        }
    }
} else if ($_GET['id']) {
    if (!filter_var($_GET['id'], FILTER_VALIDATE_EMAIL)) {
        die("SEED REJECTED: The email address provided is invalid.");
    }

    $email = $_GET['id'];

    if ($email != '') {

        $seed = new Seed($email);
        $id   = $seed->getId();
        if ($id > 0) {
            echo "<seed>\n";
            echo "\t<id>" . $seed->getId() . "</id>\n";
            echo "\t<email>" . $seed->getEmail() . "</email>\n";
            echo "\t<password>" . $seed->getPassword() . "</password>\n";
            echo "\t<domain>" . $seed->getDomain() . "</domain>\n";
            echo "\t<host>" . $seed->getHost() . "</host>\n";
            echo "\t<user>" . $seed->getUser() . "</user>\n";
            echo "\t<hostPassword>" . $seed->getHostPassword() . "</hostPassword>\n";
            echo "\t<port>" . $seed->getPort() . "</port>\n";
            echo "</seed>\n";
        } else {
            echo "SEED NOT FOUND";
        }
    } else {
        echo "SEED EMAIL ID REQUIRED";
    }
}

