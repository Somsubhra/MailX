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
    <div class="pure-u-6-24" id="preview-pane">
        <div id="preview-box"></div>
    </div>
    <div class="pure-u-14-24" id="center-pane">
        <div class="pure-u-24-24" id="view-pane">
            <div id="placeholder">
                <div id="page-logo-lbl">MailX</div>
                <div id="no-selection-lbl">No threads have been selected</div>
            </div>
            <div id="message-box"></div>
        </div>
        <div class="pure-u-24-24 pure-form" id="send-pane">
            <textarea class="pure-input" id="send-input" placeholder="Enter your message to send"></textarea>
        </div>
    </div>
    <div class="pure-u-3-24" id="contacts-pane">
        <h1 id="logo">MailX</h1>
        <h5 id="contacts-lbl" style="margin-bottom: 10px">&#183; Contacts &#183;</h5>
        <div class="pure-form">
            <input type="text" placeholder="Enter contact detail" class="pure-input-rounded" id="search-contact-inp">
        </div>
        <div id="contacts-box"></div>
    </div>
</div>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="static/js/mailx.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $("#send-pane").hide();
        $("#message-box").hide();
        set_api_key('<?php echo $api_key ?>', function() {
            load_account_details(function() {
                load_contacts();
                load_preview();
                activate_event_listeners();
            });
        });
    });
</script>
</body>
</html>

<?php
session_write_close();
?>