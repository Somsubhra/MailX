var api_key = "";
var account_email_address = "";
var account_name = "";
var account_display_name = "";

function set_api_key(param_api_key, callback) {
    api_key = param_api_key;
    callback();
}

function shorten_string(string, length) {
    if(string.length > length) {
        return string.substring(0, length) + "...";
    }
    return string;
}

function timestamp_to_localtime(timestamp) {
    var d = new Date(timestamp * 1000),
        yyyy = d.getFullYear(),
        mm = ('0' + (d.getMonth() + 1)).slice(-2),
        dd = ('0' + d.getDate()).slice(-2),
        hh = d.getHours(),
        h = hh,
        min = ('0' + d.getMinutes()).slice(-2),
        ampm = 'AM',
        time;
    if (hh > 12) {
        h = hh - 12;
        ampm = 'PM';
    } else if (hh == 0) {
        h = 12;
    }
    time = yyyy + '-' + mm + '-' + dd + ' | ' + h + ':' + min + ' ' + ampm;
    return time;
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
    result += "<div class='thread-participants'>" + shorten_string(get_participants_string(thread.participants), 25) +
        " <span class='num-thread-participants'>(" + thread.participants.length + " people)</span></div>";
    result += "<div class='thread-time'>" + timestamp_to_localtime(thread.last_message_timestamp) + "</div>";
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
                if(threads[i].unread == true) {
                    var el_class = "unread-thread";
                } else {
                    el_class = "read-thread"
                }

                $("#preview-box").append("<div data-id='" + threads[i].id + "' class='thread " + el_class + "'>" + get_thread_preview(threads[i]) + "</div>");
            }
        }, "json");
}

function load_view(thread_id) {
    $.get("api/messages.php",
        {
            api_key: api_key,
            thread_id: thread_id
        }, function(data) {
            if(!data.success) {
                return;
            }
            var messages = data.body.messages;
            console.log(messages);
        }, "json");
}