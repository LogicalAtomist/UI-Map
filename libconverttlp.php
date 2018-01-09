<?php

// License: GPLv3

function remove_tex_comments($tex) {
    $lines = explode(PHP_EOL, $tex);
    $r_array = array();
    $i=0;
    while ($i<count($lines)) {
        $e=0;
        $newline = '';
        while ((mb_ereg_match('%', $lines[$i+$e])) || mb_ereg_match('.*[^\\\]%', $lines[$i+$e])) {
            if (mb_ereg_match('%', $lines[$i+$e])) {
                $e++;
                continue;
            }
            // this isn't quite right for lines with \% before %, but too lazy now
            $newline .= explode('%',$lines[$i+$e],2)[0];
            $e++;
        }
        array_push($r_array, $newline .= $lines[$i+$e]);
        $i = $i + $e + 1;
    }
    return implode(PHP_EOL, $r_array);
}

function is_blank($s) {
    return (trim($s) == '');
}

function math_letters_to_vars($s) {
    return mb_ereg_replace('([A-MP-Za-zα-ω]+)','<var>\1</var>',$s);
}

function apply_math_spacers($s) {
    // replace hyphen with minus sign in math
    $r = mb_ereg_replace('-','<span class="mathrel">−</span>',$s);
    // mathrel spacing for =, +
    $r = mb_ereg_replace('\\s*([=+…])\\s*','<span class="mathrel">\1</span>',$r);
    return $r;
}

function prettify_math_part($p) {
    // look for html tags
    $e = explode('<',$p,2);
    // if there are no html tags, proceed
    if (count($e) == 1) {
        $ret = math_letters_to_vars($p);
        $ret = apply_math_spacers($ret);
        return $ret;
    }
    // there is at least one tag
    $after_chunks = explode('>', $e[1],2);
    
    return prettify_math_part($e[0]) . '<' . $after_chunks[0] . '>' .
        prettify_math_part($after_chunks[1]);    
}

function prettify_all_math($s) {
    $math_parts = explode('$', $s);
    
    // odd segments in above array should be math
    for ($i=1; $i<count($math_parts) ; $i += 2) {
        $math_parts[$i] = '<span class="mathmode">' . prettify_math_part($math_parts[$i]) . '</span>';
    }
    
    $r = implode('',$math_parts);
    return $r;
                                                                           
}

function break_into_paragraphs($tex) {
    $t = mb_ereg_replace('\\\par', PHP_EOL . PHP_EOL, $tex);
    $lines = explode(PHP_EOL, trim($t));
    if (count($lines)==1) {
        return $tex;
    }
    $r_array = array();
    for ($i=0; $i<count($lines); $i++) {
        if (is_blank($lines[$i])) {
            continue;
        }
        $add = $lines[$i];
        if (($i>0) && (is_blank($lines[$i-1]))) {
            $add = '<p>' . ltrim($add);
        }
        if ((($i + 1) != count($lines)) && (is_blank($lines[$i+1]))) {
            $add = rtrim($add) . '</p>';
        }
        array_push($r_array, $add);
    }
    $s = implode(PHP_EOL , $r_array);
    if (mb_ereg_match('.*<\/p>',$s)) {
        if (mb_substr($s,0,3) != '<p>') {
            $s = '<p>' . ltrim($s);
        }
    }
    if (mb_ereg_match('.*<p>',$s)) {
        if (mb_substr($s,-4) != '</p>') {
            $s = rtrim($s) . '</p>';
        }
    }
    return $s;
    
}

function handle_big_subs($t) {
    
    global $bigsubs;
    $r=$t;
        
    foreach ($bigsubs as $cmd => $sub) {
        $r = str_replace($cmd, $sub, $r);
    }
        
    return $r;
    
}

function preprocess($tex) {
    $t = remove_tex_comments($tex);
    return $t;
}

function tex_to_html($tex, $do_big_subs = true) {
    $t = preprocess($tex);
    
    // prevent five dashes from en/emdashing; restore later
    $t = mb_ereg_replace('-----', '⊚⊚⊚⊚⊚', $t);
    
    // line breaks
    $t = mb_ereg_replace('\\s*(\\\)(\\\) *','<br />' . PHP_EOL,$t);
    
    // manual spaces
    $t = mb_ereg_replace('\\\ ',' ',$t);
    
    //ampersand
    $t = mb_ereg_replace('\\\&','&amp;',$t);
    
    //nonbreaking spaces; fix later
    $t = mb_ereg_replace('~','<nbsp>',$t);
    $t = mb_ereg_replace('\\\qquad\\s*',' <nbsp> ',$t);
        
    // double quotation marks, tex style
    $t = mb_ereg_replace('``', '“', $t);
    $t = mb_ereg_replace('\'\'', '”', $t);
    
    // single quotation marks, text style
    $t = mb_ereg_replace('`', '‘', $t);
    $t = mb_ereg_replace('\'', '’', $t);
    
    // elipses
    $t = mb_ereg_replace('\\\ldots', '…', $t);
    $t = mb_ereg_replace('\\\dotsc', '…', $t);
    $t = mb_ereg_replace('\\\dots', '…', $t);

    // em and en dashes
    $t = mb_ereg_replace('---','—',$t);
    $t = mb_ereg_replace('--','–',$t);

    // manual hyphen markers
    $t = mb_ereg_replace('\\\-', '', $t);
    $t = mb_ereg_replace('\\\hyp{}', '-', $t);
   
    // Greek letters, etc.
    $t = mb_ereg_replace('\\\xi *','ξ',$t);
    $t = mb_ereg_replace('\\\phi *','φ',$t);
    $t = mb_ereg_replace('\\\psi *','ψ',$t);
    $t = mb_ereg_replace('\\\eta *','η',$t);
    $t = mb_ereg_replace('\\\nu *','ν',$t);
    $t = mb_ereg_replace('\\\mu *','μ',$t);
    $t = mb_ereg_replace('\\\Omega *','Ω',$t);
    $t = mb_ereg_replace('\\\Omega *','Ω',$t);
    $t = mb_ereg_replace('\\\aleph *','<span class="symbol">ℵ</span>',$t);
    $t = mb_ereg_replace('\\\sharp','<span class="symbol">♯</span>',$t);
    $t = mb_ereg_replace('\\\flat','<span class="symbol">♭</span>',$t);

    //logical symbols
    $t = mb_ereg_replace('\\\sim', '~', $t);
    
    // overlining
    $t = mb_ereg_replace('\\\overline{([^{}]*)}','<span class="overlined">\1</span>',$t, "m"); 
    
    // N-operator
    $t = mb_ereg_replace('\\\nop','<span class="nop">N</span>',$t);
    
    // Omega operators
    $t = mb_ereg_replace('\\\omopparen\\[([^\[\]]*)\\]{([^{}]*)} *','<span class="mathop">(Ω<sup>\1</sup>)<sup>\2</sup>’</span>',$t);
    $t = mb_ereg_replace('\\\omopparen{([^{}]*)} *','<span class="mathop">(Ω<sup>\1</sup>)’</span>',$t);
    $t = mb_ereg_replace('\\\omopparen *','<span class="mathop">(Ω)’</span>',$t);
    $t = mb_ereg_replace('\\\omop\\[([^\[\]]*)\\] *','<span class="mathop">Ω<sup>\1</sup>’</span>',$t);
    $t = mb_ereg_replace('\\\omop *','<span class="mathop">Ω’</span>',$t);
    
    // emphasis
    $t = mb_ereg_replace('\\\emph{([^{}]*)}','<em>\1</em>',$t, "m"); 
    $t = mb_ereg_replace('\\\textit{([^{}]*)}','<em>\1</em>',$t, "m"); 
    $t = mb_ereg_replace('\\\germph{([^{}]*)}','<em class="germph">\1</em>',$t, "m"); 
    $t = mb_ereg_replace('\\\textbf{([^{}]*)}','<strong>\1</strong>',$t, "m"); 
    
    // smallcaps
    $t = mb_ereg_replace('\\\textsc{([^{}]*)}','<span class="textsc">\1</span>',$t, "m"); 
    
    // phantom 
    $t = mb_ereg_replace('\\\phantom{([^{}]*)}','<span class="phantom" style="visibility: hidden;">\1</span>',$t, "m"); 
    
    // text in mathmode
    $t = mb_ereg_replace('\\\text{([^{}]*)}','\1',$t, "m"); 
    
    // my additions
    $t = mb_ereg_replace('\\\kckaddition{([^{}]*)}','<span class="kckaddition">\1</span>',$t, "m");
    
    // special spaces
    $t = mb_ereg_replace('\\\thickspace\\s*', ' ', $t);
    $t = mb_ereg_replace('\\\medspace\\s*', ' ', $t);
    $t = mb_ereg_replace('\\\thinspace\\s*', ' ', $t);
    $t = mb_ereg_replace('\\\,', ' ', $t);
    $t = mb_ereg_replace('\\\negthinspace\\s*', '', $t);
    
    // quantifiers
    $t = mb_ereg_replace('\\\rsomedd{([^{}]*)} *', '<span class="quant">(<span class="symbol">∃</span>\1):</span>', $t,"m");
    $t = mb_ereg_replace('\\\ralldd{([^{}]*)} *', '<span class="quant">(\1):</span>', $t,"m");   
    $t = mb_ereg_replace('\\\rsomed{([^{}]*)} *', '<span class="quant">(<span class="symbol">∃</span>\1).</span>', $t,"m");
    $t = mb_ereg_replace('\\\ralld{([^{}]*)} *', '<span class="quant">(\1).</span>', $t,"m");   
    $t = mb_ereg_replace('\\\rsome{([^{}]*)} *', '<span class="quant">(<span class="symbol">∃</span>\1)</span>', $t,"m");
    $t = mb_ereg_replace('\\\rall{([^{}]*)} *', '<span class="quant">(\1)</span>', $t,"m");   
    
    //standlone math operators get no special spacing
    $t = mb_ereg_replace('\\$\\\lor\\$','∨',$t);
    $t = mb_ereg_replace('\\$\\\rand\\$','.',$t);
    $t = mb_ereg_replace('\\$\\\rimplies\\$','⊃',$t);
    $t = mb_ereg_replace('\\$\\\rnot\\$','~',$t);
    
    // other math
    $t = mb_ereg_replace(' *\\\rand *','<span class="mathrel">.</span>', $t);
    $t = mb_ereg_replace(' *\\\mathrel{:} *','<span class="mathrel">:</span>', $t);
    $t = mb_ereg_replace(' *\\\dlord *','<span class="mathrel">.<span class="symbol">∨</span>.</span>', $t);
    $t = mb_ereg_replace(' *\\\lor *','<span class="mathrel"><span class="symbol">∨</span></span>', $t);
    $t = mb_ereg_replace(' *\\\ddrimpliesdd *','<span class="mathrel">:<span class="symbol">⊃</span>:</span>', $t);
    $t = mb_ereg_replace(' *\\\drimpliesd *','<span class="mathrel">.<span class="symbol">⊃</span>.</span>', $t);
    $t = mb_ereg_replace(' *\\\drimplies *','<span class="mathrel">.<span class="symbol">⊃</span></span>', $t);
    $t = mb_ereg_replace(' *\\\rimplies *','<span class="mathrel"><span class="symbol">⊃</span></span>', $t);
    $t = mb_ereg_replace(' *\\\dshefferd *','<span class="mathrel">.|.</span>', $t);
    $t = mb_ereg_replace(' *\\\sheffer *','<span class="mathrel">|</span>', $t);
    $t = mb_ereg_replace('\\\rnot *','<span class="mathop">~</span>', $t);
    $t = mb_ereg_replace('\\\times', '×', $t);
    $t = mb_ereg_replace('\\\tfrac\{1\}\{2\} *', '½', $t);
    $t = mb_ereg_replace('\\\vdash *', '⊢', $t);
    $t = mb_ereg_replace('\\\Op *','<span class="mathop"><mathrm:O>’</span>', $t);
    
    //mathspacing stuff; preproc mathrm later
    $t = mb_ereg_replace('\\\mathrm{([^{}]*)}','<mathrm:\1>',$t, "m"); 
    $t = mb_ereg_replace('\\\mathord{([^{}]*)}','\1',$t, "m"); 
    $t = mb_ereg_replace('\\\mathop{([^{}]*)}','<span class="mathop">\1</span>',$t, "m"); 
    
    // subscripts
    $t = mb_ereg_replace('_{([^{}]*)}','<sub>\1</sub>',$t);
    $t = mb_ereg_replace('_(.)','<sub>\1</sub>',$t);
    
    // being and end description
    $t = mb_ereg_replace('\\\begin{description}[^\\n]*','<ul class="desc">',$t);
    $t = mb_ereg_replace('\\\end{description}[^\\n]*','</ul>',$t);
    
    //centerblock
    $t = mb_ereg_replace('\\\begin{center}','<div class="centered">',$t);
    $t = mb_ereg_replace('\\\end{center}','</div>',$t);
    
    // big math delimiters; the $ will eventually be removed when math is processed
    $t = mb_ereg_replace('(\\\)(\[)','<div class="centered">$',$t);
    $t = mb_ereg_replace('(\\\)(\])','$</div>$',$t);
    
    // description/list items
    $t = mb_ereg_replace('\\n\\s*\\\item\\s*([^\\n]*)',PHP_EOL . '<li>\1</li>',$t);
    
    // identation stuff; treat as comments for now
    $t = mb_ereg_replace('\\\negpbk','<!-- noindent -->',$t);
    $t = mb_ereg_replace('\\\flushright','<!-- flushright -->',$t);
    
    // remove {}
    $t = mb_ereg_replace('{}','',$t);
    
    // something to insert vars in math
    $t = prettify_all_math($t);
    
    // restore mathrm
    $t=mb_ereg_replace('<mathrm:([^>]*)>','<span class="mathrm">\1</span>', $t);
    $t=mb_ereg_replace('<nbsp>','&nbsp;', $t);
    
    // restore five dashes
    $t = mb_ereg_replace('⊚⊚⊚⊚⊚', '−−−−−', $t);
    
    // handle_big_substitutions
    if ($do_big_subs) {
        $t = handle_big_subs($t);
    }
    
    $t = break_into_paragraphs($t); 
    
    // fix things that cannot go inside <p> by html rules
    $t = mb_ereg_replace('<p>\\\hfill\\s*','<p class="flushright">',$t);
    $t = mb_ereg_replace('\\\hfill','<!-- flushright -->',$t);
    $t = mb_ereg_replace('<p>\\\noindent\\s*','<p class="openingpar">',$t);
    $t = mb_ereg_replace('<p><ul','<ul',$t);
    $t = mb_ereg_replace('</ul></p>','</ul>',$t);
    $t = mb_ereg_replace('<p><div class="centered">','<div class="centered">',$t);
    $t = mb_ereg_replace('</div></p>','</div>',$t);
        
    return $t;
}

function html_russells_intro() {
    
    $intro_tex = file_get_contents(dirname(__FILE__) . '/tlp_russells_intro.tex') ?? '[Could not read Russell’s intro file.]';
    
    $intro_html = tex_to_html($intro_tex,false);
    
    return '<p class="openingpar">' . mb_substr($intro_html, 3);
}

function html_index_note() {
    
    $note_tex = file_get_contents(dirname(__FILE__) . '/index_note.tex') ?? '[Could not read index note file.]';
    
    $note_html = tex_to_html($note_tex,false);
    
    return $note_html;
}

function html_index_link_for($t) {
    
    global $columns_mode;
    
    
    if (mb_ereg_match('.*–', $t)) {
        $parts = explode('–',$t,2);
        return html_index_link_for($parts[0]) . '–' . html_index_link_for($parts[1]);
    }
    
    if (!($columns_mode)) {
        return $t . html_link_array_for($t, 'index', false);
    }
    
    $anchor = $t;
    if (substr($anchor, 0, 1) == 'P') {
        $anchor = str_replace('P','#pref',$anchor);
    } else {
        $anchor = '#p' . $anchor;
    }
    $l = '<a href="' . $anchor . '">' . $t . '</a>';
    return $l;
}

function html_index() {
        
    $entries = json_decode(file_get_contents(dirname(__FILE__) . '/tlp_index.json')) ?? 'ERROR';
    
    if ($entries==='ERROR') {
        return('ERROR: Could not read index JSON file.');
    }
    
    $r='<div id="indexentries">' . PHP_EOL;
    
    $start_letter = 'a';
    
    $r.='<div class="indexletterblock">' . PHP_EOL;
    
    foreach($entries as $e) {
        // check if new first letter
        if (!($e->isSubEntry)) {
            if ( substr($e->entryname, 0, 8) == '\\textit{' ) {
                $this_first_letter = strtolower( substr($e->entryname, 8, 1) );
            } else {
                $this_first_letter = strtolower( substr($e->entryname, 0, 1) );
            }
            if ($this_first_letter != $start_letter) {
                $start_letter = $this_first_letter;
                $r .= '</div>' . PHP_EOL . PHP_EOL;
                $r .= '<div class="indexletterblock">' . PHP_EOL;
            }
        }
        
        // type of entry
        if ($e->isSubSubEntry) {
            $r .= '        <div class="indexsubsubentry">';
        } else {
            if ($e->isSubEntry) {
                $r .= '    <div class="indexsubentry">';
            } else {
                $r .= '<div class="indexentry">';
            }
        }

        $r .= tex_to_html($e->entryname,false);
        if ((isset($e->cf)) || (isset($e->refs))) {
            $r .= ', ';
        }
        if (isset($e->cf)) {
            $r .= 'cf. ' . tex_to_html($e->cf,false) . '.';
        }

        if (isset($e->refs)) {
            for ($i=0; $i<count($e->refs); $i++) {
                $ref = $e->refs[$i];
                $r .= html_index_link_for($ref->target);
                if (isset($ref->aftertext)) {
                    if (!(substr($ref->aftertext, 0, 1) == ';')) {
                        $r .= ' ';
                    }
                    $r .= tex_to_html($ref->aftertext, false) . ' ';
                } else {
                    if (($i+1) != count($e->refs)) {
                        $r .= ', ';
                    }
                }
            }
        }
        
        $r .= '</div>' . PHP_EOL; // end of entry
        
    }
    $r .= '</div>' . PHP_EOL . PHP_EOL; // end of last letter block
    
    
    $r.='</div>' . PHP_EOL; // end of indexentries block
    return $r;
}

function exit_with_error($s) {
    fwrite(STDERR, $s . PHP_EOL);
    exit(1); 
}

?>
