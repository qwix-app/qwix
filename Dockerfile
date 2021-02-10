# pull from PHP image
FROM php:7.4

# set workdir path
WORKDIR /app

# install and enable PDO with MySQL
RUN docker-php-ext-install pdo pdo_mysql \
    && docker-php-ext-enable pdo_mysql

# bind volume
VOLUME ["/app"]

# expose port 8000
EXPOSE 8000

# serve application
CMD ["php", "-S", "0.0.0.0:8000", "-t", "/app/public"]