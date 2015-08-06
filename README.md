MailX
======
Simple to use webmail client


### Run instructions
* Get sync-engine:
```
git submodule init
git submodule update
```

* Setup the VM by running the following:
```
vagrant up
```
* SSH into the box using:
```
vagrant ssh
```
* Go to the directory ```/vagrant/sync-engine/bin```:
```
cd /vagrant/sync-engine/bin
```
* Start sync-engine by running:
```
./inbox-start
```
* Add an email account by running:
```
./inbox-auth abc@xyz.com
```
* Start the sync-engine API by running:
```
./inbox-api
```
* Go to the directory ```/vagrant/client/bin```:
```
cd /vagrant/client/bin
```
* Run the following script:
```
./fetch_new_email_accounts.php
```
* Go to http://localhost:5556
* Sit back and enjoy.
