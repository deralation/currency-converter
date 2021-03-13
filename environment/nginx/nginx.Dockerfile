FROM nginx

# Create these bunch of folders which we're going to use for the upcoming configuration:
RUN mkdir -p /etc/nginx/logs \
	&& mkdir -p /etc/nginx/sites-available \
 	&& mkdir -p /etc/nginx/sites-enabled

RUN mkdir -p /var/www

# Remove the current nginx.conf
RUN rm /etc/nginx/nginx.conf

# Create own nginx.conf
COPY nginx.conf /etc/nginx/nginx.conf

# Create own nginx.conf.default
COPY nginx.conf.default  /etc/nginx/nginx.conf.default

# Create own site available
COPY local.currency-converter.com  /etc/nginx/sites-available/local.currency-converter.com

# Now you need to symlink the virtual hosts that you want to enable into the sites-enabled folder:
RUN ln -sfv /etc/nginx/sites-available/local.currency-converter.com  /etc/nginx/sites-enabled/local.currency-converter.com