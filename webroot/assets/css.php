<?php
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
header('Content-type: text/css');
$files = explode(',', $_GET['files']);
$content = null;
foreach ($files as $file) {
  $content .= file_get_contents('css/' . $file . '.css');
}

 function compress($buffer) {
    /* remove comments */
    $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
    /* remove tabs, spaces, newlines, etc. */
    $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
    return $buffer;
  }

echo compress($content);

if(extension_loaded('zlib')){ob_end_flush();}
?>