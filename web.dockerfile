#FROM nginx:1.15.2

#ADD default.conf /etc/nginx/conf.d/default.conf

FROM nginx
RUN mkdir -p /etc/nginx/conf.d/
ADD ./default.conf /etc/nginx/conf.d/default.conf