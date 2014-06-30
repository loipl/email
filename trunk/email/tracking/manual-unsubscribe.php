<?php
require_once '../email.php';

if (!empty($_POST)) {
    $email = trim($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email. Please enter a valid email.");
    } else {
        if (Lead::addUnsubscribe($email)) {
            Lead::scoreUnsubscribe($email);
            die($email . ' has been unsubscribed successfully.');
        } else {
            die ('ERROR: ' . $email . ' has NOT been unsubscribed successfully.');
        }
    }
}
?>

<!DOCTYPE html>
<html>
    <head></head>
    <body>
        <h1>Please enter your e-mail address to unsubscribe:</h1>
        <br />
        <form action="manual-unsubscribe.php" method="post">
            <input name="email" type="text" />
            <input type="submit" value="Submit" />
        </form>
    </body>
</html>