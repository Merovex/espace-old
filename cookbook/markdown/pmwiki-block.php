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



#### Block markups ####
## Completely blank lines don't do anything.
Markup('blank', '<block', '/^\\s+$/', '');

## process any <:...> markup (after all other block markups)
Markup('^<:','>block','/^(?=\\s*\\S)(<:([^>]+)>)?/e',"Block('$2')");

## unblocked lines w/block markup become anonymous <:block>
Markup('^!<:', '<^<:',
  "/^(?!<:)(?=.*(<\\/?($BlockPattern)\\b)|$KeepToken\\d+B$KeepToken)/",
  '<:block>');

## Lines that begin with displayed images receive their own block.  A
## pipe following the image indicates a "caption" (generates a linebreak).
Markup('^img', 'block',
  "/^((?>(\\s+|%%|%[A-Za-z][-,=:#\\w\\s'\"]*%)*)$KeepToken(\\d+L)$KeepToken)(\\s*\\|\\s?)?(.*)$/e",
  "PSS((strpos(\$GLOBALS['KPV']['$3'],'<img')===false) ? '$0' : 
       '<:block,1><div>$1' . ('$4' ? '<br />' : '') .'$5</div>')");

## Removed the Whitespace Blocks.
## Removed the Bullet and Numberd Lists:

## indented (->) /hanging indent (-<) text
$s = "Markup('^->','block','/^(?>(-+))&gt;\\s?(\\s*)/','<:indent,$1,$1 $2>$2');";
Markup('^-<','block','/^(?>(-+))&lt;\\s?(\\s*)/','<:outdent,$1,$1  $2>$2');

## definition lists
Markup('^::','block','/^(:+)(\s*)([^:]+):/','<:dl,$1,$1$2><dt>$2$3</dt><dd>');

## Q: and A:
Markup('^Q:', 'block', '/^Q:(.*)$/', "<:block,1><p class='question'>$1</p>");
Markup('^A:', 'block', '/^A:/', Keep(''));

## tables
## ||cell||, ||!header cell||, ||!caption!||
Markup('^||||', 'block', 
  '/^\\|\\|.*\\|\\|.*$/e',
  "FormatTableRow(PSS('$0'))");
## ||table attributes
Markup('^||','>^||||','/^\\|\\|(.*)$/e',
  "PZZ(\$GLOBALS['BlockMarkups']['table'][0] = PQA(PSS('<table $1>')))
    .'<:block,1>'");

## headings

Markup('^!', 'block',
  '/^(!{1,6})\\s?(.*)$/e',
    "'<:block,1><h'.strlen('$1').PSS('>$2</h').strlen('$1').'>'");

## Removed Horizontal Rule
