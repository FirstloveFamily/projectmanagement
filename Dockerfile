# Base Image with PHP 8.4 FPM
FROM php:8.4-fpm

# 1. & 2. Install System Dependencies & SQL Server Drivers & Timezone
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    zip \
    unzip \
    libicu-dev \
    freetds-dev \
    freetds-bin \
    tdsodbc \
    locales \
    gnupg2 \
    curl \
    git \
    tzdata \
    unixodbc-dev \
    && curl -fsSL https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor -o /usr/share/keyrings/microsoft-prod.gpg \
    && curl -fsSL https://packages.microsoft.com/config/debian/12/prod.list | tee /etc/apt/sources.list.d/mssql-release.list \
    && apt-get update \
    && ACCEPT_EULA=Y apt-get install -y msodbcsql18 \
    && ln -snf /usr/share/zoneinfo/Asia/Bangkok /etc/localtime && echo Asia/Bangkok > /etc/timezone \
    && sed -i '/th_TH.UTF-8 UTF-8/s/^# //g' /etc/locale.gen && locale-gen \
    && rm -rf /var/lib/apt/lists/*

ENV LANG=th_TH.UTF-8 \
    LANGUAGE=th_TH:th \
    LC_ALL=th_TH.UTF-8 \
    TZ=Asia/Bangkok

# 3. Configure FreeTDS for SQL Server
RUN sed -i 's/^.*tds version =.*/#tds version = auto/g' /etc/freetds/freetds.conf && \
    sed -i '/\[global\]/a \        tds version = 7.0\n\tclient charset = UTF-8\n\ttext size = 20971520' /etc/freetds/freetds.conf

# 4. PHP Extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo_pgsql \
    pdo_dblib \
    pcntl \
    bcmath \
    intl \
    zip \
    opcache \
    gd \
    && pecl install sqlsrv pdo_sqlsrv \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv

# 5. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Install Node.js (LTS)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm@latest

# 7. Install Bun
RUN curl -fsSL https://bun.sh/install | bash
ENV BUN_INSTALL="/root/.bun"
ENV PATH="$BUN_INSTALL/bin:$PATH"

RUN groupadd -g 1000 vcst && \
    useradd -u 1000 -g vcst -m vcst

# 8. Set Working Directory
WORKDIR /usr/share/nginx/html/${FOLDER_PROJECT_NAME}

# 9. Fix OpenSSL security level for old SQL Server versions
COPY openssl.cnf /etc/ssl/openssl.cnf
ENV OPENSSL_CONF=/etc/ssl/openssl.cnf

# 10. Custom PHP Configuration
COPY php.ini /usr/local/etc/php/conf.d/docker-php.ini
COPY zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf
    
# Entry point will be FPM by default
EXPOSE 9000
CMD ["php-fpm"]
