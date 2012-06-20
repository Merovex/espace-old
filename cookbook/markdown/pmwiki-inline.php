<?php if (!defined('PmWiki')) exit();
/*  Copyright 2004-2006 Patrick R. Michaud (pmichaud@pobox.com)
    This file is part of PmWiki; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published
    by the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.  See pmwiki.php for full details.

    This script defines PmWiki's standard markup.  It is automatically
    included from stdconfig.php unless $EnableStdMarkup==0.

    Each call to Markup() below adds a new rule to PmWiki's translation
    engine (unless a rule with the same name has already been defined).
    The form of the call is Markup($id,$where,$pat,$rep);
    $id is a unique name for the rule, $where is the position of the rule
    relative to another rule, $pat is the pattern to look for, and
    $rep is the string to replace it with.
*/


#### inline markups ####
## ''emphasis''
Markup("''",'inline',"/''(.*?)''/",'<em>$1</em>');

## '''strong'''
Markup("'''","<''","/'''(.*?)'''/",'<strong>$1</strong>');

## '''''strong emphasis'''''
Markup("'''''","<'''","/'''''(.*?)'''''/",'<strong><em>$1</em></strong>');

## @@code@@
Markup('@@','inline','/@@(.*?)@@/','<code>$1</code>');

## '+big+', '-small-'
Markup("'+","<'''''","/'\\+(.*?)\\+'/",'<big>$1</big>');
Markup("'-","<'''''","/'\\-(.*?)\\-'/",'<small>$1</small>');

## '^superscript^', '_subscript_'
Markup("'^","<'''''","/'\\^(.*?)\\^'/",'<sup>$1</sup>');
Markup("'_","<'''''","/'_(.*?)_'/",'<sub>$1</sub>');

## [+big+], [-small-]
Markup('[+','inline','/\\[(([-+])+)(.*?)\\1\\]/e',
  "'<span style=\'font-size:'.(round(pow(6/5,$2strlen('$1'))*100,0)).'%\'>'.
    PSS('$3</span>')");

## {+ins+}, {-del-}
Markup('{+','inline','/\\{\\+(.*?)\\+\\}/','<ins>$1</ins>');
Markup('{-','inline','/\\{-(.*?)-\\}/','<del>$1</del>');

## [[<<]] (break)
Markup('[[<<]]','inline','/\\[\\[&lt;&lt;\\]\\]/',"<br clear='all' />");

