<?php
error_reporting(E_ERROR | E_PARSE);

session_start();
if(isset($_SESSION["MAILX_LOGGED_IN"])) {
    if($_SESSION["MAILX_LOGGED_IN"] == "true") {
        header("location: home.php");
        exit();
    }
}
?>
<html>
<head>
    <title>
        MailX - Login
    </title>
    <link href='http://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
    <link rel="stylesheet" href="static/css/mailx.css"/>
</head>
<body>
<div class="pure-g">
    <div class="pure-u-1-4"></div>
    <div class="pure-u-1-2 margin-top-200">
        <h1 style="color: rgba(0, 0, 0, 0.6)">MailX - Login</h1>
        <?php
        if(isset($_SESSION["LOGIN_ERROR"])) {
            echo "<div class='error'>" . $_SESSION["LOGIN_ERROR"] . "</div>";
        }
        session_destroy();
        ?>
        <form method="post" action="x/login.php" class="pure-form">
            <input type="email" placeholder="Email Address" name="emailaddress" id="emailaddress" required class="pure-input-rounded">
            <input type="password" placeholder="Password" name="password" id="password" required class="pure-input-rounded">
            <input type="submit" value="Go MailX!" class="pure-button pure-button-primary rounded-button">
        </form>
    </div>
    <div class="pure-u-1-4"></div>
</div>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="static/js/mailx.js"></script>
</body>
</html>