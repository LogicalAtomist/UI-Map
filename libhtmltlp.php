<?php

require 'libconverttlp.php';

date_default_timezone_set('America/New_York');

function get_version_num() {
    $cmd = 'cd "' . dirname(__FILE__) . '" && git tag | tail -n 1';
    $vnum = trim(shell_exec($cmd));
    if (substr($vnum, 0, 1) != 'v') {
        $vnum = '[unknown]';
    } else {
        $vnum = substr($vnum,1);
    }
    return $vnum;
}

function depth_of($s) {
    $p = str_replace('.','',$s);
    $d = (strlen($p) - 1);
    return $d;
}

function columns_preface() {
    global $tlp;
    
    echo '
<h2 class="majordivision" id="preface">Vorwort (Preface)</h2>
<table class="prefacetable">
    <thead>
        <tr class="prefheaderrow">
            <th class="prefheadercell">German</th>
            <th class="prefheadercell">Ogden</th>
            <th class="prefheadercell">Pears/McGuinness</th>
        </tr>
    </thead>
    <tbody>
';

    foreach ($tlp as $pn => $ptext) {
        if (substr($pn,0,1) != 'P') {
            break;
        }
        echo '<tr id="' . str_replace('P','pref',$pn) . '">' . PHP_EOL;
        echo '<td class="gerpref">';
        echo $ptext->German[0];
        echo '</td>' . PHP_EOL;
        echo '<td class="ogdpref">';
        echo $ptext->Ogden[0];
        echo '</td>' . PHP_EOL;
        echo '<td class="pmcpref">';
        echo $ptext->PearsMcGuinness[0];
        echo '</td>' . PHP_EOL;
        echo '</tr>' . PHP_EOL . PHP_EOL;
    }
    
    echo '
    </tbody>
</table>';
}

function proc_special_comments($s) {
    $r = $s;
    if (mb_ereg_match('.*-- noindent --',$s)) {
        $r = '<div class="noindent">' . $r . '</div>';
    }
    if (mb_ereg_match('.*-- flushright --',$s)) {
        $r = '<div class="flushright">' . $r . '</div>';
    }
    
    return $r;
}

function columns_maintext() {
    global $tlp;
    
    echo '
<h2 class="majordivision" id="bodytext">Tractatus Logico-Philosophicus</h2>
<table class="bodytable">
    <thead>
        <tr class="headerrow">
            <th class="numheader"></th>
            <th class="headercell">German</th>
            <th class="headercell">Ogden</th>
            <th class="headercell">Pears/McGuinness</th>
        </tr>
    </thead>
    <tbody>
';

    // hyperlink footnote parker in 6.32
    $tlp->{'6.32'}->Ogden[0] = mb_ereg_replace('\*','<a href="#fn2" id="fn2marker">†</a>',$tlp->{'6.32'}->Ogden[0]);

    foreach ($tlp as $pn => $ptext) {
        if (substr($pn,0,1) == 'P') {
            continue;
        }
        for ($i=0; $i<count($ptext->German); $i++) {
            echo '<tr class="';
            echo 'tlpdepth' . depth_of($pn);
            echo '">';
            if ($i==0) {
                echo '<td class="pnum" id="p' . $pn . '">';
                echo $pn;
                if ($pn == '1')  {
                    echo '<a href="#fn1" id="fn1marker">*</a>';
                }
                echo '</td>' . PHP_EOL;
            } else {
                echo '<td></td>' . PHP_EOL;
            }
            echo '<td class="ger">';
            echo proc_special_comments($ptext->German[$i]);
            echo '</td>' . PHP_EOL;
            echo '<td class="ogd">';
            echo proc_special_comments($ptext->Ogden[$i]);
            echo '</td>' . PHP_EOL;
            echo '<td class="pmc">';
            echo proc_special_comments($ptext->PearsMcGuinness[$i]);
            echo '</td>' . PHP_EOL;
            echo '</tr>' . PHP_EOL . PHP_EOL;
            
        }
    }
    
    echo '
    </tbody>
</table>
';
    
}

function base_anchor_for($pn) {
    if (substr($pn, 0, 1) == 'P') {
        return mb_ereg_replace('P','pref',$pn);
    }
    return 'p' . $pn;
}

function version_abbreviation($version,$useslash) {
    if ($version == "German") {
        return "GER";
    }
    if ($version == "Ogden") {
        return "OGD";
    }
    if ($useslash) {
        return "P/M";
    }
    return 'PM';
}

function html_link_array_for($pn,$version,$makeanchor) {
    $r = '';
    $ba = base_anchor_for($pn);
    $s_abbr = version_abbreviation($version,true);
    $ns_abbr = version_abbreviation($version,false);
    $r .= '<span class="linkarray';
    $r .= ' tlpdepth' . depth_of($pn);
    $r .= '"';
    if ($makeanchor) {
        $r .= ' id="' . $ba . $ns_abbr .'"';
    }
    $r .= '> ';
    if ($version != 'index') {
        $r .= $s_abbr . ' ';
    }
    $r .= '[→';
    $numdone = 0;
    if ($version != "German") {
        $r .= '<a class="gerlink" href="#' . $ba . version_abbreviation('German',false) . '">' . version_abbreviation('German',true) . '</a>';
        $numdone++;
    }
    if ($version != "Ogden") {
        if ($numdone > 0) {
            $r .= '<span class="aftergerlink"> | </span>';
        }
        $r .= '<a class="ogdlink" href="#' . $ba . version_abbreviation('Ogden',false) . '">' . version_abbreviation('Ogden',true) . '</a>';
        $numdone++;
    }
    if ($version != "PearsMcGuinness") {
        if ($numdone > 0) {
            $r .= '<span class="beforepmclink"> | </span>';
        }
        $r .= '<a class="pmclink" href="#' . $ba . version_abbreviation('PearsMcGuinness',false) . '">' . version_abbreviation('PearsMcGuinness',true) . '</a>';
        $numdone++;
    }
    $r .= ']</span>';
    return $r;
}

function version_footnotes($version) {
    echo '<div id="footnotes' . $version . '">' . PHP_EOL;
    
    if ($version == "Ogden") {
        echo '<h4 class="tlpdepth2">Footnotes</h4>' . PHP_EOL;
    } else {
        echo '<h4 class="tlpdepth0">Footnote</h4>' . PHP_EOL;
    }

    if ($version == "German") {
        echo '<p class="footnote tlpdepth0" id="fn1GER"><a href="#fn1markerGER">*</a> <span id="germanfootnote1">Die Decimalzahlen als Nummern der einzelnen Sätze deuten das logische Gewicht der Sätze an, den Nachdruck, der auf ihnen in meiner Darstellung liegt. Die Sätze <var>n</var>.1, <var>n</var>.2, <var>n</var>.3, etc., sind Bemerkungen zum Sätze No. <var>n</var>; die Sätze <var>n</var>.<var>m</var>1, <var>n</var>.<var>m</var>2, etc. Bemerkungen zum Satze No. <var>n</var>.<var>m</var>; und so weiter.</span> <span class="linkarray">[→<a href="#fn1OGD" class="ogdlink">OGD</a><span class="beforepmclink"> | </span><a href="#fn1PM" class="pmclink">P/M</a>]</span></p>' . PHP_EOL;
    }

    if ($version == "Ogden") {
        echo '<p class="footnote tlpdepth0" id="fn1OGD"><a href="#fn1markerOGD">*</a> <span id="ogdenfootnote1">The decimal figures as numbers of the separate propositions indicate the logical importance of the propositions, the emphasis laid upon them in my exposition. The propositions <var>n</var>.1, <var>n</var>.2, <var>n</var>.3, etc., are comments on proposition No. <var>n</var>; the propositions <var>n</var>.<var>m</var>1, <var>n</var>.<var>m</var>2, etc., are comments on the proposition No. <var>n</var>.<var>m</var>; and so on.</span> <span class="linkarray">[→<a href="#fn1GER" class="gerlink">GER</a><span class="beforepmclink"> | </span><a href="#fn1PM" class="pmclink">P/M</a>]</span></p>'. PHP_EOL;
        echo '<p class="footnote tlpdepth2" id="fn2"><a href="#fn2marker">†</a> <em>I.e.</em> not the form of one particular law, but of any law of a certain sort (B.&thinsp;R.).</p>' . PHP_EOL; 

    }
    if ($version == "PearsMcGuinness") {
        echo '<p class="footnote tlpdepth0" id="fn1PM"><a href="#fn1markerPM">*</a> <span id="pmcfootnote1">The decimal numbers assigned to the individual propositions indicate the logical importance of the propositions, the stress laid on them in my exposition. The propositions <var>n</var>.1, <var>n</var>.2, <var>n</var>.3, etc. are comments on proposition no. <var>n</var>; the propositions <var>n</var>.<var>m</var>1, <var>n</var>.<var>m</var>2, etc. are comments on proposition no. <var>n</var>.<var>m</var>; and so on.</span> <span class="linkarray">[→<a href="#fn1GER" class="gerlink">GER</a><span class="aftergerlink"> | </span><a href="#fn1OGD" class="ogdlink">OGD</a>]</span></p>' . PHP_EOL;
    }
    
    echo '</div>' . PHP_EOL;
    
}

function html_version($version) {
    
    global $tlp;
    if ($version=="Ogden") {
        $tlp->{'6.32'}->Ogden[0] = mb_ereg_replace('\*','<a href="#fn2" id="fn2marker">†</a>',$tlp->{'6.32'}->Ogden[0]);
    }
    
    echo '<div id="corediv' . $version . '" class="versionbigdiv';
    echo ' bigdiv' . $version;
    echo '">' . PHP_EOL;
    
    echo '<hr />' . PHP_EOL;
    
    // VERSION PREFACE
    
    echo '<div id="prefacediv' . $version . '" class="prefacediv">' . PHP_EOL;
    
    $prefname = 'Vorwort (Preface)';
    if ($version == 'Ogden') {
        $prefname = 'Preface (Ogden)';
    }
    if ($version == 'PearsMcGuinness') {
        $prefname = 'Preface (Pears/McGuinness)';
    }
    echo '<h2 class="majordivision" id="preface' . $version .'">' . $prefname . '</h2>' . PHP_EOL;
    
    foreach($tlp as $pn => $ptext) {
        // break when you get past preface
        if (substr($pn, 0, 1) != 'P') {
            break;
        }
        
        // link array
        if ($pn != 'P9') {
            echo '<div class="preflinks">';
            echo html_link_array_for($pn, $version, true);
            echo '</div>' . PHP_EOL;
        }
        echo '<p>' . $ptext->{$version}[0] . '</p>' . PHP_EOL;
        
    }
    
    echo '</div>' . PHP_EOL; // end of version prefacediv
        
    // VERSION MAIN TEXT
    
    $booktitle = 'Logisch-philosophische Abhandlung (German text)';
    if ($version == 'Ogden') {
        $booktitle = 'Tractatus Logico-Philosophicus (Ogden translation)';
    }
    if ($version == 'PearsMcGuinness') {
        $booktitle = 'Tractatus Logico-Philosophicus (Pears/McGuinness translation)';
    }
    echo PHP_EOL . '<h2 class="majordivision" id="bodytext' . $version . '">' . $booktitle . '</h2>' . PHP_EOL;
    
    // main loop
    foreach($tlp as $pn => $ptext) {
        if (substr($pn,0,1) == 'P') {
            continue;
        }
        echo '<div class="corelinks';
        echo ' tlpdepth' . depth_of($pn);
        echo '">';
        echo '<strong>' . $pn . '</strong>';
        if ($pn == "1") {
            $abbrev = version_abbreviation($version, false);
            echo '<a href="#fn1' . $abbrev . '" id="fn1marker' . $abbrev . '">*</a>';    
        }
        echo html_link_array_for($pn, $version,true);
        echo '</div>' . PHP_EOL;
        // paragraph loop
        foreach($ptext->{$version} as $thispar) {
            echo '<div class="para';
            echo ' tlpdepth' . depth_of($pn);
            if (mb_ereg_match('.*-- noindent --',$thispar)) {
                echo ' noindent';
            }
            if (mb_ereg_match('.*-- flushright --',$thispar)) {
                echo ' flushright';
            }
            echo '">';
            echo $thispar;
            echo '</div>' . PHP_EOL;
        }
        
    }
    
    version_footnotes($version);
    
    echo '</div>' . PHP_EOL; // end of version corediv
        
    
}

function three_versions() {
    
    html_version('German');
    html_version('Ogden');
    html_version('PearsMcGuinness');
    
}

?>
