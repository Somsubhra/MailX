var api_key = "";

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

function get_thread_preview(thread) {
    var result = "<div class='thread-subject'>" + thread.subject + "</div>";
    result += "<div class='thread-snippet'>" + thread.snippet + "...</div>";
    return result;
}

function load_contacts() {
    $.get("api/contacts.php",
        {
            api_key: api_key
        }, function(data) {
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
            var threads = data.body.threads;
            var num_threads = threads.length;

            for(var i = 0; i < num_threads; i++) {
                console.log(threads[i]);
                $("#preview-box").append("<div class='thread'>" + get_thread_preview(threads[i]) + "</div>");
            }
        }, "json");
}