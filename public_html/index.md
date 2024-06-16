---
title: "Hello world page"
template: "default.html"
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
![Preambula logo](https://preambula.4xpro.ru/logo.png)
# Hello world!

If you see this, everything works well!

This is simple Markdown page processed with [Preambula](https://preambula.4xpro.ru) script.

Refer to [Markdownguide.Org](ttps://www.markdownguide.org/basic-syntax/) to learn more Markdown syntax.

