FROM php:8.3-cli

RUN docker-php-ext-install pdo pdo_mysql
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

WORKDIR /app

COPY . .

EXPOSE 8000

CMD [ "php", "-S", "0.0.0.0:8000", "-t" ,"public"]