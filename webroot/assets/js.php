<?php
if(extension_loaded('zlib')){ob_start('ob_gzhandler');}
header('Content-type: application/js');
$files = explode(',', $_GET['files']);
$content = null;
foreach ($files as $file) {
  $content .= file_get_contents('js/' . $file . '.js');
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