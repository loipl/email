<?php

require_once dirname(__FILE__) . '/../email.php';

?>

<html>
    <head>
        <title>Campaign Array Creator</title>
    </head>
    <body>
        <form action="make-campaign-array.php" method="post">
            <table>
                <tr>
                    <td>Minimum Score</td>
                    <td><input name="minimum_score" size="5" value="1"></td>
                </tr>
                <tr>
                    <td>Interval Days</td>
                    <td><input name="interval" size="5"></td>
                </tr>
                <tr>
                    <td>Batch Size</td>
                    <td><input name="batch_size" size="5"></td>
                </tr>
                <tr>
                    <td>Campaign ID</td>
                    <td><input name="campaign_id" size="5"></td>
                </tr>
                <tr>
                    <td>TLD List</td>
                    <td><input name="tld_list" size="40"> False <input type="checkbox" name="tld_false" value="yes"> (if checked, text box is ignored)</td>
                </tr>
                <tr>
                    <td>Country List</td>
                    <td><input name="country_list" size="40"></td>
                </tr>
                <tr>
                    <td>State List</td>
                    <td><input name="state_list" size="40"></td>
                </tr>
                <tr>
                    <td>Lead Type</td>
                    <td>
                        <select name="lead_type">
                            <option SELECTED value="verified">Verified</option>
                            <option value="openers">Openers</option>
                            <option value="clickers">Clickers</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Gender</td>
                    <td>
                        <select name="gender">
                            <option SELECTED value="">Both</option>
                            <option value="M">Male</option>
                            <option value="F">Female</option>
                        </select>
                    </td>
                </tr>
            </table>
            <br /><input type="submit">
        </form>
    </body>
</html>

<?php

if ($_POST) {

$tlds = explode(',', $_POST['tld_list']);
$countries = explode(',', $_POST['country_list']);
$states = explode(',', $_POST['state_list']);

foreach ($tlds AS &$tld) {
    $tld = trim($tld);
}

foreach ($countries AS &$country) {
    $country = trim($country);
}

foreach ($states AS &$state) {
    $state = trim($state);
}

if (empty($_POST['interval'])) {
    switch ($_POST['lead_type']) {
        case "verified" :
            $interval = Config::INTERVAL_VERIFIED;
            break;

        case "openers" :
            $interval = Config::INTERVAL_OPENER;
            break;

        case "clickers" :
            $interval = Config::INTERVAL_CLICKER;
            break;
    }
} else {
    $interval = $_POST['interval'];
}

$campaignArray['countOnly'] = false;
$campaignArray['queryName'] = 'cron' . $_POST['lead_type'];
$campaignArray['count'] = (!empty($_POST['count'])) ? $_POST['count'] : Config::MAX_BATCH_SIZE;
$campaignArray['interval'] = $interval;
$campaignArray['type'] = $_POST['lead_type'];
$campaignArray['minScore'] = $_POST['minimum_score'];
$campaignArray['campaignId'] = $_POST['campaign_id'];

if ($_POST['tld_false'] == 'yes') {
    $campaignArray['tldList'] = false;
} else {
    $campaignArray['tldList'] = (!empty($_POST['tld_list'])) ? $tlds : NULL;
}

$campaignArray['gender'] = (!empty($_POST['gender'])) ? $_POST['gender'] : NULL;
$campaignArray['country'] = (!empty($_POST['country_list'])) ? $countries : NULL;
$campaignArray['state'] = (!empty($_POST['state_list'])) ? $states : NULL;

?>

<strong>Campaign Array</strong>
<form>
    <textarea rows="15" cols="80"><?php echo serialize($campaignArray); ?></textarea>
</form>

<?php

}