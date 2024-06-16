---
title: "Preambula configuration file"
descr: "It is named config.php and located at root project directory"
rel:
  "next": "data.md"
  "prev": "install.md"
---
# Preambula configuration file

Preambula configuration file name is `config.php`. It is located in root directory of Preambula installation. It is simple PHP file where associative array $preambula_settings is defined. The array may have following keys:

* `data_dir` — directory where Markdown files stored. Can be absolute path or relative to directory where _handler.php is placed (i.e. public_html). Default value is `.`, don't change this if you do not know what are you doing.
* `index_file` — file to display if directory address is requested. Should match `DirectoryIndex` directive (for Apache) or `index` (for Nginx) in web server configuration. Multiple index names are not supported for now. Default value is `index.md`.
* `base_url` —  URL of Preambula data directory. This part will be removed from requesting URL when Preambula is looking for requested file. Default value is `/`. Change this if Preambula is installed in subdirectory of your site.
* `description_legth` — maximum length of automatically generated description. Default value is `240`.
* `debug` — debug mode enables display_errors to browser, disables cache and ouputs page generation time and memory consumption to {{ debug }} clause in template. Disabled by default.
* `nocache` — if enabled, turns off caching and prohibits the output of status 304 No Changes. Disabled by default.
* `cache_control` — value of Cache-Control header (used only if `nocache` is set to false). Default value is `public, max-age=1400`.  
* `defaults` — default values each page to apply if no such value specified in FrontMatter of requested page. Must be associative array. Some values are described in ["FrontMatter Data" section](data.md).
* `opengraph_auto` — when enabled, OpenGraph metatags will be generated automatically if missing. Disabled by default.
* `github_mode` — if enabled, Markdown will be parsed with GitHub-specific options. Disabled by default.

<p style="display:block; text-align: center"><a href="/">Back to main page</a></p>
