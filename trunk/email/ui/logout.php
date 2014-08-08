<?php
/**
* The logout file
* expires the cookie
* redirects to login.php
*/
setcookie('username', '', time() - 1*24*60*60);
setcookie('password', '', time() - 1*24*60*60);
header("location: login.php");
?>