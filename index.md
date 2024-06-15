---
title: "Preambula, the PHP FrontMatter and Markdown processor"
descr: "Preambula allows to quickly turn collection of Markdown files to fully functional Web-site."
---
# Preambula

Preambula is very simple and lighweight Markdown processor to display Markdown files as Web-pages. You can write your Markdown notes in any editor, i.e. Obsidian, or even in `nano` via SSH connection, and the result will be displayed immediately on the Web-site without any building or compilation steps. Preambula is written in PHP, so it can be run on any shared hosting.

## Features:

* Minimal requirements: PHP 7.1 or higher, mod_rewrite for Apache
* Fast page generation and low memory consumption (less than 2 Megabytes on 64-bit platform)
* Multiple templates support with subtemplate inclusion
* SEO-friendly: Title and meta tags may be specified as FrontMatter in many formats: YAML, TOML, JSON.
* OpenGraph markup support and automated OpenGraph meta-tags generation
* Correct Last-Modified header and HTTP 304 status if document is not changed since last download. Your site will appear the same way as if it was consisted of static files.

## Installation

There are two ways to install Preambula. The first way is traditional: just download ZIP archive and extract it on your server. The archive already contains all necessary files. The other way is to clone GitHub repository and then run `composer` to install dependencies:

`
git clone https://github.com/XXXXPro/Preambula
composer install
`

Then copy `config.sample.php` file to `config.php` and edit the latter according your needs.

### Apache

If `AllowOverride` directive is enabled, no futher configuration required. Just specify `public_html` subdirectory of Preambula as DocumentRoot in the site config file (i.e. if you install Preambula in /var/www/preambula, then you should put `DocumentRoot "/var/www/preambula/public_html"` to your site configuration file), put there your Markdown files and enjoy. If you don't want to enable .htaccess file, copy all directives from public_html/.htaccess to httpd.conf or site configuration file. And don't forget to enable mod_rewrite!

