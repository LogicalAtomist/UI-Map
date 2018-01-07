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
            echo '<tr>';
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

?>
