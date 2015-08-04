var api_key = "";
var account_email_address = "";
var account_name = "";
var account_display_name = "";

function set_api_key(param_api_key, callback) {
    api_key = param_api_key;
    callback();
}

function get_contact_display_name(contact) {
    if(contact.name == "") {
        return contact.email;
    }
    return contact.name;
}

function get_participants_string(participants) {
    var num_participants = participants.length;
    var participants_string = "";
    for(var i = 0; i < num_participants; i++) {
        if(participants[i].email == account_email_address) {
            continue;
        }

        participants_string += get_contact_display_name(participants[i]) + ", ";
    }
    return participants_string.replace(/(^\s*,)|(,\s*$)/g, '');
}

function get_thread_preview(thread) {
    var result = "";
    result += "<div class='thread-participants'>" + get_participants_string(thread.participants) + "</div>";
    result += "<div class='thread-subject'>" + thread.subject + "</div>";
    result += "<div class='thread-snippet'>" + thread.snippet + "...</div>";
    return result;
}

function load_account_details(callback) {
    $.get("api/index.php",
        {
            api_key: api_key
        }, function(data) {
            if(!data.success) {
                return;
            }
            var account = data.body.account;
            account_email_address = account.email_address;
            account_name = account.name;
            account_display_name = get_contact_display_name(account);
        }, "json").done(function() {
            callback();
        });
}

function load_contacts() {
    $.get("api/contacts.php",
        {
            api_key: api_key
        }, function(data) {
            if(!data.success) {
                return;
            }
            var contacts = data.body.contacts;
            var num_contacts = contacts.length;

            for(var i = 0; i < num_contacts; i++) {
                $("#contacts-box").append("<div class='contact'>" + get_contact_display_name(contacts[i]) + "</div>");
            }
        }, "json");
}

function load_preview() {
    $.get("api/threads.php",
        {
            api_key: api_key
        }, function(data) {
            if(!data.success) {
                return;
            }
            var threads = data.body.threads;
            var num_threads = threads.length;

            for(var i = 0; i < num_threads; i++) {
                console.log(threads[i]);
                $("#preview-box").append("<div class='thread'>" + get_thread_preview(threads[i]) + "</div>");
            }
        }, "json");
}