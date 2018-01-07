<?php

require 'libhtmltlp.php';

$tlp = json_decode(file_get_contents(dirname(__FILE__) . '/tlp_html.json'));

if (!(isset($tlp->{'P1'}))) {
    exit_with_error('Could not parse HTML-based json file.');
}


?>