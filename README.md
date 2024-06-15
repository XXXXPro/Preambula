# Preambula

Preambula is very simple and lightweight Markdown processor to display Markdown files as Web-pages. You can write your Markdown notes in any editor, i.e. Obsidian, or even in `nano` via SSH connection, and the result will be displayed immediately on the Web site without any building or compilation steps. Preambula is written in PHP, so it can be run on almost any shared hosting.

## Features:

* Minimal requirements: PHP 7.1 or higher, mod_rewrite for Apache
* Fast page generation and low memory consumption (less than 2 Megabytes on 64-bit platform)
* Multiple templates support with subtemplate inclusion
* SEO-friendly: Title and meta tags may be specified as FrontMatter in many formats: YAML, TOML, JSON.
* OpenGraph markup support and automated OpenGraph meta-tags generation
* Correct Last-Modified header and HTTP 304 status if document is not changed since last download. Your site will appear the same way as if it was consisted of static files.

## Installation

There are two ways to install Preambula. The first way is traditional: just download ZIP archive from Releases section and extract it on your server. The archive already contains all necessary files. The other way is to clone GitHub repository and then run `composer` to install dependencies:

    git clone https://github.com/XXXXPro/Preambula
    cd Preambula
    composer install

Then copy `config.sample.php` file to `config.php` and edit the latter according your needs.

### Apache

If `AllowOverride` directive is enabled, no futher configuration required. Just specify `public_html` subdirectory of Preambula as DocumentRoot in the site config file (i.e. if you install Preambula in /var/www/preambula, then you should put `DocumentRoot "/var/www/preambula/public_html"` to your site configuration file), put there your Markdown files and enjoy. If you don't want to enable .htaccess file, copy all directives from public_html/.htaccess to httpd.conf or site configuration file. And don't forget to enable mod_rewrite!

### Nginx 

You need to specify Nginx to process .md-files with _handler.php. To do so, add to your site configuration file something like this:

    index index.md;
    location ~ [^/]\.md(/|$) {
        root /path/to/preambula/public_html; # replace with path to your Preambula public_html subdirectory
        fastcgi_param SCRIPT_FILENAME $document_root/_handler.php; 
        try_files $uri =404;
        fastcgi_pass    unix:/path/to/your/php-fpm-socket.sock  # replace with path to your php-fpm socker
        fastcgi_index   index.md;
        include         /etc/nginx/fastcgi_params;
    }

## Markdown and FrontMatter

Frontmatter is just Markdown file with some metadata at beginning separated with ---. Metadata can be in YAML, TOML or JSON format.
Preambula just converts Markdown files to HTML on the fly, so site structure will be the same as structure of your files in public_html directory. Just like good old static files, but with templating and much less to type than HTML!

Example:
```
---
title: "Hello world page"
template: "simple.html"
meta:
      description: "This is first page created with Preambula."
      generator: "Preambula, the PHP Markdown processor"
      "og:title": "Hello world page build with Preambula"
      "og:description": "If key contains colon (:), the metatag wil have property attribute instead of name attribute"
rel:
      next: "page2.md"
somevar: "Use {{ somevar }} to output this in your HTML template"
othervar:
      subvar: "Use {{ othervar.subvar }} to print this in template"
      second: "And {{ othervar.second }} for this"
---
# Hello world!

This is simple Markdown page!
Refer to [Markdownguide.Org](ttps://www.markdownguide.org/basic-syntax/) to learn more Markdown syntax.   
```
The most important variables are:

* `title` — will be displayed as page title tag.
* `meta` — all subkeys of this variable will become the meta tags in the resulting page. If subkey has colon (:) in its name, the meta tag will have "property" attribute, otherwise it get "name" attribute. This is convinient to specify both common metatags and OpenGraph attributes.
* `template` — the name of template file to use. By default, the template path should be relative to `templates` subdirectory of Preambula root dir, but templates location can be changed in configuration file.
* `descr` — it is just shortcut for meta.description.
* `rel` — all subkeys of this tag will be converted to link tags with specified rel attribute.   

## Templates 

By default, templates are located in the `templates` subdirectory, but this can be overridden in configuration file. They are simple HTML files with some directives inside:

* `{% include filename.html %}` — puts the contents of filename.html (note: there is no quotes around filename). Filename can contain path that should be relative to templates directory.
* `{% include_relative filename.html %}` — the same as former, but file path is relative to directory where current template is located.
* `{{ content }}` — inserts rendered Markdown content.
* `{{ meta }}` — generates meta-tags based on `meta` data in FrontMatter.
* `{{ rel }}` — grenerates link-tags with rel attributes based on `rel` data in FrontMatter.
* `{{ debug }}` — inserts debug information about page generation time and memory consumption.
* `{{ title }}` — outputs page title.
* `{{ somevar }}` — outputs value of somevar variable from Frontmatter data. For complex data types, use `{{ othervar.subvar }}` syntax, not `othervar['subvar']`.

The Preambula intended to be as simple and lightweight as possible, so none of loops or ifs are supported.

