var api_key = "";
var account_email_address = "";
var account_name = "";
var account_display_name = "";

var num_contacts_loaded = 0;
var num_messages_loaded = 0;
var num_threads_loaded = 0;

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

function get_safe_html(html) {
    var barrier = document.createElement('DIV');
    barrier.innerHTML = html;

    var elements = barrier.querySelectorAll('*');
    for (var i = 0, l = elements.length; i < l; i++) {
        var el = elements[0];

        if (el.tagName === 'SCRIPT') {
            $(el).remove();
        }

        if(el.tagName === 'STYLE') {
            $(el).remove();
        }

        var attributes = el.attributes;
        for (var j = 0, m = attributes.length; j < m; j++) {
            var attribute = attributes[j];
            var name = attribute.name.toLowerCase();

            if (name === 'href') {
                var value = attribute.value;
                if (value.indexOf('http:') !== 0 || value.indexOf('https:') !==0) {
                    attribute.value = '#';
                }
            } else if (name.indexOf('on') === 0) {
                attribute.value = '';
            }
        }
    }

    return barrier.innerHTML;
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
    time = yyyy + '-' + mm + '-' + dd + ' at ' + h + ':' + min + ' ' + ampm;
    return time;
}

function get_contact_display_name(contact) {
    if(contact.name == "" || contact.name == null) {
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

function get_message_view(message) {
    var result = "";

    var sender_string = "";
    var num_senders = message.from.length;
    for(var i = 0; i < num_senders; i++) {
        sender_string += get_contact_display_name(message.from[i]);
    }
    sender_string.replace(/(^\s*,)|(,\s*$)/g, '');

    result += "<div class='message-header'>";
    result += "<div class='message-subject'>" + message.subject + "</div>";
    result += "<div class='message-sender-time'>On " + timestamp_to_localtime(message.date) + ", " + sender_string + " wrote...</div>";
    result += "</div>";

    result += get_safe_html(message.body);
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
            api_key: api_key,
            offset: num_contacts_loaded
        }, function(data) {
            if(!data.success) {
                return;
            }
            var contacts = data.body.contacts;
            var num_contacts = contacts.length;

            for(var i = 0; i < num_contacts; i++) {
                num_contacts_loaded++;
                
                if(contacts[i].email ==  account_email_address) {
                    continue;
                }

                $("#contacts-box").append("<div data-name='" + contacts[i].name +
                    "' data-email='" + contacts[i].email + "' class='contact'>" +
                    get_contact_display_name(contacts[i]) + "</div>");
            }
        }, "json");
}

function load_preview() {

    $.get("api/threads.php",
        {
            api_key: api_key,
            offset: num_threads_loaded
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
                num_threads_loaded++;
            }
        }, "json");
}

function load_view(thread_id) {
    var message_box = $("#message-box");
    var view_pane = $("#view-pane");

    message_box.html("");

    $.get("api/messages.php",
        {
            api_key: api_key,
            thread_id: thread_id
        }, function(data) {
            if(!data.success) {
                return;
            }

            $("#placeholder").hide();
            message_box.show();
            $("#send-pane").show();
            view_pane.css("height", "85%");

            var messages = data.body.messages;
            var num_messages = messages.length;

            num_messages_loaded = 0;

            for(var i = 0; i < num_messages; i++) {
                $("#message-box").append("<div class='message'>" + get_message_view(messages[num_messages - i - 1]) + "</div>");
                num_messages_loaded++;
            }

            view_pane.scrollTop(view_pane.prop("scrollHeight"));
        }, "json");
}

function load_new_thread_view(name, email) {
    var display_name = name;
    if(display_name == "") {
        display_name = email;
    }

    $("#placeholder").hide();
    $("#message-box").hide();
    $("#view-pane").css("height", "85%");
    $("#send-pane").show();

    $("#preview-box").prepend("<div data-id='-1' data-email='" + email +
        "' data-name='" + name + "' class='thread selected-thread read-thread new-thread'>" +
        "<div class='thread-participants'>" + display_name + "</div>" +
        "<div class='thread-subject'>Start a new conversation</div>" +
        " </div>");
}

function send_message() {
    var send_input = $("#send-input");

    if($.trim(send_input.val()) == "") {
        return;
    }

    var thread_id = $(".selected-thread").attr("data-id");

    if(thread_id != -1) {
        $.post("api/reply.php",
            {
                api_key: api_key,
                thread_id: thread_id,
                message: send_input.val()
            }, function(data) {
                if(!data.success) {
                    return;
                }
                send_input.val("");
            }, "json");
    } else {
        var new_thread_view = $(".new-thread");
        var email = new_thread_view.attr("data-email");
        var name = new_thread_view.attr("data-name");

        $.post("api/send.php",
            {
                api_key: api_key,
                name: name,
                email: email,
                subject: "MailX Conversation",
                message: send_input.val()
            }, function(data) {
                if(!data.success) {
                    return;
                }
                send_input.val("");
                $(".new-thread").remove();
            }, "json");
    }
}

function activate_event_listeners() {
    $("#preview-box").on("click", '.thread', function() {
        load_view($(this).attr("data-id"));
        $(".new-thread").remove();
        $(".selected-thread").attr("class", "thread read-thread");
        $(this).attr("class", "thread read-thread selected-thread");
    });

    $("#contacts-box").on("click", '.contact', function() {
        $(".new-thread").remove();
        $(".selected-thread").attr("class", "thread read-thread");
        load_new_thread_view($(this).attr("data-name"), $(this).attr("data-email"));
    });

    $("#send-input").keypress(function(e) {
        if(e.which == 13) {
            e.preventDefault();
            send_message();
        }
    });

    var search_contact_input = $("#search-contact-input");

    search_contact_input.keypress(function(e) {
        if(e.which == 13) {
            e.preventDefault();
            var email = search_contact_input.val();
            if(email == "") {
                return;
            }
            $(".new-thread").remove();
            $(".selected-thread").attr("class", "thread read-thread");
            load_new_thread_view("", email);
            search_contact_input.val("");
        }
    });

    $("#preview-pane").on("scroll", function() {
        if($(this).scrollTop() + $(this).innerHeight() >= this.scrollHeight) {
            load_preview();
        }
    });

    $("#contacts-pane").on("scroll", function() {
        if($(this).scrollTop() + $(this).innerHeight() >= this.scrollHeight) {
            load_contacts();
        }
    });

    $("#view-pane").on("scroll", function() {
        if($(this).scrollTop() < 5) {
            console.log("Load more messages");
            console.log("Messages offset: " + num_messages_loaded);
        }
    });
}