#!/bin/sh

apt-get install -y apache2
apt-get install -y php5 libapache2-mod-php5 php5-mysql php5-curl

/etc/init.d/apache2 restart
php -r 'echo "\n\nYour PHP installation is working fine.\n\n\n";'

if ! [ -L /var/www ]; then
  rm -rf /var/www
  ln -fs /vagrant/client/www /var/www
fi

echo "Creating database for MailX..."
mysql -u root -proot < db/schema.sql
