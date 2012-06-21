<?php if (!defined('PmWiki')) exit();
/*  Copyright 2005-6 Benjamin C. Wilson (ameen@dausha.net)
    This file is multicat.php; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published
    by the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.  

    This script simulates the style of the Mediawiki wiki markup. 
    When categories are listed on a line it will label them and 
    put them into a block.

    To use this script, simply copy it into the cookbook/ directory
    and add the following line to config.php (or a per-page/per-group
    customization file).

        include_once('cookbook/mediacat.php');

    For more details, visit http://pmwiki.org/wiki/Cookbook/MediaCat
    Customizable Variables:
        :$McatLabelS     :This sets the singular form of the category
        label. Defaults to "Category:"
        :$McatLabelP     :This sets the plural form of the category
        label. Defaults to "Categories:"
        :$McatSep        :This determines what the seperator format is.
        Defaults to ' | '
        :$McatBlockStart :This creates the markup that creats the block.
        :$McatBlockEnd   :This creates the markup that terminates the block.
        :$McatCSSStyle   :This is the CSS that is added to
        $HTMLStylesFmt when categories are marked up by this script.

    Version History
    ---------------

    1.0 August 07, 2005 - by BenWilson: Initially published.
    2.0 May 07, 2006 - by BenWilson: Rewrote to address issues raised by
      my friend Mateusz. This version pulls all freelink categories, 
      builds an alphabetized category list, and then appends the
      categories at the end of the page.
      
*/

SDV($McatLabelS, 'Category:');
SDV($McatLabelP, 'Categories:');
SDV($McatSep,    ' | ');
SDV($McatClass, 'category');
SDV($McatBlockStart, "<div class='$McatClass'>");
SDV($McatBlockEnd,   "</div>");
SDV($McatCSSStyle, "
div.category { 
  border: 1px solid #666;
  padding: 0.5em;
  background-color:  #EEE;
};
");
Markup('multicat','fulltext','[[!', 'return Multicat($x);');
#Markup('multicat','fulltext','[[Category:', 'return Multicat($x);');

$mcategories = array();
function Multicat($t) {
    global $McatLabelS, $McatLabelP, $McatSep;
    global $McatBlockStart, $McatBlockEnd;
    global $HTMLStylesFmt, $McatCSSStyle;
    global $mcategories;
    $t = preg_replace_callback(
            '/(\[\[(Category:|!).*?\]\])/mx',
            '_MulticatCallback', 
            $t
         );
    sort($mcategories);
    $label = (count($mcategories) > 1) ? $McatLabelP : $McatLabelS;
    $input = implode($McatSep, $mcategories);
    $HTMLStylesFmt[] = $McatCSSStyle;
    $mcats = "$McatBlockStart $label $input $McatBlockEnd";
    $t .= $mcats;
    return $t;
}
function _MulticatCallback($m) {
    global $CategoryGroup, $mcategories;
    $cat = preg_replace('/(Category:|!)/',"$CategoryGroup/",$m[1]);
    $mcategories[] = $cat;
    return '';
}
