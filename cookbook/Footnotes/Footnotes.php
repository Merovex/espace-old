<?php if (!defined('PmWiki')) exit();
/*  Copyright 2007 Benjamin C. Wilson (ameen@dausha.net)

    This file is Footnotes.php; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published
    by the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.  

    This recipe mimics LaTeX \footnote{} behavior, allowing the author to group
    footnotes logically with the note anchor.

    To use this script, simply copy it into the cookbook/ directory
    and add the following line to config.php (or a per-page/per-group
    customization file).

        include_once('cookbook/footnotes.php');

    Version History
    ---------------

    1.0 March 27, 2007 - by BenWilson: Initially published.
      
*/

SDV($FootnotesHeader, '----');
$Footnotes = array();
SDV($FootnotesCSSStyle, " div#footnotes { margin-top: 2em; font-size: 0.8em; border-top: 1px solid #999; color: #333; } div#footnotes:before {content: 'Footnotes:';} ");
Markup('footnote','fulltext','(:footnote', 'return Footnotes($x);');

function Footnotes($t) {
    global $Footnotes, $FootnotesCSSStyle, $HTMLStylesFmt;

    $t = preg_replace_callback(
            '/\(:footnote(.*?):\)/mx',
            '_FootnoteCallback', 
            $t
         );
    $input = "(:div id='footnotes':)\n\n".implode("\n\n\n", $Footnotes)."\n\n\n(:divend:)";
    $HTMLStylesFmt[] = $FootnotesCSSStyle; # Only add style when needed.

    # I ran into a problem with group footer, so I'm avoiding it here...
    if (preg_match("/\(:groupfooter:\)/", $t))
        $t = preg_replace("/\(:groupfooter:\)/", "$input\n\n(:groupfooter:)", $t);
    else 
        $t .= $index;
    return $t;
}
function _FootnoteCallback($m) {
    global $Footnotes;
    $index = count($Footnotes) + 1;
    $Footnotes[] = "# [[#fn-$index]] $m[1]";
    return "'^[[#fn-$index|$index]]^'";
}
