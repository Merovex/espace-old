<?php

Markup('TranslateThis','style','/\(:translate-this\s*(\w+?):\)/se',"TranslateThis('$1');");
Markup('TranslateThisIcon','style','/\(:translate-this-icon\s*(\w+?):\)/se',"TranslateThisIcon('$1');");
SDV($TranslateThisUrlFormat, 'http://www.google.com/translate?u=%s&langpair=%s|%s&hl=%s&ie=UTF8');
SDV($TranslateThisSiteLanguage, 'en');
SDVA($TranslateThisLanguages, array('en' => 'English', 'es' => 'Spanish', 'pt-BR' => 'Portuguese', 'ja' => 'Japanese', 'de' => 'German'));

function TranslateThis($language, $link='') {
  global $TranslateThisUrlFormat;
  global $TranslateThisSiteLanguage;
  global $TranslateThisLanguages;
  global $ScriptUrl, $pagename;
  $url = "$ScriptUrl/$pagename";
  #print_r($TranslateThisLanguages);
  if ($link == '') {$link = 'Translate This';}
  return "<a href='".sprintf($TranslateThisUrlFormat, $url, $TranslateThisSiteLanguage, $language, $TranslateThisSiteLanguage)."'>$link</a>";
}
function TranslateThisIcon($language, $link='') {
  global $TranslateThisLanguages;
  global $PubDirUrl;
  global $TranslateThisLanguages;
  $alt = "Translate to $TranslateThisLanguages[$language]";
  $icon = sprintf("<img alt='$alt' src='$PubDirUrl/images/flag_icons/%s.png' />", $language);
  return TranslateThis($language, $icon);
}
