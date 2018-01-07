<?php

require 'libhtmltlp.php';

$tlp = json_decode(file_get_contents(dirname(__FILE__) . '/tlp_html.json'));

if (!(isset($tlp->{'P1'}))) {
    exit_with_error('Could not parse HTML-based json file.');
}


?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- standard metata -->
        <meta charset="utf-8" />
        <meta name="descrption" content="Ludwig Wittgenstein's Tractatus Logico-Philosophicus; side-by-side-by-side edition" />
        <meta name="author" content="Ludwig Wittgenstein" />
        <meta name="keywords" content="philosophy,logic,metaphysics,analytic philosophy,mysticisim" />
        <meta name="creator" content="Ludwig Wittgenstein" />
        <meta name="contributor" content="Kevin C. Klement" />
        <meta name="subject" content="Philosophy" />
        <meta name="date" content="<?php echo date('D M d H:i:s T Y'); ?>" />
        <meta name="source" content="German text plus Ogden-Ramsey and Pears-McGuinness translations" />
        <meta name="rights" content="Public Domain" />
        
        <!-- if mobile ready -->
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="applie-mobile-web-app-capable" content="yes" />
        <meta name="mobile-web-app-capable" content="yes" />
        
        <!-- web icon -->
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
        <title>Tractatus Logico-Philosophicus | Side-by-side-by-side edition</title>
    </head>
    <body>
        <h1 class="englishtitle">Tractatus Logico-Philosophicus</h1>
        <h1 class="germantitle">Logisch-philosophische Abhandlung</h1>
        <h3 class="byline">By Ludwig Wittgenstein</h3>
        <h3 class="pubinfo">First published by Kegan Paul (London), 1922.</h3>
        <h3 class="pubinfo">Side-by-side-by-side edition, version <?php echo get_version_num(); ?> (<?php echo date('j F Y'); ?>), containing the original German, alongside both the Ogden/Ramsey, and Pears/McGuinness English translations.</h3>
        <hr />
        <h2 class="majordivision" id="tableofcontents">Contents</h2>
        <ul class="contentslist">
            <li class="contentsitem"><a href="#intro" class="contentslink">Introduction (by Bertrand Russell)</a></li>
            <li class="contentsitem"><a href="#dedication" class="contentslink">Dedication page</a></li>
            <li class="contentsitem"><a href="#preface" class="contentslink">Preface (and translations)</a></li>
            <li class="contentsitem"><a href="#bodytext" class="contentslink">Tractatus Logico-Philosophicus (and translations)</a></li>
            <li class="contentsitem"><a href="#index" class="contentslink">Index</a></li>
        </ul>
        <hr />
        <div class="russellsintro">
            <h2 class="majordivision" id="intro">Introduction</h2>
            <h3 class="bylinebr">By Bertrand Russell, F.R.S.</h3>
        </div>
    </body>
</html>