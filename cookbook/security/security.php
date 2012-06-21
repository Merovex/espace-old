<?php if (!defined('PmWiki')) exit();

SDV($EnableOpenUrl, 1);
SDV(
    $AuthUser['htpasswd'], 
    "$LocalDir/.htpasswd"
    );
include_once("$FarmC/security/e-protect.php");
include_once("$FarmC/security/openurls.php");
include_once("$FarmC/security/htpasswdform.php");

$DefaultPasswords['admin'] = 'id:BenWilson @admins @admin';
$DefaultPasswords['edit']  = $DefaultPasswords['upload'] = '@editors';
if ($isPrivateFarm) $DefaultPasswords['read']  = 'id:*';

include_once("$FarmD/scripts/authuser.php");
$Author = $AuthId;
