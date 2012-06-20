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


###### Links ######
## [[free links]]
Markup('[[','links',"/(?>\\[\\[\\s*)(.*?)\\]\\]($SuffixPattern)/e",
  "Keep(MakeLink(\$pagename,PSS('$1'),NULL,'$2'),'L')");

## [[!Category]]
SDV($CategoryGroup,'Category');
SDV($LinkCategoryFmt,"<a class='categorylink' href='\$LinkUrl'>\$LinkText</a>");
Markup('[[!','<[[','/\\[\\[!(.*?)\\]\\]/e',
  "Keep(MakeLink(\$pagename,PSS('$CategoryGroup/$1'),NULL,'',\$GLOBALS['LinkCategoryFmt']),'L')");
# This is a temporary workaround for blank category pages.
# It may be removed in a future release (Pm, 2006-01-24)
if (preg_match("/^$CategoryGroup\\./", $pagename)) {
  SDV($DefaultPageTextFmt, '');
  SDV($PageNotFoundHeaderFmt, 'HTTP/1.1 200 Ok');
}

## [[target | text]]
Markup('[[|','<[[',
  "/(?>\\[\\[([^|\\]]*)\\|\\s*)(.*?)\\s*\\]\\]($SuffixPattern)/e",
  "Keep(MakeLink(\$pagename,PSS('$1'),PSS('$2'),'$3'),'L')");

## [[text -> target ]]
Markup('[[->','>[[|',
  "/(?>\\[\\[([^\\]]+?)\\s*-+&gt;\\s*)(.*?)\\]\\]($SuffixPattern)/e",
  "Keep(MakeLink(\$pagename,PSS('$2'),PSS('$1'),'$3'),'L')");

## [[#anchor]]
Markup('[[#','<[[','/(?>\\[\\[#([A-Za-z][-.:\\w]*))\\]\\]/e',
  "Keep(TrackAnchors('$1') ? '' : \"<a name='$1' id='$1'></a>\", 'L')");
function TrackAnchors($x) { global $SeenAnchor; return @$SeenAnchor[$x]++; }

## [[target |#]] reference links
Markup('[[|#', '<[[|',
  "/(?>\\[\\[([^|\\]]+))\\|\\s*#\\s*\\]\\]/e",  
  "Keep(MakeLink(\$pagename,PSS('$1'),'['.++\$MarkupFrame[0]['ref'].']'),'L')");

## [[target |+]] title links
Markup('[[|+', '<[[|',
  "/(?>\\[\\[([^|\\]]+))\\|\\s*\\+\\s*]]/e",
  "Keep(MakeLink(\$pagename, PSS('$1'),
                 PageVar(MakePageName(\$pagename,PSS('$1')), '\$Title')
                ),'L')");

## bare urllinks 
Markup('urllink','>[[',
  "/\\b(?>(\\L))[^\\s$UrlExcludeChars]*[^\\s.,?!$UrlExcludeChars]/e",
  "Keep(MakeLink(\$pagename,'$0','$0'),'L')");

## mailto: links 
Markup('mailto','<urllink',
  "/\\bmailto:([^\\s$UrlExcludeChars]*[^\\s.,?!$UrlExcludeChars])/e",
  "Keep(MakeLink(\$pagename,'$0','$1'),'L')");

## inline images
Markup('img','<urllink',
  "/\\b(?>(\\L))([^\\s$UrlExcludeChars]+$ImgExtPattern)(\"([^\"]*)\")?/e",
  "Keep(\$GLOBALS['LinkFunctions']['$1'](\$pagename,'$1','$2','$4','$1$2',
    \$GLOBALS['ImgTagFmt']),'L')");

## bare wikilinks
#Markup('wikilink', '>urllink',
  #"/\\b($GroupPattern([\\/.]))?($WikiWordPattern)/e",
  #"Keep('<span class=\\'wikiword\\'>'.WikiLink(\$pagename,'$0').'</span>',
        #'L')");

## escaped `WikiWords
Markup('`wikiword', '<wikilink',
  "/`(($GroupPattern([\\/.]))?($WikiWordPattern))/e",
  "Keep('$1')");


