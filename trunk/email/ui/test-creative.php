<?php

require_once dirname(__FILE__) . '/../email.php';

?>

<html>
    <head>
        <title>Campaign Array Creator</title>
    </head>
    <body>
        <form action="test-creative.php" method="post">
            <table>
                <tr>
                    <td>ESP</td>
                    <td>
                        <select name="channel">
                            <option SELECTED value="Dynect">DynECT</option>
                            <option value="SmtpCom">SMTP.com</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td><input name="email" size="40"></td>
                </tr>
                <tr>
                    <td>Creative ID</td>
                    <td><input name="creative" size="20"></td>
                </tr>
            </table>
            <br /><input type="submit">
        </form>
    </body>
</html>

<?php

if ($_POST) {

    if (!is_numeric($_POST['creative'])) {
        die ('Creative ID must be numeric');
    }

    if ($_POST['channel'] != 'SmtpCom' && $_POST['channel'] != 'Dynect') {
        die('Invalid Channel');
    }

    $channelObject = new $_POST['channel'];

    $creativeData = Engine_Scheduler_Creatives::getCreativeData($_POST['creative']);
    $channelObject->sendEmail($_POST['email'], $creativeData['from_name'], $creativeData['sender_email'],
                              $creativeData['subject'], $creativeData['html_body'], $creativeData['text_body'],
                              null, null, true);
}