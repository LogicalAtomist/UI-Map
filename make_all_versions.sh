#!/bin/bash

if [[ ! -e "$HOME/bin/kckcolor.sh" ]] ; then
    echo "Sorry this script is meant for Kevin’s use only." > /dev/stderr
    exit 1
fi

source "$HOME/bin/kckcolor.sh"

TEMPDIR="$(mktemp -d)"

function exit_nicely {
    cd "$HOME"
    rm -rf "$TEMPDIR"
    exitwitherror 1 "$1" 
}

# find the folder I should be in
SCRIPTLOC="$(readlink -f "$0")"
SCRIPTDIR="$(dirname "$SCRIPTLOC")"
cd "$SCRIPTDIR"

OUTPUTDIR="$SCRIPTDIR/output"

if [[ ! -d "$OUTPUTDIR" ]] ; then
    echoyellow "Creating output folder."
    mkdir -p "$OUTPUTDIR"
fi

echoyellow "Creating LaTeX versions source code."
php create_tlp_latex_version.php > "$TEMPDIR/tlp.tex"
php create_tlp_latex_version.php ebook_latex_settings.json > "$TEMPDIR/tlp-ebook.tex"
php create_tlp_latex_version.php hierarchy_version_settings.json > "$TEMPDIR/tlp-hierarchy.tex"

echoyellow "Converting LaTeX-based JSON to HTML based JSON."
php json_tex2html.php > "$TEMPDIR/tlp_html.json"

# check if new text is available
if ! diff -q "tlp_html.json" "$TEMPDIR/tlp_html.json" ; then
    echored "replacing tlp_html.json with $TEMPDIR/tlp_html.json";
    cp "$TEMPDIR/tlp_html.json" "tlp_html.json"
fi

echoyellow "Creating HTML sources."
php create_tlp_html_version.php > "$TEMPDIR/tlp.html"
php create_tlp_html_version.php nocolumns > "$TEMPDIR/tlp-hyperlinked.html"
php create_tlp_html_version.php "nocolumns,epub" > "$TEMPDIR/tlp-epubsource.html"

echoyellow "Copying resources to temporary folder."
cp cover.jpg "$TEMPDIR"
cp -r images "$TEMPDIR"
cd "$TEMPDIR"

echoyellow "Running pdfLaTeX..."
echo "Compiling columned LaTeX to PDF."
pdflatex "tlp.tex" > /dev/null || exit_nicely "Compilation failed."
echo "Re-compiling columned LaTeX to PDF."
pdflatex "tlp.tex" > /dev/null || exit_nicely "Compilation failed."

echo "Compiling small page LaTeX to PDF."
pdflatex "tlp-ebook.tex" > /dev/null || exit_nicely "Compilation failed."
echo "Re-compiling small page LaTeX to PDF."
pdflatex "tlp-ebook.tex" > /dev/null || exit_nicely "Compilation failed."

echo "Compiling hierarchy LaTeX to PDF."
pdflatex "tlp-hierarchy.tex" > /dev/null || exit_nicely "Compilation failed."
echo "Re-compiling hierarchy LaTeX to PDF."
pdflatex "tlp-hierarchy.tex" > /dev/null || exit_nicely "Compilation failed."

echoyellow "Creating ePub with calibre. (This may take awhile.)"
ebook-convert "tlp-epubsource.html" "tlp.epub" --cover cover.jpg --disable-remove-fake-margins || exit_nicely "Conversion failed."

echoyellow "Copying to output folder."
cp tlp.pdf tlp-ebook.pdf tlp-hierarchy.pdf tlp.epub tlp.html tlp-hyperlinked.html "$OUTPUTDIR" || exit_nicely "Copying failed."

cd "$HOME"
rm -rf "$TEMPDIR"
exit 0