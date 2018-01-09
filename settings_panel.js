
window.settings={};

function creAdd(tagType, parNode, styclasses, idToUse) {
    if (typeof styclasses === "undefined") styclasses = [];
    if (typeof idToUse === "undefined") idToUse = "none";
    var newNode = document.createElement(tagType);
    parNode.appendChild(newNode);
    if (idToUse != 'none') {
        newNode.id = idToUse;
    }
    for (var i = 0; i < styclasses.length; i++) {
        newNode.classList.add(styclasses[i]);
    }
    return newNode;
}

function isMultiCol() {
    if (document.getElementById("bookcore")) {
        return true;
    }
    return false;
}

function defaultMarg() {
    if (isMultiCol()) {
        return 0;
    }
    return goodMarg();
}

function goodMarg() {
    var width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
    if (width <= 600) {
        return 0;
    }
    var extraSpace = (width - 600);
    return Math.floor(extraSpace / 2);
}

function resetSettings() {
    window.settings.coverPageCB.checked = true;
    window.settings.tocCB.checked = true;
    window.settings.introCB.checked = true;
    window.settings.dedicationCB.checked = true;
    window.settings.prefaceCB.checked = true;
    window.settings.indexCB.checked = true;
    window.settings.licenseCB.checked = true;
    window.settings.settingsCB.checked = true;
    window.settings.germanCB.checked = true;
    window.settings.ogdenCB.checked = true;
    window.settings.pmcCB.checked = true;
    window.settings.minDepth.value = 0;
    window.settings.maxDepth.value = 5;
    window.settings.fontSelector.selectedIndex = 0;
    window.settings.fontSizeInput.value = 16;
    window.settings.fontColorInput.value = '#000000';
    window.settings.alignSelect.selectedIndex = 0;
    window.settings.spacingSelect.value = 1.2;
    window.settings.bgColorInput.value = '#ffffff';
    if (isMultiCol()) {
        window.settings.gerColorInput.value = '#ffffe0';
        window.settings.ogdColorInput.value = '#f0f8ff';
        window.settings.pmcColorInput.value = '#fbbbb9';
    } else {
        window.settings.gerColorInput.value = '#ffffff';
        window.settings.ogdColorInput.value = '#ffffff';
        window.settings.pmcColorInput.value = '#ffffff';
    }
    window.settings.wholePageMargInput.value = defaultMarg();
    window.settings.extraMargInput.value = goodMarg();
}

function colorTheseClasses(c, a) {
    for (var i=0; i<a.length; i++) {
        var ee = document.getElementsByClassName(a[i]);
        for (var j=0; j<ee.length; j++) {
            ee[j].style.backgroundColor = c;
        }
    }
}

function unHideTheseClasses(a) {
    for (var i=0; i<a.length; i++) {
        var ee = document.getElementsByClassName(a[i]);
        for (var j=0; j<ee.length; j++) {
            if (ee[j].style.display == 'none') {
                ee[j].style.display = null;
            }
        }
    }
}


function hideTheseClasses(a) {
    for (var i=0; i<a.length; i++) {
        var ee = document.getElementsByClassName(a[i]);
        for (var j=0; j<ee.length; j++) {
            ee[j].style.display = 'none';
        }
    }
}


function applySettings() {
    
    document.body.style.fontFamily = window.settings.fontSelector.value;
    document.body.style.fontSize = window.settings.fontSizeInput.value + 'px';
    document.body.style.color = window.settings.fontColorInput.value;
    document.body.style.backgroundColor = window.settings.bgColorInput.value;
    document.body.style.textAlign = window.settings.alignSelect.value;
    document.body.style.lineHeight = window.settings.spacingSelect.value;
    
    document.body.style.marginLeft = window.settings.wholePageMargInput.value + 'px';
    document.body.style.marginRight = window.settings.wholePageMargInput.value + 'px';
    
    var exm = 0;
    var a = parseInt(window.settings.extraMargInput.value);
    var b = parseInt(window.settings.wholePageMargInput.value)
    if (a > b) {
        exm = (a - b);
    }
    var ri = document.body.getElementsByClassName("russellsintro");
    for (var i=0; i<ri.length; i++) {
        ri[i].style.marginLeft = exm + 'px';
        ri[i].style.marginRight = exm + 'px';
    }
    var ri = document.body.getElementsByClassName("indexdiv");
    for (var i=0; i<ri.length; i++) {
        ri[i].style.marginLeft = exm + 'px';
        ri[i].style.marginRight = exm + 'px';
    }
    
    colorTheseClasses(
        window.settings.gerColorInput.value,
        ["ger","gerpref","bigdivGerman"]
    );
    colorTheseClasses(
        window.settings.ogdColorInput.value,
        ["ogd","ogdpref","bigdivOgden"]
    );
    colorTheseClasses(
        window.settings.pmcColorInput.value,
        ["pmc","pmcpref","bigdivPearsMcGuinness"]
    );
    
    var toHide = [];
    var toUnHide = [];
    var toCheck = [
        [window.settings.coverPageCB.checked, ["coverpageDiv"]],
        [window.settings.tocCB.checked, ["contentsDiv"]],
        [window.settings.introCB.checked, ["russellsintro","introlink"]],
        [window.settings.dedicationCB.checked, ["dedicationDiv","dedlink"]],
        [window.settings.prefaceCB.checked, ["prefacediv","preflink"]],
        [window.settings.indexCB.checked, ["indexdiv","indextoc"]],
        [window.settings.licenseCB.checked, ["licenseDiv"]],
        [window.settings.settingsCB.checked, ["settingsbuttonbg"]],
        [window.settings.germanCB.checked, ["ger","gerpref","bigdivGerman","gerlink","gertoc","aftergerlink","gerhdr"]],
        [window.settings.ogdenCB.checked, ["ogd","ogdpref","bigdivOgden","ogdlink","ogdtoc","ogdhdr", "ogdnote"]],
        [window.settings.pmcCB.checked, ["pmc","pmcpref","bigdivPearsMcGuinness","pmclink","beforepmclink","pmctoc","pmchdr"]]
    ];
    for (var x=0; x<toCheck.length; x++) {
        if (toCheck[x][0]) {
            toUnHide = toUnHide.concat(toCheck[x][1]);
        } else {
            toHide = toHide.concat(toCheck[x][1]);
        }
    }
    unHideTheseClasses(toUnHide);
    hideTheseClasses(toHide);
    
    closeSettingsPanel();
}

function closeSettingsPanel() {
    document.body.removeChild(window.settings.bgBox);
}

function openSettingsPanel() {
    document.body.appendChild(window.settings.bgBox);
}

function createSettingsPanel() {
    window.settings.bgBox = creAdd("div", document.body, ["settingsbg"]);
    window.settings.settingsBox = creAdd("div", window.settings.bgBox, ["settingsbox"]);
    window.settings.settingsInnerBox = creAdd("div", window.settings.settingsBox, ["innersettingsbox"]);
    var h = creAdd("h4", window.settings.settingsInnerBox);
    h.innerHTML = "Settings";
    var d = creAdd("fieldset", window.settings.settingsInnerBox);

    // content options
    d.innerHTML = '<legend>Content options:</legend>';
    d.getcb = function(opt,id) {
        var idiv = creAdd("div",this,["idiv"]);
        var cb = creAdd("input",idiv,[],"cb-" + id);
        cb.type = "checkbox";
        var l = creAdd("label",idiv);
        l.htmlFor = "cb-" + id;
        l.innerHTML = opt;
        l.classList.add("cblabel");
        return cb;
    }
    d.getslider = function(opt,id,min,max) {
        var idiv = creAdd("div",this,["idiv"]);
        var l= creAdd("div", idiv);
        l.innerHTML = opt;
        l.htmlFor = "sl" + id;
        var t1 = creAdd("span",idiv);
        t1.innerHTML = '(' + min + ')';
        var sl = creAdd("input",idiv,[],"sl-" + id);
        sl.type = "range";
        sl.min = min;
        sl.max = max;
        var t2 = creAdd("span",idiv);
        t2.innerHTML = '(' + max + ') ';
        return sl;
    }
    window.settings.coverPageCB = d.getcb("cover page","cover");
    window.settings.tocCB = d.getcb("TOC","toc");
    window.settings.introCB = d.getcb("Russell’s introduction","intro");
    window.settings.dedicationCB = d.getcb("dedication page","dedication");
    window.settings.prefaceCB = d.getcb("preface","pref");
    window.settings.indexCB = d.getcb("index","ind");
    window.settings.licenseCB = d.getcb("license info","lic");
    window.settings.settingsCB = d.getcb("settings button","setb");
    var b = creAdd("br", d);
    window.settings.germanCB = d.getcb("German text","ger");
    window.settings.ogdenCB = d.getcb("Ogden translation","ogd");
    window.settings.pmcCB = d.getcb("Pears/McGuinness translation","pm");
    var b = creAdd("br", d);
    window.settings.minDepth = d.getslider("Minimum TLP remark depth (decimal places)","mindepth",0,5);
    var b = creAdd("br", d);
    window.settings.maxDepth = d.getslider("Maximum TLP remark depth (decimal places)","maxdepth",0,5);
    var b = creAdd("br", d);

    // appearance options
    var d = creAdd("fieldset", window.settings.settingsInnerBox);
    d.innerHTML = '<legend>Appearance options:</legend>';

    var idiv = creAdd("div",d,["idiv"]);
    window.settings.fontSelector = creAdd("select",idiv,[],"fontselector");
    window.settings.fontSelector.innerHTML = '<option value="serif" selected="selected">Default Serif</option>' +
        '<option value="sans-serif">Default Sans</option>' +
        '<option value="monospace">Default Monospace</option>' +   
        '<option value="\'Comic Sans MS\', cursive">Comic Sans</option>' +
        '<option value="\'Courier New\', \'Courier Std\', \'Courier 10 Pitch\', Courier, monospace, Monospace">Courier</option>' +
        '<option value="\'Adobe Garamond Pro\', \'Garamond Premier Pro\', Garamond, \'EB Garamond\', \'Cormorant Garamond\', \'ITC Garamond Std\', \'Garamond 3 LT Std\', \'Stempel Garamond LT Std\', \'Simoncini Garamond Std\', \'URW Garamond\'">Garamond</option>' +
        '<option value="Georgia, serif, Serif">Georgia</option>' +
        '<option value="FreeSans, \'TeX Gyre Heros\', \'Nimbus Sans L\', \'Helvetica LT Std\', \'Helvetica Neue LT Pro\', \'Helvetica Neue LT Std\', \'Helvetica Neue\', Helvetica, Arial, Arimo, sans-serif, Sans">Helvetica / Arial</option>' +
        '<option value="\'Impact LT Std\',Impact, Oswald, Charcoal, sans-serif">Impact</option>' +
        '<option value="\'Lucida Console\', Monaco, \'Lucida Sans Typewriter Std\', monospace, Monospace">Lucida Console / Monaco</option>' +
        '<option value="\'Lucida Sans Unicode\', \'Lucida Sans Std\', \'Lucida Grande\'">Lucida Sans</option>  ' +
        '<option value="\'Palatino Linotype\', \'Palatino LT Std\', \'Book Antiqua\', Palatino, \'URW Palladio L\', \'TeX Gyre Pagella\', serif, Serif">Palatino</option>' +
        '<option value="Tahoma, Geneva, sans-serif, Sans">Tahoma / Geneva</option>' +
        '<option value="\'Times New Roman\', Times, \'Times New Roman MT Std\', \'Times Ten LT Std\', \'Times LT Std\', \'TeX Gyre Termes\', Tinos, serif, Serif">Times</option>' +
        '<option value="\'Trebuchet MS\', \'Gill Sans Std\', \'Gill Sans MT Pro\', \'Gillius ADF\', \'Frutiger LT Std\', \'Grotesque MT Std\', sans-serif, Sans">Trebuchet MS</option>' +
        '<option value="Verdana, \'Verana Sans\', Geneva, sans-serif, Sans">Verdana / Geneva</option>';
    var l = creAdd("label",idiv);
    l.htmlFor = "fontselector";
    l.innerHTML = "Font";

    var idiv = creAdd("div",d,["idiv"]);
    window.settings.fontSizeInput = creAdd("input",idiv,[],"fontsizeselector");
    window.settings.fontSizeInput.type="number";
    var l = creAdd("label",idiv);
    l.htmlFor = "fontsizeselector";
    l.innerHTML = "Size";

    var idiv = creAdd("div",d,["idiv"]);
    window.settings.fontColorInput = creAdd("input",idiv,[],"fontcolorselector");
    window.settings.fontColorInput.type="color";
    var l = creAdd("label",idiv);
    l.htmlFor = "fontcolorselector";
    l.innerHTML = "Color";
    var b = creAdd("br", d);

    var idiv = creAdd("div",d,["idiv"]);
    window.settings.alignSelect = creAdd("select",idiv,[],"alignselector");
    window.settings.alignSelect.innerHTML = '<option value="justify" selected="selected">justified</option>' +
        '<option value="left" >left</option>' +
        '<option value="center" >center</option>' +
        '<option value="right" >right</option>';
    var l = creAdd("label",idiv);
    l.innerHTML = "Align";
    l.htmlFor = "alignselector";

    var idiv = creAdd("div",d,["idiv"]);
    window.settings.spacingSelect = creAdd("input",idiv,[],"spacingselector");
    window.settings.spacingSelect.type = "number";
    window.settings.spacingSelect.step = 0.2;
    var l = creAdd("label",idiv);
    l.innerHTML = "Spacing";
    l.htmlFor = "spacingselector";

    var idiv = creAdd("div",d,["idiv"]);
    window.settings.bgColorInput = creAdd("input",idiv,[],"bgcolorselector");
    window.settings.bgColorInput.type="color";
    var l = creAdd("label",idiv);
    l.htmlFor = "bgcolorselector";
    l.innerHTML = "Background color";
    var b = creAdd("br", d);

    var idiv = creAdd("div",d,["idiv"]);
    window.settings.gerColorInput = creAdd("input",idiv,[],"gercolorselector");
    window.settings.gerColorInput.type="color";
    var l = creAdd("label",idiv);
    l.htmlFor = "gercolorselector";
    l.innerHTML = "German BG color";

    var idiv = creAdd("div",d,["idiv"]);
    window.settings.ogdColorInput = creAdd("input",idiv,[],"ogdcolorselector");
    window.settings.ogdColorInput.type="color";
    var l = creAdd("label",idiv);
    l.htmlFor = "ogdcolorselector";
    l.innerHTML = "Ogden BG color";

    var idiv = creAdd("div",d,["idiv"]);
    window.settings.pmcColorInput = creAdd("input",idiv,[],"pmccolorselector");
    window.settings.pmcColorInput.type="color";
    var l = creAdd("label",idiv);
    l.htmlFor = "pmccolorselector";
    l.innerHTML = "Pears/McGuinness BG color";

    var b = creAdd("br", d);

    var idiv=creAdd("div",d,["idiv"]);
    window.settings.wholePageMargInput = creAdd("input",idiv,[],"wholepagemargselect");
    window.settings.wholePageMargInput.type = "number";
    var l = creAdd("label",idiv);
    l.htmlFor = "wholepagemargselect";
    l.innerHTML = "Page margin (px)";

    var idiv=creAdd("div",d,["idiv"]);
    window.settings.extraMargInput = creAdd("input",idiv,[],"extramargselect");
    window.settings.extraMargInput.type = "number";
    var l = creAdd("label",idiv);
    l.htmlFor = "extramargselect";
    l.innerHTML = "Margin for intro/index (px)";

    var bt = creAdd("button", window.settings.settingsInnerBox);
    bt.type = "button";
    bt.innerHTML = "reset";
    bt.onclick = function() { resetSettings(); }
    var bt = creAdd("button", window.settings.settingsInnerBox);
    bt.innerHTML = "apply";
    bt.type = "button";
    bt.onclick = function() { applySettings(); }
    resetSettings();
    applySettings();
}

function createSettingsButton() {
    window.settingsButtonBox =creAdd("div",document.body,["settingsbuttonbg"]);
    window.settingsButtonBox.innerHTML = '<button type="button" onclick="openSettingsPanel();"><span style="font-size: 150%;">⛭</span> SETTINGS</button>';
}

window.onload = function() {
    if (typeof document.body.getElementsByClassName !== 'function') {
        return;
    }
    createSettingsPanel();
    createSettingsButton();
}

