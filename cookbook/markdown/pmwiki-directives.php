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

## first we preserve text in [=...=] and [@...@]
function PreserveText($sigil, $text, $lead) {
  if ($sigil=='=') return $lead.Keep($text);
  if (strpos($text, "\n")===false) 
    return "$lead<code class='escaped'>".Keep($text)."</code>";
  $text = preg_replace("/\n[^\\S\n]+$/", "\n", $text);
  if ($lead == "" || $lead == "\n") 
    return "$lead<pre class='escaped'>".Keep($text)."</pre>";
  return "$lead<:pre,1>".Keep($text);
}

Markup('[=','_begin',"/(\n[^\\S\n]*)?\\[([=@])(.*?)\\2\\]/se",
    "PreserveText('$2', PSS('$3'), '$1')");
Markup('restore','<_end',"/$KeepToken(\\d.*?)$KeepToken/e",
    '$GLOBALS[\'KPV\'][\'$1\']');
Markup('<:', '>restore',
  "/<:[^>]*>/", "");

## remove carriage returns before preserving text
Markup('\\r','<[=','/\\r/','');

# $[phrase] substitutions
Markup('$[phrase]', '>[=',
  '/\\$\\[(?>([^\\]]+))\\]/e', "NoCache(XL(PSS('$1')))");

# {$var} substitutions
Markup('{$var}', '>$[phrase]',
  '/\\{(!?[-\\w.\\/]*)(\\$\\w+)\\}/e', 
  "htmlspecialchars(PageVar(\$pagename, '$2', '$1'), ENT_NOQUOTES)");

Markup('if', 'fulltext',
  "/\\(:(if[^\n]*?):\\)(.*?)(?=\\(:if[^\n]*?:\\)|$)/sei",
  "CondText(\$pagename,PSS('$1'),PSS('$2'))");

## (:include:)
Markup('include', '>if',
  '/\\(:include\\s+(\\S.*?):\\)/ei',
  "PRR(IncludeText(\$pagename, '$1'))");

## (:redirect:)
Markup('redirect', '<include',
  '/\\(:redirect\\s+(\\S.*?):\\)/ei',
  "RedirectMarkup(\$pagename, PSS('$1'))");

$SaveAttrPatterns['/\\(:(if|include|redirect)(\\s.*?)?:\\)/i'] = ' ';

## GroupHeader/GroupFooter handling
Markup('nogroupheader', '>include',
  '/\\(:nogroupheader:\\)/ei',
  "PZZ(\$GLOBALS['GroupHeaderFmt']='')");
Markup('nogroupfooter', '>include',
  '/\\(:nogroupfooter:\\)/ei',
  "PZZ(\$GLOBALS['GroupFooterFmt']='')");
Markup('groupheader', '>nogroupheader',
  '/\\(:groupheader:\\)/ei',
  "PRR(FmtPageName(\$GLOBALS['GroupHeaderFmt'],\$pagename))");
Markup('groupfooter','>nogroupfooter',
  '/\\(:groupfooter:\\)/ei',
  "PRR(FmtPageName(\$GLOBALS['GroupFooterFmt'],\$pagename))");

## (:nl:)
Markup('nl0','<split',"/([^\n])(?>(?:\\(:nl:\\))+)([^\n])/i","$1\n$2");
Markup('nl1','>nl0',"/\\(:nl:\\)/i",'');

## \\$  (end of line joins)
Markup('\\$','>nl1',"/\\\\(?>(\\\\*))\n/e",
  "str_repeat('<br />',strlen('$1'))");

## Remove one <:vspace> after !headings
Markup('!vspace', '>\\$', "/^(!(?>[^\n]+)\n)<:vspace>/m", '$1');

## (:noheader:),(:nofooter:),(:notitle:)...
Markup('noheader', 'directives',
  '/\\(:noheader:\\)/ei',
  "SetTmplDisplay('PageHeaderFmt',0)");
Markup('nofooter', 'directives',
  '/\\(:nofooter:\\)/ei',
  "SetTmplDisplay('PageFooterFmt',0)");
Markup('notitle', 'directives',
  '/\\(:notitle:\\)/ei',
  "SetTmplDisplay('PageTitleFmt',0)");
Markup('noleft', 'directives',
  '/\\(:noleft:\\)/ei',
  "SetTmplDisplay('PageLeftFmt',0)");
Markup('noright', 'directives',
  '/\\(:noright:\\)/ei',
  "SetTmplDisplay('PageRightFmt',0)");

## (:spacewikiwords:)
Markup('spacewikiwords', 'directives',
  '/\\(:(no)?spacewikiwords:\\)/ei',
  "PZZ(\$GLOBALS['SpaceWikiWords']=('$1'!='no'))");

## (:linkwikiwords:)
Markup('linkwikiwords', 'directives',
  '/\\(:(no)?linkwikiwords:\\)/ei',
  "PZZ(\$GLOBALS['LinkWikiWords']=('$1'!='no'))");

## (:linebreaks:)
Markup('linebreaks', 'directives',
  '/\\(:(no)?linebreaks:\\)/ei',
  "PZZ(\$GLOBALS['HTMLPNewline'] = ('$1'!='no') ? '<br  />' : '')");

## (:messages:)
Markup('messages', 'directives',
  '/^\\(:messages:\\)/ei',
  "'<:block>'.Keep(
    FmtPageName(implode('',(array)\$GLOBALS['MessagesFmt']), \$pagename))");

## (:comment:)
Markup('comment', 'directives', '/\\(:comment .*?:\\)/i', '');

## character entities
Markup('&','directives','/&amp;(?>([A-Za-z0-9]+|#\\d+|#[xX][A-Fa-f0-9]+));/',
  '&$1;');

## (:title:)
Markup('title','>&',
  '/\\(:title\\s(.*?):\\)/ei',
  "PZZ(PCache(\$pagename, 
         array('title' => SetProperty(\$pagename, 'title', PSS('$1')))))");

## (:keywords:), (:description:)
Markup('keywords', '>&', 
  "/\\(:keywords?\\s+(.+?):\\)/ei",
  "PZZ(SetProperty(\$pagename, 'keywords', PSS('$1'), ', '))");
Markup('description', '>&',
  "/\\(:description\\s+(.+?):\\)/ei",
  "PZZ(SetProperty(\$pagename, 'description', PSS('$1'), '\n'))");
$HTMLHeaderFmt['meta'] = 'function:PrintMetaTags';
function PrintMetaTags($pagename, $args) {
  global $PCache;
  foreach(array('keywords', 'description') as $n) {
    foreach((array)@$PCache[$pagename]["=p_$n"] as $v) {
      $v = str_replace("'", '&#039;', $v);
      print "<meta name='$n' content='$v' />\n";
    }
  }
}
function MarkupMarkup($pagename, $text, $opt = '') {
  $MarkupMarkupOpt = array('class' => 'vert');
  $opt = array_merge($MarkupMarkupOpt, ParseArgs($opt));
  $html = MarkupToHTML($pagename, $text, array('escape' => 0));
  if (@$opt['caption'])
    $caption = str_replace("'", '&#039;',
                           "<caption>{$opt['caption']}</caption>");
  $class = preg_replace('/[^-\\s\\w]+/', ' ', @$opt['class']);
  if (strpos($class, 'horiz') !== false)
    { $sep = ''; $pretext = wordwrap($text, 40); }
  else
    { $sep = '</tr><tr>'; $pretext = wordwrap($text, 75); }
  return Keep("<table class='markup $class' align='center'>$caption
      <tr><td class='markup1' valign='top'><pre>$pretext</pre></td>$sep<td
        class='markup2' valign='top'>$html</td></tr></table>");
}

Markup('markup', '<[=',
  "/\\(:markup(\\s+([^\n]*?))?:\\)[^\\S\n]*\\[([=@])(.*?)\\3\\]/sei",
  "MarkupMarkup(\$pagename, PSS('$4'), PSS('$2'))");
Markup('markupend', '>markup',
  "/\\(:markup(\\s+([^\n]*?))?:\\)[^\\S\n]*\n(.*?)\\(:markupend:\\)/sei",
  "MarkupMarkup(\$pagename, PSS('$3'), PSS('$1'))");

$HTMLStylesFmt['markup'] = "
  table.markup { border:2px dotted #ccf; width:90%; }
  td.markup1, td.markup2 { padding-left:10px; padding-right:10px; }
  table.vert td.markup1 { border-bottom:1px solid #ccf; }
  table.horiz td.markup1 { width:23em; border-right:1px solid #ccf; }
  table.markup caption { text-align:left; }
  div.faq p, div.faq pre { margin-left:2em; }
  div.faq p.question { margin:1em 0 0.75em 0; font-weight:bold; }
  ";

#### Special conditions ####
## The code below adds (:if date:) conditions to the markup.
$Conditions['date'] = "CondDate(\$condparm)";

function CondDate($condparm) {
  global $Now;
  NoCache();
  if (!preg_match('/^(.*?)(\\.\\.(.*))?$/', $condparm, $match)) return false;
  if ($match[2]) {
    $t0 = $match[1];  if ($t0 == '') $t0 = '19700101';
    $t1 = $match[3];  if ($t1 == '') $t1 = '20380101';
  } else $t0 = $t1 = $match[1];
  $t0 = preg_replace('/\\D/', '', $t0);
  if (!preg_match('/^(\\d{4})(\\d\\d)(\\d\\d)$/', $t0, $m)) return false;
  $g0 = mktime(0, 0, 0, $m[2], $m[3], $m[1]);
  if ($Now < $g0) return false;

  $t1 = preg_replace('/\\D/', '', $t1);
  $t1++;
  if (!preg_match('/^(\\d{4})(\\d\\d)(\\d\\d)$/', $t1, $m)) return false;
  $g1 = mktime(0, 0, 0, $m[2], $m[3], $m[1]);
  if ($Now >= $g1) return false;
  return true;
}

# This pattern enables the (:encrypt <phrase>:) markup/replace-on-save
# pattern.
SDV($ROSPatterns['/\\(:encrypt\\s+([^\\s:=]+).*?:\\)/e'],
"crypt(PSS('$1'))");

