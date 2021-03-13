server {
    listen       80;
    server_name  local.currency-converter.com;
    root         /var/www/html/currency-converter;

    client_header_timeout 10m;
    client_body_timeout 10m;
    client_body_buffer_size 256m;
    client_header_buffer_size 12m;
    client_max_body_size 128m;
    fastcgi_buffers 8 128k;
    fastcgi_buffer_size 128k;
    access_log  /etc/nginx/logs/access.log;
    error_log /etc/nginx/logs/error.log debug; 

    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        
        fastcgi_pass php:9000;
        fastcgi_index index.php;
        fastcgi_read_timeout 600;
        
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;

        fastcgi_param PATH_INFO $fastcgi_path_info;
        include fastcgi_params;
    }

    error_page  404     /404.html;
    error_page  403     /403.html;
}