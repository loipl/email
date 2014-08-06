<?php
    require_once dirname(__FILE__) . '/../email.php';
    session_start();
    
    $page = 'login';
    $pageTitle = 'Login';
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
?>
<?php include('layout/header.php');?>

<!-- Top fixed navigation -->
<div class="topNav">
    <div class="wrapper">
        <div class="userNav">
            <ul>
                <li><a href="#" title=""><img src="images/icons/topnav/mainWebsite.png" alt="" /><span>Main website</span></a></li>
                <li><a href="#" title=""><img src="images/icons/topnav/profile.png" alt="" /><span>Contact admin</span></a></li>
                <li><a href="#" title=""><img src="images/icons/topnav/messages.png" alt="" /><span>Support</span></a></li>
                <li><a href="login.html" title=""><img src="images/icons/topnav/settings.png" alt="" /><span>Settings</span></a></li>
            </ul>
        </div>
        <div class="clear"></div>
    </div>
</div>

<!-- Main content wrapper -->
<div class="loginWrapper">
    <div class="loginLogo"><img src="images/loginLogo.png" alt="" /></div>
    <div class="widget">
        <div class="title"><img src="images/icons/dark/files.png" alt="" class="titleIcon" /><h6>Login panel</h6></div>
        <form action="login.php" method="POST" id="validate" class="form">
            <fieldset>
                <div class="formRow">
                    <label for="login">Username:</label>
                    <div class="loginInput"><input type="text" name="username" class="validate[required]" id="login" /></div>
                    <div class="clear"></div>
                </div>
                
                <div class="formRow">
                    <label for="pass">Password:</label>
                    <div class="loginInput"><input type="password" name="password" class="validate[required]" id="pass" /></div>
                    <div class="clear"></div>
                </div>
                <div class="loginControl">
                    <div class="rememberMe"><input type="checkbox" id="remMe" name="remMe" /><label for="remMe">Remember me</label></div>
                    <input type="submit" value="Log me in" class="dredB logMeIn" />
                    <div class="clear"></div>
                </div>
            </fieldset>
        </form>
    </div>
</div>
<?php include('layout/footer.php');?>
<?php } else { ?>
<?php
    if (!empty($_POST['username'])) {
        $username = $_POST['username'];
    }

    if (!empty($_POST['password'])) {
        $password = $_POST['password'];
        $hashPassword = md5(Config::SECRET_CODE . $password . Config::SECRET_CODE);
    }
    
    if (!empty($username) && !empty($hashPassword)) {
        if (User::checkUserExists($username, $hashPassword)) {
            $_SESSION['user_login'] = $username;

            if(isset($_POST['remMe'])) {
                setcookie('username', $username, time() + 1*24*60*60);
                setcookie('password', $hashPassword, time() + 1*24*60*60);
            } else {
                //destroy any previously set cookie
                setcookie('username', '', time() - 1*24*60*60);
                setcookie('password', '', time() - 1*24*60*60);
            }
            
            header("Location: campaigns.php");
            die();
        } else {
            header("Location: login.php");
            die();
        }
    }
?>
<?php } ?>
