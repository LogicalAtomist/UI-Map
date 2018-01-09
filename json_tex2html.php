<?php

// License: GPLv3

require 'libconverttlp.php';

$tex_json_filename = dirname(__FILE__) . '/tlp_latex.json';
if (isset($argv[1])) {
    $tex_json_filename = $argv[1];
}

if (!(file_exists($tex_json_filename))) {
    exit_with_error('Input JSON file ' . $tex_json_filename . ' not found.');
}

$tex_obj = json_decode(file_get_contents($tex_json_filename)) ?? 'ERROR';

if ($tex_obj==='ERROR') {
    exit_with_error('JSON file was not properly parsed.');
}

$subsfile = dirname(__FILE__) . '/html_substitutions.json';

if (!(file_exists($subsfile))) {
    exit_with_error("HTML big substitutions file not found.");
}

$bigsubs = json_decode(file_get_contents($subsfile)) ?? 'ERROR';

if ($bigsubs==='ERROR') {
    exit_with_error("Could not parse HTML big substitutions file.");
}


$html_obj = new StdClass();

foreach($tex_obj as $pn => $ptext) {
    $htext = new StdClass();
    foreach ($ptext as $version => $lang_array) {
        $harray = array();
        foreach($lang_array as $p) {
            array_push($harray, tex_to_html($p, true));
        }
        $htext->{$version} = $harray;
    }
    $html_obj->{$pn} = $htext;
}

echo json_encode($html_obj, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);

?>
