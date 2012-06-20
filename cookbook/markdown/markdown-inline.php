<?php if (!defined('PmWiki')) exit();
#
# Markdown  -  A text-to-HTML conversion tool for web writers
#
# Copyright (c) 2004-2005 John Gruber  
# <http://daringfireball.net/projects/markdown/>
#
# Copyright (c) 2004-2005 Michel Fortin - PHP Port  
# <http://www.michelf.com/projects/php-markdown/>
#
# PmWiki Conversion Copyrigh (c) 2006 Benjamin C. Wilson
# <http://www.dausha.net/Markdown/Recipe
#
Markup('MarkdownComment', '<comment', '/<!--.*?-->/si', '');
Markup('MarkdownComment', '<_begin', '/<!--/s', '');

#### inline markups ####
## ''emphasis''
Markup('mkem','inline', '/(\*|_)(\w.*?)\1/', '<em>$2</em>');
## '''strong'''
Markup('mkstrong','<mkem', '/(\*\*|__)(\w.*?)\1/', '<strong>$2</strong>');
## '''strong emphasis'''
Markup('mkstrongem','<mkstrong', '/(\*\*\*|___)(\w.*?)\1/',
'<strong><em>$2</em></strong>');

## @@code@@
Markup('code','inline','/@@(.*?)@@/','<code>$1</code>');
Markup("mkcode",'inline', '/((?<!`)`|(?<!`)`)(?=\S)(?! \1)(.+?)(?<=\S)\1/sx','<code>$2</code>');

## '+big+', '-small-'
Markup("'big","<strongem","/'\\+(.*?)\\+'/",'<big>$1</big>');
Markup("small","<strongem","/'\\-(.*?)\\-'/",'<small>$1</small>');

## '^superscript^', '_subscript_'
Markup("super","<strongem","/'\\^(.*?)\\^'/",'<sup>$1</sup>');
Markup("sub","<strongem","/'_(.*?)_'/",'<sub>$1</sub>');

## [+big+], [-small-]
Markup('[+','inline','/\\[(([-+])+)(.*?)\\1\\]/e',
  "'<span style=\'font-size:'.(round(pow(6/5,$2strlen('$1'))*100,0)).'%\'>'.
    PSS('$3</span>')");

## {+ins+}, {-del-}
Markup('ins','inline','/\\{\\+(.*?)\\+\\}/','<ins>$1</ins>');
Markup('del','inline','/\\{-(.*?)-\\}/','<del>$1</del>');

## [[<<]] (break)
Markup('brclear','inline','/\\[\\[&lt;&lt;\\]\\]/',"<br clear='all' />");
