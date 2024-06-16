---
title: "Preambula multisite mode"
descr: "To use Preambula in multisite mode, just create and configure data directory for each domain"
rel:
  prev: "templates.md"
---
# Multisite mode

Single copy of Preambula can be used to serve multiple sites at same time. To use Preambula in multisite mode do following:

* create subdirectories matching domain names in Preambula root directory (i.e. /path/to/preambula/domain1.example.com, /path/to/preambula/domain2.example.com and so on)
* copy files `.htaccess` and `_handler.php` from `public_html` to each created directory
* specify created directories as `DocumentRoot` (in VirtualHost sections of Apache config file) or just `root` (in "server" blocks of nginx config)
* put your Markdown files in appropriate directories for each site.

If you need specific config for one or more of this sites, copy `config.php` file to domain.name.config.php (i.e. domain1.example.com.config.php) and change the name of config file in `_handler.php` (string `require __DIR__.'/../config.php';`) in appropriate directory.

<p style="display:block; text-align: center"><a href="/">Back to main page</a></p>