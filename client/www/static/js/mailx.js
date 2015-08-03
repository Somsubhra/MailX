var api_key = "";

function set_api_key(param_api_key, callback) {
    api_key = param_api_key;
    callback();
}

function load_contacts() {
    console.log("Contacts API key: " + api_key);
}

function load_preview() {
    console.log("Preview API key: " + api_key);
}