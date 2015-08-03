<?php
include "../etc/config.php";

error_reporting(E_ERROR | E_PARSE);

session_start();

if(!isset($_SESSION["MAILX_LOGGED_IN"])) {
    header("location: index.php");
}

if($_SESSION["MAILX_LOGGED_IN"] !=  "true") {
    header("location: index.php");
}

$api_key = $_GET["MAILX_API_KEY"];
?>

<html>
<head>
    <title>
        MailX
    </title>
    <link href='http://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
    <link rel="stylesheet" href="static/css/mailx.css"/>
</head>
<body>
<div class="pure-g">
    <div class="pure-u-1-4"></div>
    <div class="pure-u-1-2 margin-top-200">
        <h1>MailX | <a href="logout.php">Logout</a></h1>
    </div>
    <div class="pure-u-1-4"></div>
</div>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="static/js/mailx.js"></script>
</body>
</html>

<?php
session_write_close();
?>