---
title: "Installation of the Preambula "
descr: "Installation process of the Preambula is rather simple and straightforward both for Apache and Nginx servers" 
rel:
  next: "config.md"
---
# Installation

There are two ways to install Preambula. The first way is traditional: just [download ZIP archive](/donwload/preambula.zip) and extract it on your server. The archive already contains all necessary files. The other way is to clone GitHub repository and then run `composer` to install dependencies:

    git clone https://github.com/XXXXPro/Preambula
    cd Preambula
    composer install

Then copy `config.sample.php` file to `config.php` and edit the latter according your needs.

## Apache

If `AllowOverride` directive is enabled, no futher configuration required. Just specify `public_html` subdirectory of Preambula as DocumentRoot in the `VirtualHost` section of site configuration file (i.e. if you install Preambula in /var/www/preambula, then you should put `DocumentRoot "/var/www/preambula/public_html"`), put there your Markdown files and enjoy. If you don't want to enable .htaccess file, copy all directives from public_html/.htaccess to appropriate `VirtualHost` section of Apache configuration file. And don't forget to enable `mod_rewrite`!

## Nginx 

You need to specify Nginx to process .md-files with _handler.php. To do so, add to your site configuration file (the block where `server_name` directive is located) something like this:

    index index.md;
    location ~ [^/]\.md(/|$) {
        root /path/to/preambula/public_html; # replace with path to your Preambula public_html subdirectory
        fastcgi_param SCRIPT_FILENAME $document_root/_handler.php; 
        try_files $uri =404;
        fastcgi_pass    unix:/path/to/your/php-fpm-socket.sock;  # replace with path to your php-fpm socket
        fastcgi_index   index.md;
        include         /etc/nginx/fastcgi_params;
    }

<p style="display:block; text-align: center"><a href="/">Back to main page</a></p>