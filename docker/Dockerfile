FROM ubuntu:14.04

ENV PHPIPAM_SOURCE https://github.com/explody/phpipam/archive/
ENV PHPIPAM_VERSION docker

RUN DEBIAN_FRONTEND=noninteractive apt-get update && apt-get -y install apache2 libapache2-mod-php5 php5-mysql vim curl php5-gmp php5-ldap php-pear && apt-get clean

ADD ${PHPIPAM_SOURCE}/${PHPIPAM_VERSION}.tar.gz /tmp/
RUN mkdir -p /var/www/ipam/
RUN rm -rf /var/www/ipam/*
RUN tar -xzf /tmp/${PHPIPAM_VERSION}.tar.gz -C /var/www/ipam/ --strip-components=1
COPY config/config.yml /var/www/ipam/config/config.yml
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

RUN a2enmod php5
RUN a2enmod rewrite

# apache env vars
ENV APACHE_LOCK_DIR /var/lock
ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2/
ENV APACHE_PID_FILE /var/apache.pid

EXPOSE 80
CMD ["/usr/sbin/apache2", "-D", "FOREGROUND"]
