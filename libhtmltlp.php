<?php

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

?>
