# Wittgenstein's *Tractatus Logico-Philosophicus* #

## Side-by-side-by-side Edition master text files ##

If you are looking for the *finished product*, please visit the project page for the [Side-by-Side-by-Side Edition of Wittgenstein's *Tractatus*](http://people.umass.edu/klement/tlp/).

This repository is for the master upstream text source files and processing scripts code.

### The important files in this repository ###

#### 1. tlp_latex.json ####

This is JSON file which represents the TLP as an object in which each "proposition number" is mapped to a sub-object with three properties, "German", "Ogden" and "Pears and McGuinness", each of which is an array containing the text, using LaTeX mark-up, of the successive paragraphs of that proposition/section number, in German, or in one of the two translations. E.g.:

~~~
    ...
    "7": {
        "German": [
            "Wovon man nicht sprechen kann, darüber muss man schweigen."
        ],
        "Ogden": [
            "Whereof one cannot speak, thereof one must be silent."
        ],
        "PearsMcGuinness": [
            "What we cannot speak about we must pass over in silence."
        ]
    }
    ...
~~~

The paragraphs of the preface come first and are given designations such a "P1" for paragraph one of the preface, matching the conventions in the index.

Note that the values use custom LaTeX commands defined in the file `tlp_latex_custom_commands.tex`, and so this file, or a replacement for it, must be included one way or another in order for the LaTeX code to be used.

This is the "master" text file, and future alterations to the text should be made to this file to propagate to other versions.

#### 2. create_tlp_latex_version.php ####

This is a PHP script which uses the JSON file mentioned above and writes a complete regular LaTeX file to stdout. The output can be redirected to a file as such:

`$ php create_tlp_latex_version.php > tlp.tex`

Or it can be piped directly to `pdflatex` or similar to create a PDF file:

`$ php create_tlp_latex_version.php | pdflatex -jobname tlp`

This script can optionally take a single argument. This argument should be the name of a `.json` file which species settings for the output document. For example, to create the ebook version:

`$ php create_tlp_latex_version.php ebook_latex_settings.json | pdflatex -jobname tlp-ebook`

(If no option is given, it uses `default_latex_settings.json`.)

This script was written for PHP 7.2+; I do not know whether or not is compatible with earlier PHP versions.

#### 3., 4., 5. default_latex_settings.json, ebook_latex_settings.json, hierarchy_version_settings.json ####

These are JSON settings files that are used to create the PDF versions hosted on the project's main web page. See one of the files for the precise format. Here are some of the options.

1. **passes**:  *Array.* Each element of the array represents a part of the text which is to be typeset in one pass-through.  They each have four sub-options:

    a. **minDepth**: *Integer.* The minimum number of digits after the decimal place a given proposition/remark must have to be included in the pass. The usual value here would be 0, unless you only want to include remarks, but not what they are remarks about.
    
    b. **maxDepth**: *Integer.* Like mindepth, but the maximum number of digits. Possible values here are 0 through 5.
    
    c. **startProposition**: *String.* Where in the text to start the pass; the usual value is "1". Note that this value is a string, not a float, and should be in quotation marks in the JSON.
    
    d. **endProposition**: *String.* Where in the text to finish the pass; the usual value is "7". Also a string.

2. **useDepthMarkers**:  *Boolean.* If true, horizontal lines will be printed between passes showing what depth range is included in the pass to follow.

3. **includeCoverPage**: *Boolean.* Whether or not to include a cover page in the resulting PDF.

4. **includeDedicationPage**: *Boolean.* Whether or not to include the dedication page in the resulting PDF.

5. **includeRussellsIntro**: *Boolean.* Whether or not to include Russell's Introduction.

6. **columnsForRussellsIntro**: *Integer.* Number of columns to typeset Russell's Introduction.

7. **includePreface**: *Boolean.* Whether or not to include Wittgenstein's preface.

8. **includeGerman**, **includeOgden**, **includePearsMcGuinness**: *Booleans.* Set to false to exclude the German, or one or both translation.

9. **includeIndex**: *Boolean.* Whether or not to include the index from the Pears/McGuinness translation. Note that if the version has multiple passes, the Index will hyperlink only to the last pass.

10. **columnsForIndex**: *Integer.* Number of columns for typsetting the index.

11. **includeLicsenInfo**: *Boolean.* Whether or not to include a short statement of the licenses of the text, the typesetting, and a link to the project page.

12. **useBookCoverImage**: *Boolean.* If true, and a cover page is used, creates a graphical cover as the first page of the PDF.

13. **multicolumnLayout**: *Boolean.* If true, places the translations side-by-side(-by-side) on the same page; if false, typesets them on their own pages and creates hyperlinks between them (as in the ebook version).

14. **ruleBetweenColumns**: *Boolean.* If true, places vertical lines between the columns in a multi-column layout.

15. **distanceBetweenColumns**: *LaTeX length string.* Sets the spacing in between columns in a multo-column layout.

16. **papersize**: *String.* Sets the dimensions of the PDF page. You can use a description such as "letterpaper" or "a4paper" (see the documentation for the LaTeX `geometry` package for a list), or dimensions included in curly braces for the width and height, e.g., "{8.5in,11in}".

17. **landscape**: *Boolean.* Whether or not to set the PDF page in landscape mode.

18. **margin**: *LaTeX length string.* Sets the size of margins around the PDF pages.

19. **fontcommand**: *String.* Can contain arbitrary LaTeX preamble commands meant for choosing font packages, or making other aesthetic changes. Various options can be found at the [LaTeX font catalogue](http://www.tug.dk/FontCatalogue/). Remember to escape backslashes as `\\` in the JSON.

The use of multiple passes is typically used to represent different stages in the "hierarchy" of TLP. This array may have only a single element if you want to typeset the entire book in one pass, or just a single stage in the hierarchy.

You can of course create additional settings files of your own to create new custom versions.

#### 6., 7., 8. tlp_index.json, index_note.tex, tlp_russells_intro.tex ###

These are other "master" files containing ancillary text (with content suggested by their filenames), and are sometimes required by `create_tlp_latex_version.php` depending on settings.

#### 9. tlp_latex_custom_commands.tex ####

A LaTeX fragment in which various LaTeX commands used in the values inside tlp_latex.json are defined.

#### 10. libtlp.php ####

A library of PHP functions used by `create_tlp_latex_version.php`, and perhaps eventually, other scripts related to the project.

#### 11. cover.jpg ####

The image used as a cover if `useBookCoverImage` is set to `true`;

### To Do ###

Coming, hopefully soon, will be a scripts for converting the LaTeX-based JSON to HTML-based JSON, and other scripts for creating HTML-based and ePub versions.

### License ###


Wittgenstein's book is in the Public Domain.

This layout is released with a Create Commons ShareAlike-Attribution 3.0 license.

The code in the scripts is GPLv3.




Kevin C. Klement [klement@umass.edu](mailto:klement@umass.edu)