FROM alpine:3.5

RUN apk --update --no-cache add \
    nginx \
    php5 \
    php5-fpm \
    php5-mysql \
    supervisor && \
    rm -rf /var/cache/apk/* /var/www/* /tmp/*

COPY config/php-fpm.conf /etc/php5/php-fpm.conf

COPY config/nginx.conf /etc/nginx/nginx.conf
COPY config/site.conf /etc/nginx/conf.d/default.conf

COPY config/supervisor /etc

COPY senaculum /var/www/senaculum

RUN mkdir -p /var/log/supervisord

EXPOSE 80
WORKDIR /var/www

RUN chown -R nginx /var/lib/nginx /var/log/nginx /var/www

CMD [ "/usr/bin/supervisord", "-c", "/etc/supervisord.conf" ]
