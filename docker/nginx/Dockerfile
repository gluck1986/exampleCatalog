FROM nginx:stable

COPY ./*.template /etc/nginx/templates/
COPY ./nginx.conf /etc/nginx/nginx.conf
COPY ./nginx-cors/ /etc/nginx/nginx-cors/

# forward request and error logs to docker log collector
RUN ln -sf /dev/stdout /var/log/nginx/access.log \
  && ln -sf /dev/stderr /var/log/nginx/error.log