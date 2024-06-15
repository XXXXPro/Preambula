<?php
/** ================================
 *  @package Preambula
 *  @author 4X_Pro <me@4xpro.ru>
 *  @version 0.90b
 *  @copyright 2024, 4X_Pro
 *  @url https://preambula.4xpro.ru
 * 
 *  Simple and lighweight MarkDown and FrontMatter files processor
 *  ================================ */

require_once __DIR__."/vendor/autoload.php";

// Exceptions to return error HTTP codes
class Exception401 extends Exception {}
class Exception403 extends Exception {}
class Exception404 extends Exception {}
class Exception500 extends Exception {}

class Preambula {
  protected array $config;
  protected int $lastmod = 0;
  protected array $included = array();

  public function __construct(array $settings) {
    $default_settings = array(
      'charset'=>'utf-8',
      'data_dir'=>'.',
      'base_url'=>'/',
      'index_file'=>'index.md',
      'template_dir'=>__DIR__.'/template',
      'defaults'=>array(
        'template'=>'default.html',
        'lang'=>'en'
      ),
      'opengraph_auto'=>false,
      'github_mode'=>false,
      'nocache'=>false,
      'debug'=>false
    );
    $this->config = array_replace_recursive($default_settings,$settings); // merging defaults and passed settings
    if ($this->config['debug']) ini_set('display_errors',true); // enabling output errors to browser in debug mode
  }

  private function setLastMod(string $filename):void {
    $this->lastmod = max($this->lastmod,filemtime($filename));
  }

  private function loadFile(string $filename):string {
    $datadir = $this->config['data_dir'];
    if (!$this->isValidFile($filename)) throw new Exception403('Filename contains invalid symbols!');
    $fullname = $datadir.'/'.$filename;
    if (is_dir($fullname)) $fullname=$fullname.$this->config['index_file'];
    if (!file_exists($fullname)) throw new Exception404('Markdown file '.$filename.' not found!');
    if (!is_readable($fullname)) throw new Exception403('Markdown file is not readable!');
    $this->setLastMod($fullname);
    return file_get_contents($fullname);    
  }

  protected function parse(string $fileContent):array {
    /// Using Webuni-- universal Frontmatter parser that supports YAML, TOML and other formats
    $frontMatter = \Webuni\FrontMatter\FrontMatterChain::create();
    $document = $frontMatter->parse($fileContent);
    
    $metadata = array_merge($this->config['defaults'],$document->getData()); // missing metadata are obtained from config['defaults']
    $content = $document->getContent();
    
    // Using cebe/markdown parser to process 
    if ($this->config['github_mode']) $mdparser = new \cebe\markdown\GithubMarkdown();
    else $mdparser = new \cebe\markdown\Markdown();
    $content = $mdparser->parse($content); 

    return array($metadata,$content);
  }

  protected function prepareTemplate(string $template):string {
    $template_dir = $this->config['template_dir'];
    $fullname = $template_dir.'/'.$template;

    if (in_array($fullname,$this->included)) throw new Exception500("Recursive inclusion of template ".htmlspecialchars($template)."!");
    $this->included[]=$fullname;

    if (!file_exists($fullname)) throw new Exception404("Template file ".htmlspecialchars($template)." not found!");
    if (!is_readable($fullname)) throw new Exception403('Template file '.htmlspecialchars($template).' is not readable!');
    $this->setLastMod($fullname);
    $templateData = file_get_contents($fullname);    
    
    // include directive processing
    $match_count = preg_match_all('|{%\s*include(_relative)?\s+([\S]+?)\s*%}|u',$templateData,$matches);
    for ($i=0; $i<$match_count; $i++) {
      if (!empty($matches[1][$i])) $included_name = dirname($template).'/'.$matches[2][$i];
      else $included_name = $matches[2][$i];
      $innerTemplate = $this->prepareTemplate($included_name);
      $templateData = str_replace($matches[0][$i],$innerTemplate,$templateData);
    }

    return $templateData;
  }

  protected function setMissingData(array $data, string $text):array {
    // Building title if missing
    if (empty($data['title'])) $data['title']=$this->buildTitle($text);    

    // build description
    if (empty($data['meta']['description']) && !empty($data['descr'])) $data['meta']['description']=$data['descr'];
    if (empty($data['meta']['description']) && !empty($data['meta']['og:description'])) $data['meta']['description']=$data['meta']['og:description'];
    if (empty($data['meta']['description'])) $data['meta']['description']=$this->buildDescr($text);

    // If automatic generation of OpenGraph data enabled
    if ($this->config['opengraph_auto']) {
      if (empty($data['meta']['og:title'])) $data['meta']['og:title']=$data['title'];
      if (empty($data['meta']['og:description'])) $data['meta']['og:description']=$data['meta']['description'];
      if (empty($data['meta']['og:type'])) $data['meta']['og:type']='article';
      if (empty($data['meta']['og:url']) && !empty($data['rel']) && !empty($data['rel']['canonical'])) $data['meta']['og:url']=$data['rel']['canonical'];
      if (empty($data['meta']['og:image'])) {
        if (preg_match('|<img[^>]+src=["\'](\S+?)["\'][^>]*>|us',$text,$match)) {  // finding first image tag in content
          $data['meta']['og:image']=$match[1];
        }
      }
      if (!empty($data['site_name'])) $data['meta']['og:site_name']=$data['site_name'];
    }
    return $data;
  }

  protected function buildMeta(array $data):string {
    $result = '';
    foreach ($data as $key=>$value) {
      if (substr($key,0,3)==='og:' || substr($key,0,8)==='twitter:') $propname = 'property';
      else $propname='name';
      $result.='    <meta '.$propname.'="'.htmlspecialchars($key).'" content="'.htmlspecialchars($value).'">'.PHP_EOL;
    }
    return $result;
  }

  protected function buildRel(array $data):string {
    $result = '';
    foreach ($data as $key=>$value) $result.='    <link rel="'.htmlspecialchars($key).'" href="'.htmlspecialchars($value).'">'.PHP_EOL;
    return $result;
  }

  protected function buildTitle(string $text):string {
    for ($i=1; $i<=6; $i++) {
      if (preg_match('|<h'.$i.'[^>]*?>(.*?)</h'.$i.'>|us',$text,$match)) return $this->cutText(strip_tags($match[1]),60);
    }
    return 'Untitled page';
  }

  protected function buildDescr(string $text):string {
    for ($i=1; $i<=6; $i++) {
      if (preg_match('|<h'.$i.'[^>]*?>(.*?)</h'.$i.'>|us',$text,$match)) $text = str_replace($match[0],'',$text);
    }
    return str_replace("\n"," ",$this->cutText(strip_tags($text),240));
  }

  protected function cutText(string $text, int $maxlength):string {
    $maxlength = min($maxlength,mb_strlen($text));
    $space_pos = $maxlength;
    $spaces = array(' ',' ');
    $punctuation = array('.',',',':',';','!','?','-','—');
    for ($i=$maxlength-1; $i>=0; $i--) { // walking string from maxlength to start until first punctuation mark found
      $char = mb_substr($text,$space_pos,1);
      if ($space_pos===$maxlength && in_array($char,$spaces)) $space_pos=$i; // when first space occured, memorizing its position in case if no punctuations present
      if (in_array($char,$punctuation)) return mb_substr($text,0,$i);  
    }
    return mb_substr($text,0,$space_pos); // if no punctuation found, return substring before last space. If there is no spaces, max specified number of characters will be returned
  }

  protected function isValidFile(string $filename):bool {
    $result = (substr($filename, 0, 1) != '/' && substr($filename, 0, 1) != '\\' && substr($filename, 0, 1) != '~');
    if ($result) {
      $test = array('..', '://', '`', '\'', '"', ':', ';', ',', '&', '>', '<');
      for ($i = 0, $count = count($test); $i < $count && $result; $i++)
        $result = (strpos($filename, $test[$i]) === false);
    }
    return $result;
  }  

  protected function getVariable(array $var_name, array $data):string {
    if (is_string($var_name)) $var_name = explode('.',$var_name);
    $first_part = array_shift($var_name);
    if (count($var_name)===0 && isset($data[$first_part])) return (string)$data[$first_part];
    elseif (isset($data[$first_part])) return $this->getVariable($var_name,$data[$first_part]);
    else return '';
  }
  
  protected function fillTemplate(array $data, string $template):string {
    $cur_pos = 0;
    $result = '';
    $open = '{{';
    $open_len = strlen($open);
    $close = '}}';
    $close_len = strlen($close);
    $open_pos=strpos($template,$open,$cur_pos);
    while ($open_pos!==false) {
      $close_pos = strpos($template,$close,$open_pos);
      $next_pos = strpos($template,$open,$open_pos+$open_len);
      if ($close_pos===false || ($next_pos!==false && $close_pos>$next_pos)) {
        $line_number = substr_count(substr($template,0,$open_pos),"\n");
        throw new Exception500("Template syntax error: no matching closing brackets at line ".$line_number+1);
      }
      $var_name = trim(substr($template,$open_pos+$open_len,$close_pos-$open_pos-$open_len));
      $result.=substr($template,$cur_pos,$open_pos-$cur_pos);
      $result.=$this->getVariable(explode('.',$var_name),$data);
      $cur_pos=$close_pos+$close_len;
      $open_pos = $next_pos;
    }
    $result.= substr($template,$cur_pos); // adding last part ot template
    return $result;
  }

  /** Processes specified Markdown file:  
   * extracts FrontMatter data, 
   * loads HTML template specified in FrontMatter or in config.php, 
   * converts Markdown to HTML,
   * puts result of conversion into the template (replacing the {{ content }} clause), 
   * build meta and link rel tags,
   * fills {{ variable }} clauses in template with FrontMatter data,
   * returns the resulting HTML document as string.
   * 
   * @param string $filename — Name of Markdown file to process, relative to config['document_dir']
   * @return string HTML document as result of Markdown processing
   */
  public function process(string $filename):string {
    $starttime = microtime(true);
    $fileContent = $this->loadFile($filename);
    if (empty($fileContent)) return '';

    list($data,$content) = $this->parse($fileContent);
    $data = $this->setMissingData($data,$content);
    
    $template = $this->prepareTemplate($data['template']);
    $data['meta']=isset($data['meta']) && is_array($data['meta']) ? $this->buildMeta($data['meta']) : '';
    $data['rel']=isset($data['rel']) && is_array($data['rel']) ? $this->buildRel($data['rel']) : '';

    if ($this->config['debug']) {
      $data['debug']=sprintf("Exec time: %.3f, memory usage: %d Kb, %d Kb",
        (microtime(true)-$starttime),ceil(memory_get_peak_usage()/1024),ceil(memory_get_peak_usage(true)/1024));
    }
    $data['content']=$content;

    return $this->fillTemplate($data,$template);
  }

  /** Processes and outputs requested Markdown file with all necessary HTTP headers,
   * i.e. Content-Type, Content-Length, Last-Modified and Cache-Control.
   * If requested URL starts with base_url specified in config.php, the base_url will be removed
   * If If-Modified-Since request header present, document and template have no changes 
   * and no debug or nocache modes enabled in config, outputs 304 Not changed status.
   * 
   * @param string $url 
   */
  public function display(string $url):void {
    ob_start();
    try {
      $baseurl = $this->config['base_url'];
      if ($baseurl==='') $baseurl='/';
      $len = strlen($baseurl);
      if (substr($url,0,$len)===$baseurl) {
        $url = substr($url,$len);
      }
      $filename = $url;
      $result = $this->process($filename);
      $return304 = false;
      if (empty($this->config['debug']) && empty($this->config['nocache']) && isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) { // checking If-Modified-Since header if no debug mode and 
        $time = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
        if ($this->lastmod<=$time) $return304=true; // if document is newer, do not return 304
      }
  
      if (!$return304) {
        print $result; 
        header('Content-Type: text/html; charset="'.$this->config['charset'].'"');     
        header('Content-Length: '.ob_get_length());
      }
      else {
        header($_SERVER['SERVER_PROTOCOL'].' 304 Not Modified');
      }
      if ($this->lastmod) header('Last-Modified: '.gmdate('D, d M Y H:i:s T', $this->lastmod));
      if (!empty($this->config['nocache'])) {
        header('Cache-Control: no-cache, no-store, must-revalidate');
      }
      elseif (!empty($this->config['cache_control'])) {
        header('Cache-Control: '.$this->config['cache_control']);
      }
    }
    catch (Exception401 $ex) {
      http_response_code(401);
      if ($this->config['debug']) print $ex->getMessage();
    }
    catch (Exception403 $ex) {
      http_response_code(403);
      if ($this->config['debug']) print $ex->getMessage();
    }
    catch (Exception404 $ex) {
      http_response_code(404);
      if ($this->config['debug']) print $ex->getMessage();
    }
    catch (Exception500 $ex) {
      http_response_code(500);
      if ($this->config['debug']) print $ex->getMessage();
    }
    catch (\Symfony\Component\Yaml\Exception\ParseException $ex) {
      http_response_code(500);
      print "FrontMatter YAML syntax error: ".$ex->getMessage();
    }
    catch (Yosymfony\Toml\Exception\ParseException $ex) {
      http_response_code(500);
      print "FrontMatter TOML syntax error: ".$ex->getMessage();
    }    
    catch (Exception $ex) {
      http_response_code(500);
      print get_class($ex)." ";
      if ($this->config['debug']) print $ex->getMessage().' line '.$ex->getLine().' in file '.$ex->getFile();
    }
    ob_end_flush();
  }
}
