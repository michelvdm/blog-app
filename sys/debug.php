<?php defined('BASE') or die('No access');

echo '<h2>', $label, '</h2><pre class="debug">'; 
var_dump( $val );
echo '---', PHP_EOL, 'Backtrace: ', PHP_EOL; 
debug_print_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS ); 
echo '</pre>', PHP_EOL; 
