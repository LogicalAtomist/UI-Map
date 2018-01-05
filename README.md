# Wittgenstein's *Tractatus Logico-Philosophicus* #

## Side-by-side-by-side edition master text files ##

If you are looking for the *finished product*, please visit the project page for the [Side-by-Side-by-Side edition of Wittgenstein's *Tractatus*](http://people.umass.edu/klement/tlp/).

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

The paragraphs of the preface come first and are given designations such a "P1" for paragraph one of the preface, matching the conventions in Pears and McGuinness's index.

This is the "master" text file, and alterations to the text should be made to this file.

#### 2. create_tlp_latex_version.php ####

This is a PHP script which uses the JSON file mentioned above and writes a complete regular LaTeX file to stdout. The output can be redirected to a file as such:

`$ php create_tlp_latex_version.php > tlp.tex`

Or it can be piped directly to `pdflatex` or similar to create a PDF file:

`$ php create_tlp_latex_version.php | pdflatex -jobname tlp`

This script can optionally take a single argument. This argument should be the name of a `.json` file which species settings for the output document. For example, to create the ebook version:

`$ php create_tlp_latex_version.php ebook_latex_settings.json | pdflatex -jobname tlp-ebook`

(If no option is given, it uses `default_latex_settings.json`.)

#### 3., 4., 5. default_latex_settings.json, ebook_latex_settings.json, hierarchy_version_settings.json ####

These are settings files that are used to create the PDF versions hosted on the pr

### License ###

| Wittgenstein's book is in the Public Domain.
| This layout is released with a Create Commons ShareAlike-Attribution 3.0 license.
| The code in the scripts is GPLv3.
|
| Kevin C. Klement [klement@umass.edu](mailto:klement@umass.edu)