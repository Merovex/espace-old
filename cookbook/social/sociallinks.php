<?php

Markup('social_links','fulltext','/\(:social\s*(\w+)=(.*?):\)/se',"SocialLinks('$1','$2');");
Markup('social_sharing','directives','/\(:social-sharing\s*(\w+)=?(.*?):\)/se',"SocialSharing('$1','$2');");
SDV($SocialIconSize, 32);
SDV($SocialIconType, 'social');

function SocialLinks($site, $link) {
    global $SocialIconSize;
    global $SocialIconType;
    $s = $SocialIconSize;
    if (!preg_match('/http/', $link)) { $link = "http://$site.com/$link"; }
    return Keep(sprintf("<a href='%s'><img src='/pub/images/$SocialIconType/%sx%s/%s.png' /></a>", $link, $s, $s, $site));
}
function SocialSharing($site) {
    global $ScriptUrl, $pagename;
    global $SocialIconSize;
    global $SocialIconType;
    $s = $SocialIconSize;
    $templates = array(
        'facebook' => 'http://www.facebook.com/sharer.php?t=%s&u=%s',
        'twitter' =>  'http://twitter.com/home?status=%%40merovex+%s+%s'
    );
    $source = "$ScriptUrl/$pagename";
    #$title = $pagename;
    $img = sprintf("<img src='/pub/images/$SocialIconType/%sx%s/%s.png' />", $s, $s, $site);
    $url = sprintf("<a href='$templates[$site]'>$img</a>", $title, $source);
    return Keep($url);

}
