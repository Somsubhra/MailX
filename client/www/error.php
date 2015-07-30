<?php
$messageString = "";

switch($_GET["code"]) {
    case "DB_ERR":
        $messageString = "We are having problems accessing our data!";
        break;
    default:
        $messageString = "Something broke!";
        break;
}
?>
<html>
<head>
    <title>
        MailX - Error
    </title>
    <link href='http://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
    <link rel="stylesheet" href="static/css/mailx.css"/>
</head>
<body>
<div class="pure-g">
    <div class="pure-u-1-4"></div>
    <div class="pure-u-1-2 margin-top-200">
        <h1><?php echo $messageString ?></h1>
    </div>
    <div class="pure-u-1-4"></div>
</div>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="static/js/mailx.js"></script>
</body>
</html>