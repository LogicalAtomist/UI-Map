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

function unicodeize($s) {
    $rv = $s;
    $rv = mb_ereg_replace('\{\\\"a\}','ä', $rv);
    $rv = mb_ereg_replace('\{\\\"o\}','ö', $rv);
    $rv = mb_ereg_replace('\{\\\"u\}','ü', $rv);
    $rv = mb_ereg_replace('\{\\\"U\}','Ü', $rv);
    $rv = mb_ereg_replace('\{\\\"A\}','Ä', $rv);
    $rv = mb_ereg_replace('\{\\\ss\}','ß', $rv);
    $rv = mb_ereg_replace('\\\gdql\s*','„', $rv);
    $rv = mb_ereg_replace('\{\\\gsql\}','‚', $rv);
    $rv = mb_ereg_replace('\{\\\gsqr\}','‘', $rv);
    $rv = mb_ereg_replace('\\\gdqr\{\}\s\s*','“ ', $rv);
    $rv = mb_ereg_replace('\\\gdqr\{\}','“', $rv);
    $rv = mb_ereg_replace('\\\gdqr','“', $rv);
    $rv = mb_ereg_replace(' -- ',' – ', $rv);
    $rv = mb_ereg_replace('([^-])---([^-])','\1—\2', $rv);
    $rv = mb_ereg_replace('([^-])---$','\1—', $rv);
    $rv = mb_ereg_replace('([0-9])--([0-9])','\1–\2', $rv);
    $rv = mb_ereg_replace('``','“', $rv);
    $rv = mb_ereg_replace('`','‘', $rv);
    $rv = mb_ereg_replace('\'\'','”', $rv);
    $rv = mb_ereg_replace('\'','’', $rv);
    $rv = mb_ereg_replace('z\.\\\ B\.\\\ ','z.\\,B.\\ ', $rv);
    $rv = mb_ereg_replace('z\.\\\ B\.','z.\\,B.', $rv);
    $rv = mb_ereg_replace('z\.~B\.\\\ ','z.\\,B.\\ ', $rv);
    $rv = mb_ereg_replace('z\.~B\.','z.\\,B.', $rv);
    $rv = mb_ereg_replace('Z\.\\\ B\.\\\ ','Z.\\,B.\\ ', $rv);
    $rv = mb_ereg_replace('Z\.\\\ B\.','Z.\\,B.', $rv);
    $rv = mb_ereg_replace('U\.\\\ s\.\\\ w\.','U.\\,s.\\,w.', $rv);
    $rv = mb_ereg_replace('\\\ldots','…', $rv);
    $rv = mb_ereg_replace('\\\ae\{\}','æ', $rv);
    $rv = mb_ereg_replace('\{\\\ae\}','æ', $rv);
    return trim($rv);
}

function columns_preface() {

    global $tlp,$settings;
    $columns = 0;

    foreach( array($settings->includeGerman, $settings->includeOgden, $settings->includePearsMcGuinness) as $v) {
        if ($v) {
            $columns++;
        }
    }

    if ($columns == 0) {
        return '';
    }

    $rv = '\\phantomsection\\addcontentsline{toc}{chapter}{Preface}\\section*{';
    if ($settings->includeGerman) {
        $rv .= 'Vorwort (Preface)';
    } else {
        $rv .= 'Preface';
    }
    $rv .= '}' . PHP_EOL;


    $rv .= '\\begin{parcolumns}[sloppy,%' . PHP_EOL;
    if ($settings->ruleBetweenColumns) {
        $rv .= '    rulebetween,%' . PHP_EOL;
    }
    $rv .= '    distance=' . $settings->distanceBetweenColumns . '%' . PHP_EOL;
    $rv .= ']{' . $columns . '}'. PHP_EOL;

    // TABLE HEADER ROW
    if ($settings->includeGerman) {
        $rv .= '\\colchunk{\\negpbk\\textbf{German}\\\\~}' . PHP_EOL;
    }

    if ($settings->includeOgden) {
        $rv .= '\\colchunk{\\negpbk\\textbf{Ogden}\\\\~}' . PHP_EOL;
    }

    if ($settings->includePearsMcGuinness) {
        $rv .= '\\colchunk{\\negpbk\\textbf{Pears/McGuinness}\\\\~}' . PHP_EOL;
    }

    $rv .= '\\colplacechunks' . PHP_EOL;

    foreach ($tlp as $pn => $a) {
        //break after Preface
        if (substr($pn,0,1) != "P") {
            break;
        }

        if ($settings->includeGerman) {
            $rv .= '\\colchunk{\\selectlanguage{german}';
            $link_target = str_replace('P','pref',$pn);
            $rv .= '\hypertarget{' . $link_target . '}{}';
            $rv .= $a->German[0];
            $rv .= '}' . PHP_EOL;

        }
        if ($settings->includeOgden) {
            $rv .= '\\colchunk{\\selectlanguage{english}';
            if (!($settings->includeGerman)) {
                $link_target = str_replace('P','pref',$pn);
                $rv .= '\hypertarget{' . $link_target . '}{}';
            }
            $rv .= $a->Ogden[0];
            $rv .= '}' . PHP_EOL;
        }
        if ($settings->includePearsMcGuinness) {
            $rv .= '\\colchunk{\\selectlanguage{english}';
            if ((!($settings->includeGerman)) && (!($settings->includeOgden))) {
                $link_target = str_replace('P','pref',$pn);
                $rv .= '\hypertarget{' . $link_target . '}{}';
            }
            $rv .= $a->PearsMcGuinness[0];
            $rv .= '}' . PHP_EOL;
        }
        $rv .= '\\colplacechunks' . PHP_EOL;
    }

    $rv .= '\\end{parcolumns}\\clearpage%' . PHP_EOL;

    return $rv;
}

function version_abbreviation($version,$slash) {
    if ($version == 'German') {
        return 'GER';
    }
    if ($version == 'Ogden') {
        return 'OGD';
    }
    if ($version == 'PearsMcGuinness') {
        if ($slash) {
            return 'P/M';
        } else {
            return 'PM';
        }
    }
    return 'ERR';
}

function base_target($pn) {
    if (substr($pn, 0, 1) == 'P') {
        return 'pref' . substr($pn, 1);
    }
    return 'prop' . $pn;
}

function link_bracket_for($pn,$version,$anchor) {
    global $settings;
    $r = '';
    $abbrev = version_abbreviation($version,false);
    $base_target = base_target($pn);
    if ($anchor) {
        $r .= '\\hypertarget{' . $base_target . $abbrev; 
        if (!($settings->isLastPass)) {
            $r .= '-' . $settings->currPassNum;
        }
        $r .= '}{';
    }
    $numvers = 0;
    foreach(array($settings->includeGerman, $settings->includeOgden, $settings->includePearsMcGuinness) as $b) {
        if ($b) { $numvers++; }
    }
    if (($numvers < 2) && ($version != 'index')) {
        if ($anchor) {
            $r .= '}';
            return $r;
        } else {
            return '';
        }
    }

    $r .= '\\textsf{';
    if ($version != 'index') {
        $r .= '\\scriptsize ';
        $r .= version_abbreviation($version,true);
        $r .= ' ';
    } else {
        $r .= '\\tiny ';
    }
    $r .= '[';
    $numdone = 0;

    if (($settings->includeGerman) && ($version != 'German')) {
        $r .= '\\hyperlink{' . $base_target . version_abbreviation('German',false);
        if (!($settings->isLastPass)) {
            $r .= '-' . $settings->currPassNum;
        }
        $r .= '}{$\rightarrow$' . version_abbreviation('German',true) . '}';
        $numdone++;                                                                                                               
    }
    if (($settings->includeOgden) && ($version != 'Ogden')) {
        if ($numdone > 0) {
            $r .= ' | ';
        }
        $r .= '\\hyperlink{' . $base_target . version_abbreviation('Ogden',false);
        if (!($settings->isLastPass)) {
            $r .= '-' . $settings->currPassNum;
        }
        $r .= '}{';
        if ($numdone == 0) {
            $r.= '$\rightarrow$';
        }
        $r .= version_abbreviation('Ogden',true) . '}';
        $numdone++;                                                                                                               
    }
    if (($settings->includePearsMcGuinness) && ($version != 'PearsMcGuinness')) {
        if ($numdone > 0) {
            $r .= ' | ';
        }
        $r .= '\\hyperlink{' . $base_target . version_abbreviation('PearsMcGuinness',false); 
        if (!($settings->isLastPass)) {
            $r .= '-' . $settings->currPassNum;
        }
        $r .= '}{';
        if ($numdone == 0) {
            $r.= '$\rightarrow$';
        }        
        $r .= version_abbreviation('PearsMcGuinness',true) . '}';
    }

    $r .= ']}';

    if ($anchor) {
        $r .= '}';
    }
    return $r;
}

function standalone_preface($version) {

    global $settings,$tlp;

    if ($version == 'German') {
        $identifier = 'Vorwort';
        $short_identifier = $identifier;
        if (($settings->includeOgden) || ($settings->includePearsMcGuinness)) {
            $identifier .= ' (German preface)';
            $short_identifier .= ' (Preface)';
        }
    }

    if ($version == 'Ogden') {
        $identifier = 'Preface';
        $short_identifier = $identifier;
        if (($settings->includeGerman) && (!($settings->includePearsMcGuinness))) {
            $identifier .= ' (English)';
        }
        if ($settings->includePearsMcGuinness) {
            $identifier .= ' (Ogden)';
        }
    }

    if ($version == 'PearsMcGuinness') {
        $identifier = 'Preface';
        $short_identifier = $identifier;
        if (($settings->includeGerman) && (!($settings->includeOgden))) {
            $identifier .= ' (English)';
        }
        if ($settings->includeOgden) {
            $identifier .= ' (Pears/McGuinness)';
        }
    }

    $rv = '\\phantomsection\\addcontentsline{toc}{section}{' . $short_identifier . '}\\section*{' . $identifier . '}' . PHP_EOL;

    foreach ($tlp as $pn => $a) {
        //break after Preface
        if (substr($pn,0,1) != "P") {
            break;
        }

        if ($pn != 'P9') {
            $rv .= '\\noindent ';
            $rv .= link_bracket_for($pn,$version,true) . PHP_EOL . PHP_EOL;
        }

        $rv .= $a->{$version}[0] . PHP_EOL . PHP_EOL;
    }
    $rv .= '\\clearpage%' . PHP_EOL;
    return $rv;
}

function prop_one_footnote_text_for_version($version) {
    if ($version == 'German') {
        return 'Die Decimalzahlen als Nummern der einzelnen Sätze deuten das logische Gewicht der Sätze an, den Nachdruck, der auf ihnen in meiner Darstellung liegt. Die Sätze $n.1,\\thickspace n.2,\\thickspace n.3,$ etc., sind Bemerkungen zum Satze No.\\ $n$; die Sätze $n.m1,\\thickspace n.m2,$ etc.\\ Bemerkungen zum Satze No.\\ $n.m$; und so weiter.';
    }
    if ($version == 'Ogden') {
        return 'The decimal figures as numbers of the separate propositions indicate the logical importance of the propositions, the emphasis laid upon them in my exposition. The propositions $n.1,\\thickspace n.2,\\thickspace n.3,$ etc., are comments on proposition No.\\ $n$; the propositions $n.m1,\\thickspace n.m2,$ etc., are comments on the proposition No.\\ $n.m$; and so on.';
    }
    return 'The decimal numbers assigned to the individual propositions indicate the logical importance of the propositions, the stress laid on them in my exposition. The propositions $n.1,\\thickspace n.2,\\thickspace n.3,$ etc.\\ are comments on proposition no. $n$; the propositions $n.m1,\\thickspace n.m2,$ etc.\\ are comments on proposition no. $n.m$; and so on.';
}

function proposition_one_footnote_text() {
    global $settings;
    $r = '\\renewcommand{\\thefootnote}{}' . PHP_EOL;
    $r .= '\\footnotetext{* ';
    if ($settings->includeGerman) {
        $r .= '\\kckaddition{[German]} ' . prop_one_footnote_text_for_version('German');
        if (($settings->includeOgden) || ($settings->includePearsMcGuinness)) {
            $r .= '\\ / ';
        }
    }
    if ($settings->includeOgden) {
        $r .= '\\kckaddition{[Ogden]} ' . prop_one_footnote_text_for_version('Ogden');
        if ($settings->includePearsMcGuinness) {
            $r .= '\\ / ';
        }
    }
    if ($settings->includePearsMcGuinness) {
        $r .= '\\kckaddition{[Pears \\& McGuinness]} ' . prop_one_footnote_text_for_version('PearsMcGuinness');
    }
    $r .= '}' . PHP_EOL;
    return $r;
}

function start_col_environment($columns,$rulebetween,$dist) {
    $r = '\\begin{parcolumns}[sloppy,%' . PHP_EOL;
    if ($rulebetween) {
        $r .= '    rulebetween,%' . PHP_EOL;
    }
    $r .= '    distance=' . $dist . ',%' . PHP_EOL;
    $r .= '    colwidths={1={\\pnmaxwidth}}%' . PHP_EOL;
    $r .= ']{' . ($columns+1) . '}' . PHP_EOL;
    return $r;
}

function end_col_environment() {
    return '\\end{parcolumns}%' . PHP_EOL ;
}

function br_footnote_text($mark) {
    $r = '\\footnotetext{* ';
    if ($mark) {
        $r .= '\\kckaddition{[Ogden only]} ';
    }
    $r .= '\\emph{I.e.}\\ not the form of one particular law, but of any law of a certain sort (B.\\,R.).}' . PHP_EOL;
    return $r;
}
function depth_of($s) {
    $p = str_replace('.','',$s);
    $d = (strlen($p) - 1);
    return $d;
}

function multicolumn_version() {
    global $tlp,$settings;

    // handle preface if necessary
    if ($settings->includePreface) {
        echo columns_preface();
    }

    // count columns
    $columns = 0;
    foreach( array($settings->includeGerman, $settings->includeOgden, $settings->includePearsMcGuinness) as $v) {
        if ($v) {
            $columns++;
        }
    }
    if ($columns == 0) {
        echo 'No versions selected!';
        return;
    }

    // title header
    echo '\\phantomsection\\addcontentsline{toc}{chapter}{Tractatus Logico-Philosophicus}\\section*{Tractatus Logico-Philosophicus}' . PHP_EOL;

    // PASSES LOOP START

    for ($passnum = 0; $passnum<count($settings->passes); $passnum++) {
        $settings->thisPass = $settings->passes[$passnum];
        $settings->isLastPass = (($passnum+1) == count($settings->passes));
        $settings->currPassNum = $passnum;

        // depth level indicator
        if ($settings->useDepthMarkers) {
            echo PHP_EOL . PHP_EOL;

            if ($settings->thisPass->maxDepth > $settings->thisPass->minDepth) { 
                echo '\\noindent\\hrulefill\ \textsf{[depth levels ' . $settings->thisPass->minDepth . '–' . $settings->thisPass->maxDepth . ']}';  
            } else {
                echo '\\noindent\\hrulefill\ \textsf{[depth level ' . $settings->thisPass->minDepth . ']}';  
            }
            echo PHP_EOL . PHP_EOL;
        } else {
            if ($passnum != 0) {
                echo PHP_EOL . PHP_EOL . '\\bigskip' . PHP_EOL . PHP_EOL;
            }
        }

        // footnote on section 1
        if (($settings->thisPass->minDepth == 0) && ($settings->thisPass->startProposition == "1") && (!($settings->fnDone)) ) {
            echo proposition_one_footnote_text();
            $settings->fnDone = true;
        }

        // have latex determine width of widest label
        if ($passnum == 0) {
            echo '\\newlength{\\pnmaxwidth}%' . PHP_EOL . '\\setlength{\\pnmaxwidth}{\\widthof{\\textbf{2.02331}}}%' . PHP_EOL;
        }
        // start columns
        echo start_col_environment($columns, $settings->ruleBetweenColumns, $settings->distanceBetweenColumns);

        // TABLE HEADER ROW
        echo '\\colchunk{}' . PHP_EOL;
        if ($settings->includeGerman) {
            echo '\\colchunk{\\negpbk\\textbf{German}\\\\~}' . PHP_EOL;
        }

        if ($settings->includeOgden) {
            echo '\\colchunk{\\negpbk\\textbf{Ogden}\\\\~}' . PHP_EOL;
        }

        if ($settings->includePearsMcGuinness) {
            echo '\\colchunk{\\negpbk\\textbf{Pears/McGuinness}\\\\~}' . PHP_EOL;
        }

        echo '\\colplacechunks' . PHP_EOL;

        // MAIN LOOP THROUGH TLP PROPOSITIONS
        $reached_starting_prop = false;
        $reached_ending_prop = false;

        foreach ($tlp as $pn => $ptext) {
            // see if reached starting proposition; if not, go to next proposition
            // this should also skip the Preface
            if (!($reached_starting_prop)) {
                if ($pn == $settings->thisPass->startProposition) {
                    $reached_starting_prop = true;
                } else {
                    continue;
                }
            }

            // check if depth is within range of current settings
            $pndepth = depth_of($pn);
            if (($pndepth < $settings->thisPass->minDepth) || ($pndepth > $settings->thisPass->maxDepth)) {
                if ($pn == $settings->thisPass->endProposition) {
                    $reached_ending_prop = true;
                    break;
                } else {
                    continue;
                }
            }

            // loop through paragraphs in proposition
            for ($i=0; $i<count($ptext->German); $i++) {
                // first column in row, for prop number
                if ($i==0) {
                    echo '\\colchunk{\\negpbk\\hypertarget{prop';
                    echo $pn;
                    if (!($settings->isLastPass)) {
                        echo '-' . $settings->currPassNum;
                    }
                    echo '}{\\textbf{';
                    echo $pn;
                    if ($pn == '1') { echo '*'; }
                    echo '}}}' . PHP_EOL;
                } else {
                    echo '\\colchunk{}' . PHP_EOL;
                }

                //German column
                if ($settings->includeGerman) {
                    echo '\\colchunk{\\selectlanguage{german}';
                    echo $ptext->German[$i];
                    echo '}' . PHP_EOL;
                }
                //Ogden column
                if ($settings->includeOgden) {
                    echo '\\colchunk{\\selectlanguage{english}';
                    echo $ptext->Ogden[$i];
                    echo '}' . PHP_EOL;
                }
                //Pears/McGuinness column
                if ($settings->includePearsMcGuinness) {
                    echo '\\colchunk{\\selectlanguage{english}';
                    echo $ptext->PearsMcGuinness[$i];
                    echo '}' . PHP_EOL;
                }
                echo '\\colplacechunks' . PHP_EOL;

            }

            // check if footnote for 6.32 needed
            if (($pn == '6.32') && ($settings->includeOgden)) {
                echo end_col_environment();
                echo br_footnote_text(true);
                echo start_col_environment($columns, $settings->ruleBetweenColumns, $settings->distanceBetweenColumns);
            }

            // check if ending proposition; if so, break loop
            if ($pn == $settings->thisPass->endProposition) {
                $reached_ending_prop = true;
                break;
            } 
        }

        // finish column block
        echo end_col_environment();


    } // PASSES LOOP END
}

function index_link_for($s) {
    global $settings;
    if (mb_ereg_match('.*–', $s)) {
        $parts = explode('–',$s,2);
        return index_link_for($parts[0]) . '–' . index_link_for($parts[1]);
    }
    if (substr($s, 0, 1) == 'P') {
        $targ = mb_ereg_replace('P','pref',$s);
        if ($settings->includePreface) {
            if ($settings->multicolumnLayout) {
                return '\\hyperlink{' . $targ . '}{' . $s . '}';
            } else {
                return $s . ' ' . link_bracket_for($s, 'index', false);
            }
        } else {
            return $s;
        }
    }
    $d = depth_of($s);
    if (($d < $settings->thisPass->minDepth) || ($d > $settings->thisPass->maxDepth)) {
        return $s;
    } 

    if ($settings->multicolumnLayout) {
        return '\\hyperlink{prop' . $s . '}{' . $s . '}';
    }
    return $s . ' ' . link_bracket_for($s, 'index', false);


}

function insert_index() {
    $ind_entries = json_decode(file_get_contents(dirname(__FILE__) . '/tlp_index.json'));

    if (!(is_array($ind_entries))) {
        echo '\\indexentry{Failed to decode index JSON file.}';
        return;
    }

    $start_letter = 'a';

    foreach ($ind_entries as $e) {
        // check if new first letter
        if (!($e->isSubEntry)) {
            if ( substr($e->entryname, 0, 8) == '\\textit{' ) {
                $this_first_letter = strtolower( substr($e->entryname, 8, 1) );
            } else {
                $this_first_letter = strtolower( substr($e->entryname, 0, 1) );
            }
            if ($this_first_letter != $start_letter) {
                $start_letter = $this_first_letter;
                echo PHP_EOL . '\\indexgap' . PHP_EOL . PHP_EOL;
            }
        }
        // type of entry
        if ($e->isSubSubEntry) {
            echo '        \indexsubsubentry{';
        } else {
            if ($e->isSubEntry) {
                echo '    \indexsubentry{';
            } else {
                echo '\indexentry{';
            }
        }

        echo $e->entryname;
        if ((isset($e->cf)) || (isset($e->refs))) {
            echo ', ';
        }
        if (isset($e->cf)) {
            echo 'cf.\\ ' . $e->cf . '.'; 
        }
        if (isset($e->refs)) {
            for ($i=0; $i<count($e->refs); $i++) {
                $r = $e->refs[$i];
                echo index_link_for($r->target);
                if (isset($r->aftertext)) {
                    if (!(substr($r->aftertext, 0, 1) == ';')) {
                        echo ' ';
                    }
                    echo $r->aftertext . ' ';
                } else {
                    if (($i+1) != count($e->refs)) {
                        echo ', ';
                    }
                }
            }
        }

        echo '}' . PHP_EOL;

    }

}

function standalone_version($version) {
    global $settings,$tlp;

    $booktitle = 'Tractatus Logico-Philosophicus';
    $toc_line = 'English translation';
    $title_header = $booktitle;

    if ($version=='German') {
        echo '\\selectlanguage{german}%' . PHP_EOL;
        $booktitle = 'Logisch-philosophische Abhandlung';
        $title_header = $booktitle;
        $toc_line = 'German text';
        if (($settings->includeOgden) || ($settings->includePearsMcGuinness)) {
            $title_header = $booktitle . ' (German text)';
        }
    }
    if ($version=='Ogden') {
        echo '\\selectlanguage{english}%' . PHP_EOL;
        $toc_line .= ' (Ogden)';
        if ($settings->includePearsMcGuinness) {
            $toc_line = 'Ogden translation';
        } 
        if (($settings->includeGerman) ||  ($settings->includePearsMcGuinness)) {
            $title_header .= ' (Ogden translation)';
        }
    }
    if ($version=='PearsMcGuinness') {
        echo '\\selectlanguage{english}%' . PHP_EOL;
        $toc_line .= ' (Pears/McGuinness)';
        if ($settings->includeOgden) {
            $toc_line = 'Pears/McGuinness translation';
        }
        if (($settings->includeGerman) ||  ($settings->includeOgden)) {
            $title_header .= ' (Pears/McGuinness translation)';
        }
    }

    // toc category listing
    echo '\\clearpage\\phantomsection\\addcontentsline{toc}{chapter}{' . $toc_line . '}%' . PHP_EOL;

    // preface
    if ($settings->includePreface) {
        echo standalone_preface($version);
    }

    // header and toc entry
    echo '\\phantomsection\\addcontentsline{toc}{section}{' . $booktitle . '}\\section*{\raggedright ' . $title_header . '}%' . PHP_EOL;






    // PASSES LOOP

    $settings->fnDone = false;
    for ($passnum = 0; $passnum<count($settings->passes); $passnum++) {
        $settings->thisPass = $settings->passes[$passnum];
        $settings->isLastPass = (($passnum+1) == count($settings->passes));
        $settings->currPassNum = $passnum;

        // depth level indicator
        if ($settings->useDepthMarkers) {
            echo PHP_EOL . PHP_EOL;

            if ($settings->thisPass->maxDepth > $settings->thisPass->minDepth) { 
                echo '\\noindent\\hrulefill\ \\textsf{[depth levels ' . $settings->thisPass->minDepth . '–' . $settings->thisPass->maxDepth . ']}';  
            } else {
                echo '\\noindent\\hrulefill\ \\textsf{[depth level ' . $settings->thisPass->minDepth . ']}';  
            }
            echo PHP_EOL . PHP_EOL;
        } else {
            if ($passnum != 0) {
                echo PHP_EOL . PHP_EOL . '\\bigskip' . PHP_EOL . PHP_EOL;
            }
        }

        //footnote on prop1
        if (($settings->thisPass->minDepth == 0) && ($settings->thisPass->startProposition == "1") && (!($settings->fnDone)) ) {
            echo '\\renewcommand{\\thefootnote}{}%' . PHP_EOL . '\\footnotetext{* ' . prop_one_footnote_text_for_version($version) . '}%' . PHP_EOL;
            $settings->fnDone = true;
        }

        // MAIN LOOP THROUGH TLP PROPOSITIONS
        $reached_starting_prop = false;
        $reached_ending_prop = false;

        foreach ($tlp as $pn => $ptext) {
            // see if reached starting proposition; if not, go to next proposition
            // this should also skip the Preface
            if (!($reached_starting_prop)) {
                if ($pn == $settings->thisPass->startProposition) {
                    $reached_starting_prop = true;
                } else {
                    continue;
                }
            }

            // check if depth is within range of current settings
            $pndepth = depth_of($pn);
            if (($pndepth < $settings->thisPass->minDepth) || ($pndepth > $settings->thisPass->maxDepth)) {
                if ($pn == $settings->thisPass->endProposition) {
                    $reached_ending_prop = true;
                    break;
                } else {
                    continue;
                }
            }

            // prop num marker
            echo '\\noindent\\textbf{' . $pn;
            if ($pn == '1') { echo '*'; }
            echo '} ';
            echo link_bracket_for($pn,$version,true);
            echo PHP_EOL . PHP_EOL;


            // loop through paragraphs in proposition
            for ($i=0; $i<count($ptext->{$version}); $i++) {
                echo '{' . $ptext->{$version}[$i] ;
                // insert footnote
                if (($pn == '6.32') && ($version=='Ogden')) {
                    echo br_footnote_text(false);
                }            
                echo '\\par}' . PHP_EOL . PHP_EOL;    
            }

            // check if ending proposition; if so, break loop
            if ($pn == $settings->thisPass->endProposition) {
                $reached_ending_prop = true;
                break;
            } 
        }

    } // end passes loop

}

function german_standalone_version() {
    standalone_version('German');
}
function ogden_standalone_version() {
    standalone_version('Ogden');
}
function pmc_standalone_version() {
    standalone_version('PearsMcGuinness');
}


?>