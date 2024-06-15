---
title: "Preambula, the PHP FrontMatter and Markdown processor"
descr: "Preambula allows to quickly turn collection of Markdown files to fully functional Web-site."
meta:
   generator: "Preambula, the PHP Markdown processor"
---
# Preambula

Preambula is very simple and lightweight Markdown processor to display Markdown files as Web pages. You can write your Markdown notes in any editor, i.e. Obsidian, or even in `nano` via SSH connection, and the result will be displayed immediately on the Web site without any building or compilation steps. Preambula is written in PHP, so it can be run on any shared hosting.

## Features:

* Minimal requirements: PHP 7.1 or higher, mod_rewrite for Apache
* Fast page generation and low memory consumption (less than 2 Megabytes with PHP 8.2 on 64-bit platform)
* Multiple templates support with subtemplate inclusion
* SEO-friendly: Title and meta tags may be specified as FrontMatter in many formats: YAML, TOML, JSON
* OpenGraph markup support and automated OpenGraph meta-tags generation
* Correct Last-Modified header and HTTP 304 status if document is not changed since last download. Your site will appear the same way as if it was consisted of static files

## Documentation 

* [Installation and configuration](install.md)
* [Frontmatter data format](data.md)
* [Templates](templates.md)
* [Multidomain mode](multi.md)

## Author

Idea and programming: 4X_Pro <me@4xpro.ru>, site: [4xpro.ru](https://4xpro.ru), GitHub: [XXXXPro](https://github.com/XXXXPro), Telegram: [@XXXXPro](https://t.me/XXXXPro).