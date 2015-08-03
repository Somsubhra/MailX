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
            console.log(threads);
        }, "json");
}