<?php if (!defined('PmWiki')) exit();
                                                                      /*
  * Copyright *
  Copyright 2004, by Steven Leite (steven_leite@kitimat.net).

  * License *
  Same as PmWiki (GNU GPL) 
  
  * Special Thanks *
  Thanks to  Pm (Patrick Michaud, www.pmichaud.com), for creating
  PmWiki, and for sharing his knowledge and insightfulness with the 
  PmWiki community.

  * Description *
  eProtect is an email obfuscation add-on for PmWiki. It intercepts 
  pages before they are saved, and rewrites email addresses in a 
  protected format. 
  
     Example: "mailto:username@domain.net" is automatically replaced with: 
     "[[hidden-email:hfre@qbznva.arg]]".

  When a WikiPage is requested the [[hidden-email:]] directive is 
  detected, and then translated into a small javascript.  Once the page
  is rendered the in website, it's at that point which the javascript
  decodes the protected address. Viewing the source-code of the page
  won't reveal the true email address, nor will clicking on the link to
  "Edit This Page".
  
  * Installation Instructions *
  1. Copy this script (e-protect.php) to local/scripts/
  2. In your config.php file, add the following line:
     include_once('local/scripts/e-protect.php');
  3. That's it!

  * History *
  May 11, 2004 - Working Beta.  Still a few improvements to be made, but
                 the script is ready for public testing.  Please feel
				 free to email me your comments/suggestions.  Thanks!
  May 8, 2004  - Alpha release.  Not released to public.

  * Configuration *
  There aren't (yet) any configuration variables for this script.    */
 
//----------------------------------------------------------------------

$eProtectRedirect      = $HandleActions['post']; // back up the referring function name
$HandleActions['post'] = "eProtectHandlePost";   // set redirection for 'post' to eProtectHandlePost
$DoubleBrackets["/\\[\\[hidden-email:(.*?)\\]\\]/e"] = 'eProtect("$1");';

//----------------------------------------------------------------------
function eProtect($CompressedEmailAddress) {
//----------------------------------------------------------------------
  $email = $CompressedEmailAddress;
  $html = '';
  static $eProtectFirstTime = true;
  if ($eProtectFirstTime==true) {
     $html .= "\n\n<!--eProtectJavascriptDecoder-->\n<script language='JavaScript' type='text/JavaScript'>Email={map:null,convert:function(a){Email.init();var s='';for(i=0;i<a.length;i++){var b=a.charAt(i);s+=((b>='A'&&b<='Z')||(b>='a'&&b<='z')?Email.map[b]:b);}return s;},init:function(){if(Email.map!=null)return;var map=new Array();var s='abcdefghijklmnopqrstuvwxyz';for(i=0;i<s.length;i++)map[s.charAt(i)]=s.charAt((i+13)%26);for(i=0;i<s.length;i++)map[s.charAt(i).toUpperCase()]=s.charAt((i+13)%26).toUpperCase();Email.map=map;},decode:function(a){document.write(Email.convert(a));}}</script>\n<!--/eProtectJavascriptDecoder-->\n";
     $eProtectFirstTime=false;}
  $html .= "\n<!--eProtect-->\n";
  $html .= "<script language='JavaScript' type='text/JavaScript'>Email.decode" .
    "(\"<n uers='znvygb:$email'>$email</n>\");" . "</script>"; 
  $html .= "\n<!--/eProtect-->\n";
  return Keep($html);			
}
  
//----------------------------------------------------------------------
function eProtectHandlePost ($pagename) {
//----------------------------------------------------------------------
  global $HandleActions, $UrlPathPattern, $KeepToken, $KPV;
  global $eProtectRedirect;
  $email_pattern = "mailto:(\w[-._\w]*\w@\w[-._\w]*\w\.\w{2,4})";
  $text = $_POST['text'];
  $text = preg_replace_callback("/\\[\\=(.*?)\\=\\]/s", create_function('$str', 'return Keep($str[0]);'), $text);    // extract the [= .. =] and temporarily store in $KPV[]
  $text = preg_replace_callback("/$email_pattern/s", create_function('$m','return "[[hidden-email:" . trim(str_rot13($m[1])) . "]]";'), $text);
  $text = preg_replace("/$KeepToken(\\d+)$KeepToken/e",'$KPV[$1]',$text);   // put the [= .. =] back in to the text
  $_POST['text'] = $text;
  $eProtectRedirect($pagename);
  return;
}
