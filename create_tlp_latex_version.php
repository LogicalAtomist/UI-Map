<?php

// create_tlp_latex_version.php. License: GPLv3

require('libtlp.php');

function exit_with_error($s) {
    fwrite(STDERR, $s . PHP_EOL);
    exit(1); 
}

// set default location for settings file
$settings_file = dirname(__FILE__) . '/default_latex_settings.json';

// read alternative settings file from command line
if (isset($argv[1])) {
    $settings_file = $argv[1];
}

// ensure settings file exists
if (!(file_exists($settings_file))) {
    exit_with_error('Settings file ' . $settings_file . ' not found.');
}

// read settings
$settings = json_decode(file_get_contents($settings_file));

// ensure json parsing worked correctly
if (!(isset($settings->passes))) {
    exit_with_error('Settings file ' . $settings_file . ' could not be properly parsed.');
}
$settings->fnDone = false;
$settings->isLastPass = true;
$settings->currPassNum = 0;

// read TLP json file

$tlp = json_decode(file_get_contents(dirname(__FILE__) . '/tlp_latex.json'));

if (!(isset($tlp->P1))) {
    exit_with_error('JSON file with LaTeX markup could not be properly parsed.');
}

?>
%
% Created by create_tlp_latex_version.php script
% <?php echo date('d M Y H:i:s') . PHP_EOL; ?>
%
% Settings given in <?php
    echo $settings_file . PHP_EOL;
    $settings_text = json_encode($settings, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);
    foreach (explode(PHP_EOL, $settings_text) as $line) {
        echo '% ' . $line . PHP_EOL;
    }
?>
\documentclass[oneside,openany,12pt]{book}
%
% For tracking changes
\newcommand{\version}{<?php echo get_version_num(); ?>}
%
% For unicode input
\usepackage[utf8]{inputenc}
%
% For English and German hyphenation patterns
\usepackage[german,english]{babel}
%
% Sets page dimensions and orientation:
\usepackage[<?php

if (substr($settings->papersize,0,1) == '{') {
    echo 'papersize=' . $settings->papersize . ',';
} else {
    echo 'paper=' . $settings->papersize . ',';
}

if ($settings->landscape) {
    echo 'landscape,';
}

echo 'margin=' . $settings->margin;

?>]{geometry}
%
% Font commands 
<?php echo $settings->fontcommand . PHP_EOL; ?>
%
% For the columns of Russell's intro
\usepackage{multicol}
\usepackage{enumitem}
%
% For the basic column-based set-up of the book:
\usepackage{parcolumns}
%
% For greater control over lines in truth-tables:
\usepackage{hhline}
%
% Used for s p a c e d German emphasis:
\usepackage{soulutf8}
%
% For greater math options:
\usepackage{amsmath}
%
% For hyphens in pseudo-proposition!
\usepackage{hyphenat}
%
% Improves typography in cramped quarters:
\usepackage[kerning=true]{microtype}
%
% Used to determine column widths
\usepackage{calc}
%
% Creative commons icons:
\usepackage{ccicons}
%
<?php if (!($settings->multicolumnLayout)) { ?>
%
% table of contents stuff
\usepackage{tocloft}
\tocloftpagestyle{empty}
\setlength{\cftbeforetoctitleskip}{0pt}
\setlength{\cftaftertoctitleskip}{4pt}
\renewcommand{\cfttoctitlefont}{\Large\bfseries}
\setlength{\cftbeforechapskip}{0.7\baselineskip}
<?php } ?>
%
% For graphics:
\usepackage{tikz}
\usetikzlibrary{arrows}
%
<?php if ($settings->useBookCoverImage) { ?>
% for cover image
\usepackage{eso-pic}
<?php } ?>
%
\usepackage{hyperref}
\hypersetup{
   pdfauthor={Ludwig Wittgenstein},%
   pdftitle={Tractatus Logico-Philosophicus},%
   pdfsubject={Philosophy,Logic},%
   colorlinks,%
   linkcolor=blue
}
%
\title{Tractatus Logico-Philosophicus}
\author{Ludwig Wittgenstein}
\date{1922}
<?php 

// insert file with custom latex commands
readfile(dirname(__FILE__) . '/tlp_latex_custom_commands.tex');

// commands for index, if needed
if ($settings->includeIndex) { ?>
% Formatting of the index
%
% Sets up indents for index
\setitemize[1]{label={},leftmargin=\parindent,itemindent=-1\parindent,nolistsep}
\setitemize[2]{label={},leftmargin=\parindent,itemindent=-1.5\parindent,nolistsep}
\setitemize[3]{label={},leftmargin=\parindent,itemindent=0pt,nolistsep}
%
% Codes for index entries
\newcommand{\indexentry}[1]{\item #1}
\newcommand{\indexsubentry}[1]{\begin{itemize} \item #1 \end{itemize}}
\newcommand{\indexsubsubentry}[1]{\begin{itemize} \item \begin{itemize} \item #1 \end{itemize} \end{itemize}}
\newcommand{\indexgap}{\bigskip}
<?php } ?>
%
\begin{document}
% no headers or footers at first
\pagestyle{empty}%
% sloppy mode reduces badboxes in tight quarters
\sloppy%
% zeroout to help with math spacing in chunks
\zeroout%
% get rid of stretch between paragraphs
\setlength{\parskip}{0pt}%
% more hyphens
\global\hyphenpenalty=100%
\hyphenation{pseu-do-prop-o-si-tion pseu-do-prop-o-si-tions}%
<?php if ($settings->includeCoverPage) { ?>
\begin{titlepage}
<?php if ($settings->useBookCoverImage) { ?>
\pdfbookmark{Cover page}{cp}\AddToShipoutPicture*{\put(0,0){\includegraphics*[scale=0.45,trim=8 40 0 0]{cover}}}% 
\phantom{Cover image} 
\newpage 
<?php } ?>
\begin{center}
\phantom{x}

\vfill

\vfill

\begin{Huge}
Tractatus Logico-Philosophicus\par
\end{Huge}

\bigskip
\begin{LARGE}
Logisch-philosophische Abhandlung\par
\end{LARGE}

\bigskip
\begin{Large}
\textit{By Ludwig Wittgenstein}\par
\end{Large}

\vfill

\vfill


{First published by Kegan Paul (London), 1922.}

\medskip
\textsc{Side-by-side-by-side edition, version \version\ (\today),}\\
containing the original German, alongside both the Ogden/Ramsey, and Pears/McGuinness English translations.

Available at: \url{http://people.umass.edu/klement/tlp/}

\vfill

\phantom{x}
\end{center}
\end{titlepage}
\clearpage%
\pagestyle{empty}%
<?php } // end coverpage ?>
<?php if (!($settings->multicolumnLayout)) { ?>
\phantomsection%
\tableofcontents
<?php } ?>
<?php if ($settings->includeRussellsIntro) { ?>
%=============================================
% RUSSELL'S INTRODUCTION
%=============================================
\clearpage\phantomsection\addcontentsline{toc}{chapter}{Introduction (by Bertrand Russell)}%
<?php if ($settings->columnsForRussellsIntro >= 2) { ?>
\begin{multicols}{<?php echo $settings->columnsForRussellsIntro; ?>}[\section*{Introduction}By Bertrand Russell, F.\,R.\,S.]
<?php } else { ?>
\section*{Introduction}By Bertrand Russell, F.\,R.\,S.

\medskip

<?php } ?>
\selectlanguage{english}\noindent% 
<?php 
readfile(dirname(__FILE__) . '/tlp_russells_intro.tex');
if ($settings->columnsForRussellsIntro >= 2) {
?>
\end{multicols}
<?php } ?>
<?php } //end Russell intro ?>
\clearpage%
<?php if ($settings->includeDedicationPage) { ?>
\thispagestyle{empty}\phantomsection\addcontentsline{toc}{chapter}{Dedication page}\thispagestyle{empty}%
\begin{center}
\phantom{x}

\vfill

\begin{Huge}
Tractatus Logico-Philosophicus\par
\end{Huge}

\vfill

\vfill

{\textsc{Dedicated}}\\
{\textsc{to the Memory of My Friend}}

\medskip
\begin{Large}
\textsc{David H. Pinsent}\par
\end{Large}

\vfill

\vfill

{\germph{Motto}: \ldots und alles, was man weiss, nicht bloss rauschen und brausen geh{\"o}rt hat, l{\"a}sst sich in drei Worten sagen.}
{\quad\textsc{--K{\"u}rnberger.}}

\vfill

\phantom{x}
\end{center}
\clearpage%
<?php } //end dedication page ?>
<?php 

// CORE OF BOOK HANDLED HERE

if ($settings->multicolumnLayout) {
    multicolumn_version();
} else {
    if ($settings->includeGerman) {
        german_standalone_version();
    }
    if ($settings->includeOgden) {
        ogden_standalone_version();
    }
    if ($settings->includePearsMcGuinness) {
        pmc_standalone_version();
    }
}

    
// end CORE ?>
<?php if ($settings->includeIndex) { ?>
\clearpage%
\phantomsection\addcontentsline{toc}{chapter}{Index}%
<?php if ($settings->ruleBetweenColumns) { ?>
\setlength{\columnseprule}{0.5pt}
<?php } 
        if ($settings->columnsForIndex >= 2) { ?>
\begin{multicols}{<?php echo $settings->columnsForIndex; ?>}[\section*{Index (Pears/McGuinness)}]
<?php } else { ?>
\section*{Index (Pears/McGuinness)}
<?php } ?>
<?php readfile(dirname(__FILE__) . '/index_note.tex'); ?>

\bigskip

\bigskip

\begin{itemize}
\raggedright

<?php insert_index(); ?>

\end{itemize}

<?php  if ($settings->columnsForIndex >= 2) { ?>
\end{multicols}
<?php } ?>
<?php } // end of index ?>
<?php if ($settings->includeLicenseInfo) { ?>
\phantomsection\addcontentsline{toc}{chapter}{Edition notes}\bigskip

\raggedright
\noindent\hrulefill

\phantom{xx}

\noindent {\Huge \ccPublicDomainAlt}\ Ludwig Wittgenstein's \textit{Tractatus Logico-Philosophicus} is in the \textbf{Public Domain}.

\noindent See \url{http://creativecommons.org/licenses/publicdomain/}


\bigskip

\noindent {\Huge \ccLogo\ccAttribution\ccShareAlike}\ This typesetting (including \LaTeX\ code), by Kevin C.\ Klement, is licensed under a \textbf{Creative Commons Attribution---Share Alike 3.0 United States License}.

\noindent See \url{http://creativecommons.org/licenses/by-sa/3.0/us/}

<?php if ($settings->useBookCoverImage) { ?>

\bigskip 
        
The cover includes the photo “Ladders” by dev null, licensed under a Creative Commons Attribution Non-Commercial Share-Alike 2.0 License.

<?php } ?>

\bigskip

\noindent Latest version available at: \url{http://people.umass.edu/klement/tlp/}
<?php } ?>
\end{document}
