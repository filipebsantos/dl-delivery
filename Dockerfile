FROM php:8.3.19-cli AS builder

RUN find /etc/apt/ -type f -exec sed -i 's|http://deb.debian.org|https://deb.debian.org|g' {} \; && \
    find /etc/apt/ -type f -exec sed -i 's|http://security.debian.org|https://security.debian.org|g' {} \; && \
    apt-get update && \
    apt-get install -y build-essential unixodbc-dev libzip-dev gnupg && \
    docker-php-ext-install zip && \
    pecl install sqlsrv pdo_sqlsrv apcu && \
    docker-php-ext-enable sqlsrv pdo_sqlsrv apcu zip

# Final image
FROM php:8.3.19-apache-bookworm

ARG appName="DL Delivery"
ARG appVersion="2.0.0"
ENV APP_NAME=${appName}
ENV APP_VERSION=${appVersion}

LABEL "br.dev.filipebezerra.product"="${APP_NAME}"
LABEL "br.dev.filipebezerra.version"="${APP_VERSION}"
LABEL "org.opencontainers.image.title"="${APP_NAME}"
LABEL "org.opencontainers.image.version"="${APP_VERSION}"
LABEL "org.opencontainers.image.authors"="Filipe Bezerra :: https://filipebezerra.dev.br"
LABEL "org.opencontainers.image.description"="${APP_NAME} is an auxiliary delivery system developed for Drogaria Litorânea Inc. Due to poor quality map information in some countryside cities, this software is used as a GPS coordinates database for clients, improving delivery times."
LABEL "org.opencontainers.image.ref.name"="php:8.3.1-apache"

# Força HTTPS nos repositórios
RUN find /etc/apt/ -type f -exec sed -i 's|http://deb.debian.org|https://deb.debian.org|g' {} \; && \
    find /etc/apt/ -type f -exec sed -i 's|http://security.debian.org|https://security.debian.org|g' {} \;

# Instala ODBC drivers da Microsoft
RUN apt-get update && apt-get install -y --no-install-recommends \
    ca-certificates \
    curl \
    gpg \
    libzip4 \
    unzip \
    && curl -fsSL https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor -o /usr/share/keyrings/microsoft-prod.gpg \
    && curl https://packages.microsoft.com/config/debian/12/prod.list | tee /etc/apt/sources.list.d/mssql-release.list \
    && apt-get update \
    && ACCEPT_EULA=Y apt-get install -y --no-install-recommends msodbcsql18 \
    && rm -rf /var/lib/apt/lists/*

# Instala Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

# Copia extensões PHP compiladas
COPY --from=builder /usr/local/lib/php/extensions/no-debug-non-zts-20230831/*.so /usr/local/lib/php/extensions/no-debug-non-zts-20230831/
COPY --from=builder /usr/local/etc/php/conf.d/* /usr/local/etc/php/conf.d/

RUN a2enmod rewrite headers
COPY ./conf/apache-vhost.conf /etc/apache2/sites-available/000-default.conf

# Código da aplicação
COPY /src /var/www/html