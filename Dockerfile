FROM php:8.3.1-cli AS builder

RUN apt update && apt install -y build-essential unixodbc-dev gnupg \
    && pecl install sqlsrv pdo_sqlsrv apcu \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv apcu

# Final image
FROM php:8.3.1-apache

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

# Install ODBC Drivers withou compilation dependencies
RUN apt-get update && apt-get install -y --no-install-recommends gpg curl \
    && curl -fsSL https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor -o /usr/share/keyrings/microsoft-prod.gpg \
    && curl https://packages.microsoft.com/config/debian/12/prod.list | tee /etc/apt/sources.list.d/mssql-release.list \
    && apt-get update \
    && ACCEPT_EULA=Y apt-get install -y --no-install-recommends msodbcsql18 \
    && rm -rf /var/lib/apt/lists/*

# Copy compiled drivers from builder stage
COPY --from=builder /usr/local/lib/php/extensions/no-debug-non-zts-20230831/sqlsrv.so /usr/local/lib/php/extensions/no-debug-non-zts-20230831/
COPY --from=builder /usr/local/lib/php/extensions/no-debug-non-zts-20230831/pdo_sqlsrv.so /usr/local/lib/php/extensions/no-debug-non-zts-20230831/
COPY --from=builder /usr/local/lib/php/extensions/no-debug-non-zts-20230831/apcu.so /usr/local/lib/php/extensions/no-debug-non-zts-20230831/
COPY --from=builder /usr/local/etc/php/conf.d/docker-php-ext-sqlsrv.ini /usr/local/etc/php/conf.d/
COPY --from=builder /usr/local/etc/php/conf.d/docker-php-ext-pdo_sqlsrv.ini /usr/local/etc/php/conf.d/
COPY --from=builder /usr/local/etc/php/conf.d/docker-php-ext-apcu.ini /usr/local/etc/php/conf.d/

COPY /src /var/www/html