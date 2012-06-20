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



#### (:table:) markup (AdvancedTables)

function Cells($name,$attr) {
  global $MarkupFrame;
  $attr = preg_replace('/([a-zA-Z]=)([^\'"]\\S*)/',"\$1'\$2'",$attr);
  $tattr = @$MarkupFrame[0]['tattr'];
  $name = strtolower($name);
  $out = '<:block>';
  if (strncmp($name, 'cell', 4) != 0 || @$MarkupFrame[0]['closeall']['div']) {
    $out .= @$MarkupFrame[0]['closeall']['div']; 
    unset($MarkupFrame[0]['closeall']['div']);
    $out .= @$MarkupFrame[0]['closeall']['table']; 
    unset($MarkupFrame[0]['closeall']['table']);
  }
  if ($name == 'div') {
    $MarkupFrame[0]['closeall']['div'] = "</div>";
    $out .= "<div $attr>";
  }
  if ($name == 'table') $MarkupFrame[0]['tattr'] = $attr;
  if (strncmp($name, 'cell', 4) == 0) {
    if (strpos($attr, "valign=")===false) $attr .= " valign='top'";
    if (!@$MarkupFrame[0]['closeall']['table']) {
       $MarkupFrame[0]['closeall']['table'] = "</td></tr></table>";
       $out .= "<table $tattr><tr><td $attr>";
    } else if ($name == 'cellnr') $out .= "</td></tr><tr><td $attr>";
    else $out .= "</td><td $attr>";
  }
  return $out;
}

Markup('table', '<block',
  '/^\\(:(table|cell|cellnr|tableend|div|divend)(\\s.*?)?:\\)/ie',
  "Cells('$1',PSS('$2'))");
Markup('^>>', '<table',
  '/^&gt;&gt;(.+?)&lt;&lt;(.*)$/',
  '(:div:)%div $1 apply=div%$2 ');
Markup('^>><<', '<^>>',
  '/^&gt;&gt;&lt;&lt;/',
  '(:divend:)');


