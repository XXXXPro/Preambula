<?php
/** ================================
 *  @package Preambula
 *  @author 4X_Pro <me@4xpro.ru>
 *  @version 0.90b
 *  @copyright 2024, 4X_Pro
 *  @url https://preambula.4xpro.ru
 * 
 *  Sample config file for Preambula Markdown Processor
 *  ================================ */

 // Copy this file to config.php and change according your needs

$preambula_settings = array(
  // Directory where Markdown files are stored. Can be absolute path or relative to directory where _handler.php is placed (i.e. public_html)
  'data_dir'=>'.',
  // File to display if directory address is requested. Should match DirectoryIndex directive in web server configuration. Multiple index names are not supported for now
  'index_file'=>'index.md',
  // The root path o
  'base_url'=>'/',
  // Debug mode enables display_errors to browser, disables cache and ouputs page generation time and memory consumption to {{ debug }} clause in template
  'debug'=>false,
  // Disables cache and HTTP status 304 output
  'nocache'=>false,
  // Value of Cache-Control header (only if nocache is false)
  'cache_control'=>'public, max-age=1400',  
  // Default values each page to use if no such value specified in FrontMatter of requested page
  'defaults'=>array(
    // lang is used in "lang" attribute <html> tag
    'lang'=>'en',
    // default template to wrap Markdown code
    'template'=>'default.html'
  ),
  // Should be OpenGraph metatags generated automatically. 
  'opengraph_auto'=>false,
  // If enabled, class \cebe\markdown\GithubMarkdown will be used for parsing instead of \cebe\markdown\Markdown
  'github_mode'=>false
);