#!/bin/sh

apt-get install -y apache2
apt-get install -y php5 libapache2-mod-php5

/etc/init.d/apache2 restart
php -r 'echo "\n\nYour PHP installation is working fine.\n\n\n";'
