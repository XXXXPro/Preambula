---
title: "Markdown and FrontMatter data formats"
descr: ""
rel:
  next: "templates.md"
  prev: "config.md"
---
# Markdown and FrontMatter

Markdown is a lightweight markup language for creating formatted text using any plain-text editor (the author of Preambula recommends to use [Obsidian](https://obsidian.md/)). Refer to [Markdownguide.Org](ttps://www.markdownguide.org/basic-syntax/) to learn more Markdown syntax. Commonly Markdown files have .md extension. Frontmatter is just Markdown file with some metadata at beginning separated with --- from main text. Metadata can be written in YAML, TOML or JSON format.

Preambula just converts Markdown files to HTML on the fly, so the site structure will be the same as the structure of your files in public_html directory. Just like good old static files sites, but with templating and much less to type than HTML!

Sample FrontMatter file:

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

The most important variables are:

* `title` — will be displayed as page title tag. If no title specified, it will be generated automatically from first heading tag. 
* `meta` — all subkeys of this variable will become the meta tags in the resulting page. If subkey has colon (:) in its name, the meta tag will have "property" attribute, otherwise it get "name" attribute. This is convinient to specify both common metatags and OpenGraph attributes.
* `template` — the name of [template file](templates.md) to use. By default, the template path should be relative to `templates` subdirectory of Preambula root dir, but templates location can be changed in [configuration file](config.md).
* `descr` — it is just shortcut for meta.description. If none of descr and meta.description specified, the beginning of first non-heading text will be used as description.
* `rel` — all subkeys of this tag will be converted to link tags with specified rel attribute.
* `lang` — language code for `lang` attribute of root `html` tag.

<p style="display:block; text-align: center"><a href="/">Back to main page</a></p>