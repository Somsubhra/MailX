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

$api_key = $_SESSION["MAILX_API_KEY"];
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
    <div class="pure-u-6-24" id="preview-pane"></div>
    <div class="pure-u-14-24" id="view-pane">
    </div>
    <div class="pure-u-3-24" id="contacts-pane">
        <h1 id="logo">MailX</h1>
    </div>
</div>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="static/js/mailx.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        set_api_key('<?php echo $api_key ?>', function() {
            load_contacts();
            load_preview();
        });
    });
</script>
</body>
</html>

<?php
session_write_close();
?>