#!/bin/bash

if [[ ! -e "$HOME/bin/kckcolor.sh" ]] ; then
    echo "Sorry this script is meant for Kevin’s use only." > /dev/stderr
    exit 1
fi

source "$HOME/bin/kckcolor.sh"

TEMPDIR="$(mktemp -d)"

function exit_nicely {
    rm -rf "$TEMPDIR"
    exitwitherror 1 "$1" 
}

# find the folder I should be in
SCRIPTLOC="$(readlink -f "$0")"
SCRIPTDIR="$(dirname "$SCRIPTLOC")"
cd "$SCRIPTDIR"

echoyellow "Creating LaTeX versions source code."
php create_tlp_latex_version.php > "$TEMPDIR/tlp.tex"
php create_tlp_latex_version.php ebook_latex_settings.json > "$TEMPDIR/tlp-ebook.tex"
php create_tlp_latex_version.php hiearchy_version_settings.json > "$TEMPDIR/tlp-hierarchy.tex"

echoyellow "Converting LaTeX-based JSON to 

cd "$HOME"
rm -rf "$TEMPDIR"
exit 0