#!/bin/sh

main() {
    packages
    config
    setup
    db
    cleanup
}

packages() {
    
    # Workaround for old/broken repos in scotchbox
    if [ -e /etc/apt/sources.list.d/ondrej-php5-5_6-trusty.list ]; then
        rm /etc/apt/sources.list.d/ondrej*
    fi
    
    # re-add current ppa 
    add-apt-repository -y ppa:ondrej/php

    apt-get update

    # scotchbox uses a ppa for php/apache but the PPA does not have all the deps we need, like gmp 
    # so, remove existing, re-install and let dpkg figure it out
    dpkg --purge libapache2-mod-php5 php-pear php5 php5-cgi php5-cli php5-common php5-curl php5-fpm php5-gd php5-imagick php5-intl php5-json php5-mcrypt php5-memcache php5-memcached php5-mongo php5-mysql php5-mysqlnd php5-pgsql php5-readline php5-redis php5-sqlite apache2 apache2-bin apache2-data apache2-utils libapache2-mod-auth-mysql libapache2-mod-php5 pkg-php-tools

    apt-get -y install libapache2-mod-php7.1 php7.1 php7.1-cgi php7.1-cli php7.1-common php7.1-curl php7.1-fpm php7.1-gd php7.1-intl php7.1-json php7.1-mcrypt php7.1-mbstring php7.1-mysql php7.1-readline php7.1-redis php7.1-gmp php7.1-xml php7.1-snmp apache2 apache2-bin apache2-data apache2-utils libapache2-mod-auth-mysql avahi-daemon

    php5enmod mcrypt curl gd mysql gmp
    a2enmod rewrite php7.1
}

config() {
    
    cd /var/www/ipam/

    cp vagrant/avahi-daemon.conf /etc/avahi/
    cp vagrant/000-default.conf /etc/apache2/sites-available/
    
    if ! [ -e config/environments/vagrant.yml ]; then
        cp vagrant/vagrant.yml config/environments/
    fi
    
    # vanity
    cp vagrant/motd /etc/motd.tail
    
    # hostnames
    echo 'phpipam' > /etc/hostname
    echo 'phpipam' > /etc/mailname
    sed -i -e "s/scotchbox/phpipam/g" /etc/hosts
    hostname phpipam
}

setup() {

    cd /var/www/ipam/
    
    # Composer spits out too many errors
    /usr/local/bin/composer self-update 2>/dev/null

    sudo -H -u vagrant sh -c 'composer install'
    sudo -H -u vagrant sh -c 'npm install'
    sudo -H -u vagrant sh -c 'bower install'
    sudo -H -u vagrant sh -c 'grunt'

}

db() {
    echo "GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY 'root' WITH GRANT OPTION" | mysql -u root --password=root
	echo "GRANT PROXY ON ''@'' TO 'root'@'%' WITH GRANT OPTION" | mysql -u root --password=root
    echo "CREATE DATABASE phpipam_dev" | mysql -u root --password=root
}

cleanup() {

    service avahi-daemon restart
    service apache2 restart
    service ssh restart
    service mysql restart
    run-parts /etc/update-motd.d/ > /var/run/motd.dynamic
    
    echo "## We don't need these running right now"
    # These are here if we need them but for now, turn them off
    service postgresql stop
    service mongod stop
    service memcached stop
    service redis_6379 stop
    service beanstalkd stop
}

main 
exit 0
