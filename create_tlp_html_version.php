<?php

require 'libhtmltlp.php';

$tlp = json_decode(file_get_contents(dirname(__FILE__) . '/tlp_html.json'));

if (!(isset($tlp->{'P1'}))) {
    exit_with_error('Could not parse HTML-based json file.');
}

// process settings
$columns_mode = true;
$settings_str = '';
if (isset($argv[1])) {
    $settings_str = $argv[1];
}
if (mb_ereg_match('.*nocolumns',$settings_str)) {
    $columns_mode = false;
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
        
        <!-- css -->
        <style>
            <?php readfile(dirname(__FILE__) . '/tlp.css'); ?>
            
            
        </style>
        
    </head>
    <body>
        <div id="coverpage">
            <h1 class="englishtitle">Tractatus Logico-Philosophicus</h1>
            <h1 class="germantitle">Logisch-philosophische Abhandlung</h1>
            <h3 class="byline">By Ludwig Wittgenstein</h3>
            <h3 class="pubinfo">First published by Kegan Paul (London), 1922.</h3>
            <h3 class="pubinfo">Side-by-side-by-side edition, version <?php echo get_version_num(); ?> (<?php echo date('j F Y'); ?>), containing the original German, alongside both the Ogden/Ramsey, and Pears/McGuinness English translations.</h3>
        </div>
        <div id="contents">
            <hr />
            <h2 class="majordivision" id="tableofcontents">Contents</h2>
            <ul class="contentslist">
                <li class="contentsitem"><a href="#intro" class="contentslink">Introduction (by Bertrand Russell)</a></li>
                <li class="contentsitem"><a href="#dedication" class="contentslink">Dedication page</a></li>
                <li class="contentsitem"><a href="#preface" class="contentslink">Preface (and translations)</a></li>
                <li class="contentsitem"><a href="#bodytext" class="contentslink">Tractatus Logico-Philosophicus (and translations)</a></li>
                <li class="contentsitem"><a href="#index" class="contentslink">Index</a></li>
            </ul>
        </div>
        <hr />
        <div class="russellsintro">
            <h2 class="majordivision" id="intro">Introduction</h2>
            <h3 class="bylinebr">By Bertrand Russell, F.&thinsp;R.&thinsp;S.</h3>
            
            <?php echo html_russells_intro(); ?>
            
        </div>
        <div id="dedicationpage">
            <hr />
            <h2 class="majordivision" id="dedication">Tractatus Logico-Philosophicus</h2>
            <div class="dedicationtext">Dedicated<br />to the Memory of My Friend<br />David H. Pinsent<br /></div>
            <div class="motto"><em class="germph">Motto:</em> &hellip; und alles, was man weiss, nicht bloss rauschen und brausen gehört hat, lässt sich in drei Worten sagen. –KÜRNBERGER.</div>
        </div>
        <div id="prefacesection">
            <hr />
            
            <?php
            
            if ($columns_mode) {
                columns_preface();
            } else {
            }
            
            ?>
            
        </div>
        <div id="bookcore">
            
            <?php
            
            if ($columns_mode) {
                columns_maintext();
            } else {
            }
            
            ?>
            
            
        </div>
        <div id="footnotes">
           <h4>Footnotes</h4>
                <p class="footnote" id="fn1"><a href="#fn1marker">*</a> <span id="germanfootnote1"><span class="kckaddition">[German]</span> Die Decimalzahlen als Nummern der einzelnen Sätze deuten das logische Gewicht der Sätze an, den Nachdruck, der auf ihnen in meiner Darstellung liegt. Die Sätze <var>n</var>.1, <var>n</var>.2, <var>n</var>.3, etc., sind Bemerkungen zum Sätze No. <var>n</var>; die Sätze <var>n</var>.<var>m</var>1, <var>n</var>.<var>m</var>2, etc. Bemerkungen zum Satze No. <var>n</var>.<var>m</var>; und so weiter. </span><span id="fn1sep1">/ </span><span id="ogdenfootnote1"><span class="kckaddition">[Ogden]</span> The decimal figures as numbers of the separate propositions indicate the logical importance of the propositions, the emphasis laid upon them in my exposition. The propositions <var>n</var>.1, <var>n</var>.2, <var>n</var>.3, etc., are comments on proposition No. <var>n</var>; the propositions <var>n</var>.<var>m</var>1, <var>n</var>.<var>m</var>2, etc., are comments on the proposition No. <var>n</var>.<var>m</var>; and so on. </span><span id="fn1sep2">/ </span><span id="pmcfootnote1"><span class="kckaddition">[Pears &amp; McGuinness]</span> The decimal numbers assigned to the individual propositions indicate the logical importance of the propositions, the stress laid on them in my exposition. The propositions <var>n</var>.1, <var>n</var>.2, <var>n</var>.3, etc. are comments on proposition no. <var>n</var>; the propositions <var>n</var>.<var>m</var>1, <var>n</var>.<var>m</var>2, etc. are comments on proposition no. <var>n</var>.<var>m</var>; and so on.</span></p>
            <p class="footnote" id="fn2"><a href="#fn2marker">†</a> <span class="kckaddition">[Ogden only]</span> <em>I.e.</em> not the form of one particular law, but of any law of a certain sort (B.&thinsp;R.).</p> 
            <hr />
        </div>
        <div id="indexdiv" class="indexdiv">
            <h2 class="majordivision" id="index">Index (Pears/McGuinness)</h2>
            <?php
            
            echo html_index_note();
            
            echo html_index();
            
            ?>
            
        </div>
        <div id="licenseinfo">
            <hr />
            <p class="licensep">
                <span class="ccicongroup"><object data="images/pd.svg" type="image/svg+xml" class="ccicon"><img src="images/pd.png" alt="[PD]" class="ccicon" /></object></span> <span class="sflabel">Ludwig Wittgenstein’s <i>Tractatus Logico-Philosophicus</i> is in the Public Domain.</span></p>
            <p class="licensep"><span class="ccicongroup"><object data="images/cc.svg" type="image/svg+xml" class="ccicon"><img src="images/cc.png" alt="[CC]" class="ccicon" /></object> <object data="images/by.svg" type="image/svg+xml" class="ccicon"><img src="images/by.png" alt="[BY]" class="ccicon" /></object> <object data="images/sa.svg" type="image/svg+xml" class="ccicon"><img src="images/sa.png" alt="[SA]" class="ccicon" /></object></span> <span class="sflabel">The layout of the side-by-side-by-side edition, including HTML mark-up and related content, by Kevin C. Klement, is licensed under a <a href="http://creativecommons.org/licenses/by-sa/3.0/us/">Creative Commons Attribution—Share Alike 3.0 United States License</a>.</span></p>
            <p class="licensep sflabel">Latest version available at the project page: <a href="http://people.umass.edu/klement/tlp/">http://people.umass.edu/klement/tlp/</a></p>
        </div>
    </body>
</html>