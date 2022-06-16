FROM php:8.1-apache
WORKDIR /var/www/html

ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN a2enmod rewrite && service apache2 restart && apachectl -t -D DUMP_MODULES | grep rewrite

RUN echo 'deb [trusted=yes] https://repo.symfony.com/apt/ /' | tee /etc/apt/sources.list.d/symfony-cli.list && apt update && apt install -y symfony-cli

RUN apt install -y nodejs npm zip

RUN curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php && php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer