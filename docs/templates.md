---
title: "Templates in Preambula Markdown Processor"
descr: "List of directives to use in template files"
---

# Templates 

By default, templates are located in the `templates` subdirectory, but this can be overridden in [configuration file](config.md). They are simple HTML files with some directives inside:

* `{% include filename.html %}` — puts the contents of filename.html (note: there is no quotes around filename). Filename can contain path that should be relative to templates directory.
* `{% include_relative filename.html %}` — the same as former, but file path is relative to directory where current template is located.
* `{{ content }}` — inserts rendered Markdown content.
* `{{ meta }}` — generates meta-tags based on `meta` data in FrontMatter.
* `{{ rel }}` — grenerates link-tags with rel attributes based on `rel` data in FrontMatter.
* `{{ debug }}` — inserts debug information about page generation time and memory consumption.
* `{{ title }}` — outputs page title.
* `{{ somevar }}` — outputs value of somevar variable from Frontmatter data. For complex data types, use `{{ othervar.subvar }}` syntax, not `othervar['subvar']`.

The Preambula intended to be as simple and lightweight as possible, so none of loops or ifs are supported.

<p style="display:block; text-align: center"><a href="/">Back to main page</a></p>