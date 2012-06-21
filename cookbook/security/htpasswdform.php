<?php /*>*/ if (!defined('PmWiki')) exit();
/*
 * HtpasswdForm - An Htpasswd file editor for PmWiki 2.x
 * Copyright 2005-2007 by D.Faure (dfaure@cpan.org)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * See http://www.pmwiki.org/wiki/Cookbook/HtpasswdForm for info.
 */
$RecipeInfo['HtpasswdForm']['Version'] = '20070321-2';

include_once("$FarmD/scripts/authuser.php");

SDV($HtpasswordAuth, 'admin');
SDV($HtpasswordDefaultType, 0);
SDV($HtpasswordDefaultGroup, '');
SDVA($HtpasswordTypes, array(
/*          label   salt      timestamp? */
0 => array('apr1',  '$apr1$', true),
1 => array('crypt', null,     false),
2 => array('SHA-1', '{SHA}',  false),
));
SDVA($HtpasswordMsgFmt, array(
'created' => "<h3 class='wikimessage'>$['%s' has been created.]</h3>",
'deleted' => "<h3 class='wikimessage'>$['%s' has been deleted.]</h3>",
'no_name' => "<h3 class='wikimessage'>$[no name specified.]</h3>",
'unmatched' => "<h3 class='wikimessage'>$[passwords don't match.]</h3>",
'renamed' => "<h3 class='wikimessage'>$['%s' has been renamed to '%s'.]</h3>",
'passupdated' => "<h3 class='wikimessage'>$['%s' password has been updated.]</h3>",
'infoupdated' => "<h3 class='wikimessage'>$['%s' comment has been updated.]</h3>",
'usersupdated' => "<h3 class='wikimessage'>$['%s' group has been updated.]</h3>",
'no_group' => "<h3 class='wikimessage'>$[no group specified.]</h3>",
'useradded' => "<h3 class='wikimessage'>$['%s' has been added to '%s'.]</h3>",
'exists' => "<h3 class='wikimessage'>$['%s' is already defined. Choose a new one.]</h3>",
'' => "<h3 class='wikimessage'>&nbsp;</h3>",
));

$HandleActions['postadmhtpasswd'] = 'HandleHtpasswdAdmForm';
$HandleActions['postusrhtpasswd'] = 'HandleHtpasswdUsrForm';
if(IsEnabled($HtpasswordNewUsers, 0))
  $HandleActions['postnewhtpasswd'] = 'HandleHtpasswdNewForm';
Markup('htpasswdform', '<split',
  '/\\(:htpasswdform(.*?):\\)/ei', "HtpasswdForm(\$pagename, PSS('$1'))");

SDV($EnableHtpassword, HtAuthUserInit($pagename, $HtpasswdFile, 'htpasswd'));
SDV($EnableHtgroup,    HtAuthUserInit($pagename, $HtgroupFile,  'htgroup'));

function HtAuthUserInit($pagename, &$file, $authid) {
  global $AuthUser, $AuthUserPageFmt;
  foreach((array)($AuthUser[$authid]) as $f) {
    SDV($file, $f);
    break;
  }
  if(isset($file)) return true;
  SDV($AuthUserPageFmt, '$SiteGroup.AuthUser');
  $pn = FmtPageName($AuthUserPageFmt, $pagename);
  $apage = ReadPage($pn, READPAGE_CURRENT);
  if($apage && preg_match("/^\\s*({$authid}):\\s*(.*)/m", $apage['text'], $m)) {
    $file = $m[2];
    return true;
  }
  return false;
}

function HtpasswdForm($pagename, $args) {
  global $HtpasswordAuth, $EnableHtpassword, $HtpasswordNewUsers, $AuthId;
  if(RetrieveAuthPage($pagename, $HtpasswordAuth, false))
    return HtpasswdAdmForm($pagename);
  if(!$EnableHtpassword) return '';
  if($HtpasswordNewUsers && ! @$AuthId)
    return HtpasswdNewForm($pagename, $args);
  return HtpasswdUsrForm($pagename);
}

function HtpasswdAdmForm($pagename) {
  global $InputAttrs, $HtpasswordMessages, $HtpasswordTabIndex,
   $EnableHtpassword, $HtpasswdFile, $EnableHtgroup, $HtgroupFile,
   $HtpasswordTypes, $HtpasswordDefaultType, $PCache,
   $MessagesFmt, $HtpasswordMsgFmt,
   $EnableHtpasswordProfileLinks;
  if(!count($MessagesFmt)) $MessagesFmt[] = FmtPageName($HtpasswordMsgFmt[''], $pagename);
  $InputAttrs[] = 'tabindex';
  SDV($HtpasswordTabIndex, 10); $tx = $HtpasswordTabIndex;
  $out = array();
  SDV($HtpasswordMessages, "(:messages:)");
  $out[] = "$HtpasswordMessages";
  $out[] = FmtPageName("(:input form name='htpasswdadmform' '{\$PageUrl}':)", $pagename);
  $out[] = "(:input hidden action postadmhtpasswd:)\n(:table border='0':)";
  if(IsEnabled($EnableHtgroup, 1) && isset($HtgroupFile)) {
    $arr = LoadHtgroup($HtgroupFile);
    $grpCount = count($arr);
    $out[] = "(:cellnr colspan='4':)''$HtgroupFile''";
    $out[] = "(:cellnr:)\n(:cell:)'''$[Group]'''\n(:cell colspan='2':)'''$[Users]'''";
    if($grpCount > 0)
      foreach($arr as $i => $g) {
        $chk = ($i == $PCache[$pagename]['idxgrp']) ? "checked='on'" : '';
        $out[] = "(:cellnr:)(:input radio idxgrp value='{$i}' tabindex='$tx' $chk:)"; $tx++;
        $out[] = "(:cell:){$g[0]}\n(:cell colspan='2':){$g[1]}";
      }
    else
      $out[] = "(:cellnr:)\n(:cell colspan='3':)\n\\\\\n%center%(no group)\\\\\\\n";
    $out[] = "(:cellnr:)\n(:cell:)(:input submit rengrp value='$[Rename]' tabindex='$tx':)\\"; $tx++;
    $out[] = "&nbsp;(:input submit delgrp value='$[Delete]' tabindex='$tx':)"; $tx++;
    $out[] = "(:cell colspan='2':)(:input submit adduser value='$[Add a User]' tabindex='$tx':)\\"; $tx++;
    $out[] = "&nbsp;(:input submit setusers value='$[Set all Users]' tabindex='$tx':)"; $tx++;
    $out[] = "(:cellnr:)\n(:cell:)$[Group]:\n(:input text namegrp value='{$PCache[$pagename]['namegrp']}' tabindex='$tx':)"; $tx++;
    $out[] = "(:cell colspan='2':)$[User(s)]:\n(:input text users value='' tabindex='$tx':)"; $tx++;
    $out[] = "(:cellnr colspan='2':)\n(:cell colspan='2':)(:input submit newgrp value='$[Create Group]' tabindex='$tx':)";
    if(IsEnabled($EnableHtpassword, 1)) $out[] = "(:cellnr colspan='4':)\n----";
  }
  if(IsEnabled($EnableHtpassword, 1) && isset($HtpasswdFile)) {
    $out[] = "(:cellnr colspan='4':)''$HtpasswdFile''";
    $out[] = "(:cellnr:)\n(:cell:)'''$[User]'''\n(:cell:)'''$[Password]'''\n(:cell:)'''$[Comment]'''";
    $arr = LoadHtpasswd($HtpasswdFile);
    if(count($arr) > 0)
      foreach($arr as $i => $u) {
        $chk = ($i == $PCache[$pagename]['idxusr']) ? "checked='on'" : '';
        $out[] = "(:cellnr:)(:input radio idxusr value='{$i}' tabindex='$tx' $chk:)"; $tx++;
        $user = IsEnabled($EnableHtpasswordProfileLinks, 1) ? "%newwin% [[~{$u[0]}]]" : $u[0];
        $out[] = "(:cell:){$user}\n(:cell:)[@{$u[1]}@]\n(:cell:){$u[2]}";
      }
    else
      $out[] = "(:cellnr:)\n(:cell colspan='3':)\n\\\\\n%center%(no user)\\\\\\\n";
    $out[] = "(:cellnr:)\n(:cell:)(:input submit renusr value='$[Rename]' tabindex='$tx':)\\"; $tx++;
    $out[] = "&nbsp;(:input submit delusr value='$[Delete]' tabindex='$tx':)"; $tx++;
    $out[] = "(:cell:)(:input submit setpw value='$[Set Password]' tabindex='$tx':)"; $tx++;
    $out[] = "(:cell:)(:input submit setinfo value='$[Set Comment]' tabindex='$tx':)"; $tx++;
    $out[] = "(:cellnr:)\n(:cell:)$[Username]:\n(:input text nameusr value='{$PCache[$pagename]['nameusr']}' tabindex='$tx':)"; $tx++;
    $out[] = "(:cell:)$[Password]:\n(:input password passwd value='' tabindex='$tx':)"; $tx2 = ++$tx; $tx++;
    $out[] = "(:cell:)$[Comment]:\n(:input text info value='' tabindex='$tx':)"; $tx++;
    $out[] = "(:cellnr colspan='2':)\n(:cell:)$[again]:\n(:input password passwd2 value='' tabindex='$tx2':)";
    $out[] = "(:cell valign='bottom':)(:input submit newusr value='$[Create User]' tabindex='$tx':)"; $tx++;
    $out[] = "(:cellnr colspan='2':)\n(:cell:)";
    foreach($HtpasswordTypes as $i => $t) {
      $chk = ($i == $HtpasswordDefaultType) ? "checked='on'" : '';
      $out[] = "(:input radio pwtype value='{$i}' tabindex='$tx' $chk:){$t[0]}"; $tx++;
    }
    $out[] = "(:cell:)";
    if(IsEnabled($EnableHtgroup, 1) && $grpCount > 0) {
      $chk = $PCache[$pagename]['curgrp'] ? "checked='on'" : '';
      $out[] = "(:input checkbox curgrp tabindex='$tx' value='1' $chk:)$[add in selected group]"; $tx++;
    }
  }
  $out[] = "(:tableend:)(:input end:)";
  SDV($PCache[$pagename]['focus'], 'nameusr');
  if(isset($HtgroupFile) || isset($HtpasswdFile))
    $out[] = HtSetFocus('htpasswdadmform', $PCache[$pagename]['focus']);
  return implode("\n", $out);
}

function HandleHtpasswdAdmForm($pagename, $auth) {
  global $HtpasswordAuth, $EnableHtgroup, $HtgroupFile, $EnableHtpassword,
    $HtpasswdFile, $MessagesFmt, $HtpasswordMsgFmt, $HandleActions;
  $page = RetrieveAuthPage($pagename, $HtpasswordAuth, false);
  if (!$page) Abort('?unauthorized');
  PCache($pagename, $page);
  $browse = $HandleActions['browse'];
  $msg = '';
  $idxusr = $_REQUEST['idxusr'];
  $idxgrp = $_REQUEST['idxgrp'];
  if($EnableHtgroup) { // group handling
    if($_REQUEST['newgrp']) {
      $name = HtpasswdGetFormGroup($pagename, $auth);
      $arr = LoadHtgroup($HtgroupFile);
      $arr[] = array($name, $_REQUEST['users']);
      SaveHtgroup($HtgroupFile, $arr);
      $msg = sprintf($HtpasswordMsgFmt['created'], $name);
    } elseif(isset($idxgrp)) {
      if($_REQUEST['delgrp']) {
        $arr = LoadHtgroup($HtgroupFile);
        $name = $arr[$idxgrp][0];
        unset($arr[$idxgrp]);
        SaveHtgroup($HtgroupFile, $arr);
        $msg = sprintf($HtpasswordMsgFmt['deleted'], $name);
      } elseif($_REQUEST['rengrp']) {
        $new = HtpasswdGetFormGroup($pagename, $auth);
        $arr = LoadHtgroup($HtgroupFile);
        $name = $arr[$idxgrp][0];
        $arr[$idxgrp][0] = $new;
        SaveHtgroup($HtgroupFile, $arr);
        $msg = sprintf($HtpasswordMsgFmt['renamed'], $name, $new);
      } elseif($_REQUEST['adduser']) {
        $user = $_REQUEST['users'];
        if($EnableHtpassword && !$user && isset($idxusr)) {
          $arr = LoadHtpasswd($HtpasswdFile);
          $user = $arr[$idxusr][0];
        }
        if($user) {
          $arr = LoadHtgroup($HtgroupFile);
          $name = $arr[$idxgrp][0];
          HtgroupAddUser($arr, $name, $user);
          SaveHtgroup($HtgroupFile, $arr);
          $msg = sprintf($HtpasswordMsgFmt['useradded'], $user, $name);
        }
      } elseif($_REQUEST['setusers']) {
        $arr = LoadHtgroup($HtgroupFile);
        $name = $arr[$idxgrp][0];
        $arr[$idxgrp][1] = $_REQUEST['users'];
        SaveHtgroup($HtgroupFile, $arr);
        $msg = sprintf($HtpasswordMsgFmt['usersupdated'], $name);
      }
    }
  }
  if($EnableHtpassword) { // user handling
    if($_REQUEST['newusr']) {
      $name = HtpasswdGetFormName($pagename, $auth, true);
      $pass = HtpasswdGetFormPasswd($pagename, $auth, $_REQUEST['pwtype']);
      $info = $_REQUEST['info'];
      $arr = LoadHtpasswd($HtpasswdFile);
      $arr[] = array($name, $pass, $info);
      SaveHtpasswd($HtpasswdFile, $arr);
      if($EnableHtgroup && isset($idxgrp) && $_REQUEST['curgrp']) {
        $arr = LoadHtgroup($HtgroupFile);
        if(count($arr)) {
          $group = $arr[$idxgrp][0];
          HtgroupAddUser($arr, $group, $name);
          SaveHtgroup($HtgroupFile, $arr);
        }
      }
      $msg = sprintf($HtpasswordMsgFmt['created'], $name);
    } elseif(isset($idxusr)) {
      if($_REQUEST['delusr']) {
        $arr = LoadHtpasswd($HtpasswdFile);
        $name = $arr[$idxusr][0];
        unset($arr[$idxusr]);
        SaveHtpasswd($HtpasswdFile, $arr);
        $msg = sprintf($HtpasswordMsgFmt['deleted'], $name);
      } elseif($_REQUEST['renusr']) {
        $new = HtpasswdGetFormName($pagename, $auth, true);
        $arr = LoadHtpasswd($HtpasswdFile);
        $name = $arr[$idxusr][0];
        $arr[$idxusr][0] = $new;
        SaveHtpasswd($HtpasswdFile, $arr);
        $msg = sprintf($HtpasswordMsgFmt['renamed'], $name, $new);
      } elseif($_REQUEST['setpw']) {
        $pass = HtpasswdGetFormPasswd($pagename, $auth, $_REQUEST['pwtype']);
        $arr = LoadHtpasswd($HtpasswdFile);
        $name = $arr[$idxusr][0];
        $arr[$idxusr][1] = $pass;
        SaveHtpasswd($HtpasswdFile, $arr);
        $msg = sprintf($HtpasswordMsgFmt['passupdated'], $name);
      } elseif($_REQUEST['setinfo']) {
        $arr = LoadHtpasswd($HtpasswdFile);
        $name = $arr[$idxusr][0];
        $arr[$idxusr][2] = $_REQUEST['info'];
        SaveHtpasswd($HtpasswdFile, $arr);
        $msg = sprintf($HtpasswordMsgFmt['infoupdated'], $name);
      }
    }
  }
  $MessagesFmt[] = FmtPageName($msg, $pagename);
  $browse($pagename, $auth);
  exit();
}

function HtpasswdUsrForm($pagename) {
  global $InputAttrs, $HtpasswordForms, $PCache, $AuthId;
  $InputAttrs[] = 'tabindex';
  SDV($HtpasswordForms['user'], "(:messages:)
(:input form name='htpasswdusrform' '{\$PageUrl}':)(:input hidden action postusrhtpasswd:)
(:table border='0':)
(:cellnr:)$[Name]:\n(:input text nameusr value='_nameusr_' tabindex='1':)
(:cell:)$[Old Password]:\n(:input password passwd0 value='' tabindex='2':)
(:cellnr:)\n(:cell:)$[New Password]:\n(:input password passwd value='' tabindex='3':)
(:cellnr:)\n(:cell:)$[again]:\n(:input password passwd2 value='' tabindex='4':)
(:cell valign='bottom':)(:input submit change value='$[Change Password]' tabindex='5':)
(:tableend:)(:input end:)");
  SDV($PCache[$pagename]['nameusr'], @$AuthId);
  return FmtPageName(str_replace('_nameusr_',
                                 $PCache[$pagename]['nameusr'],
                                 $HtpasswordForms['user']),
                     $pagename) . HtSetFocus('htpasswdusrform', 'nameusr');
}

function HandleHtpasswdUsrForm($pagename, $auth) {
  global $EnableHtpassword, $HtpasswdFile, $HtpasswordTypes, $HtpasswordDefaultType,
    $MessagesFmt, $HtpasswordMsgFmt, $AuthId, $HtpasswordAutoLogin, $HandleActions;
  if($EnableHtpassword) {
    $msg = '';
    if($_REQUEST['change']) {
      $name = HtpasswdGetFormName($pagename, $auth);
      $arr = LoadHtpasswd($HtpasswdFile);
      for($i = 0; $i < count($arr); $i++) {
        if($name == $arr[$i][0]) {
          $plain = $_REQUEST['passwd0'];
          $old = $arr[$i][1];
          if(!($old || $plain) || _crypt($plain, $old) == $old) {
            $arr[$i][1] = HtpasswdGetFormPasswd($pagename, $auth,
                                                $HtpasswordDefaultType,
                                                $old, false);
            SaveHtpasswd($HtpasswdFile, $arr);
            $msg = sprintf($HtpasswordMsgFmt['passupdated'], $name);
          }
          break;
        }
      }
    }
    $MessagesFmt[] = FmtPageName($msg, $pagename);
  }
  $HandleActions['browse']($pagename, $auth);
  exit();
}

function HtpasswdNewForm($pagename, $args) {
  global $InputAttrs, $HtpasswdForms;
  $InputAttrs[] = 'tabindex';
  $opt = ParseArgs($args);
  SDV($HtpasswordForms['new'], "(:messages:)
(:input form name='htpasswdnewform' '{\$PageUrl}':)(:input hidden action postnewhtpasswd:)
(:table border='0':)
(:cellnr:)\n(:cell:)$[Name]:\n(:input text nameusr value='' tabindex='1':)
(:cell:)$[Password]:\n(:input password passwd value='' tabindex='2':)
(:if false:)(:cell:)$[Comment]:\n(:if:)(:input hidden info value='' tabindex='4':)
(:cellnr colspan='2':)\n(:cell:)$[again]:\n(:input password passwd2 value='' tabindex='3':)
(:cell valign='bottom':)(:input submit create value='$[Create]' tabindex='5':)
(:cellnr colspan='2':)\n(:cell colspan='2':)
(:tableend:)(:input end:)");
  return FmtPageName(str_replace('postnewhtpasswd',
                                 "postnewhtpasswd:)(:input hidden page '{$opt['page']}'",
                                 $HtpasswordForms['new']),
                     $pagename) . HtSetFocus('htpasswdnewform', 'nameusr');
}
  
function HandleHtpasswdNewForm($pagename, $auth) {
  global $EnableHtpassword, $HtpasswdFile, $HtpasswordDefaultType, $HtpasswordMsgFmt,
    $EnableHtgroup, $HtgroupFile, $HtpasswordDefaultGroup, $MessagesFmt, $AuthId,
    $HtpasswordAutoLogin, $HtpasswordNewPageRedirect, $HandleActions;
  $browse = $HandleActions['browse'];
  if($EnableHtpassword) {
    $arr = LoadHtpasswd($HtpasswdFile);
    if($_REQUEST['create']) {
      $newName = HtpasswdGetFormName($pagename, $auth);
      $newPass = HtpasswdGetFormPasswd($pagename, $auth, $HtpasswordDefaultType, '', false);
      for($i = 0; $i < count($arr); $i++)
        if($newName == $arr[$i][0]) {
          $MessagesFmt[] = FmtPageName(sprintf($HtpasswordMsgFmt['exists'], $newName),
                                       $pagename);
          $browse($pagename, $auth);
          exit();
        }
      $arr[] = array($newName, $newPass);
      SaveHtpasswd($HtpasswdFile, $arr);
      if($EnableHtgroup && $HtgroupFile && $HtpasswordDefaultGroup) {
        $arr = LoadHtgroup($HtgroupFile);
        HtgroupAddUser($arr, $HtpasswordDefaultGroup, $newName);
        SaveHtgroup($HtgroupFile, $arr);
      }
      SDV($HtpasswordNewPageRedirect, $pagename);
      $target = FmtPageName($_REQUEST['page'] ?
                            $_REQUEST['page'] : $HtpasswordNewPageRedirect,
                            $pagename);
      if(IsEnabled($HtpasswordAutoLogin, 1)) {
        unset($AuthId);
        AuthUserId($target, $newName, $_REQUEST['passwd']);
      }
      if($target == $pagename) {
        $MessagesFmt[] = FmtPageName(sprintf($HtpasswordMsgFmt['created'], $newName),
                                     $pagename);
        $browse($pagename, $auth);
      }
      else
        Redirect($target);
      exit();
    }
  }
  $browse($pagename, $auth);
}

function HtpasswdGetFormName($pagename, $auth, $adm = false) {
  global $PCache, $MessagesFmt, $HtpasswordMsgFmt, $HandleActions;
  $user = $_REQUEST['nameusr'];
  if(!$user) {
    if($adm) {
      $PCache[$pagename]['idxusr'] = $_REQUEST['idxusr'];
      $PCache[$pagename]['curgrp'] = $_REQUEST['curgrp'];
      $PCache[$pagename]['idxgrp'] = $_REQUEST['idxgrp'];
      $PCache[$pagename]['namegrp'] = $_REQUEST['namegrp'];
      $PCache[$pagename]['focus'] = 'nameusr';
    }
    $MessagesFmt[] = FmtPageName($HtpasswordMsgFmt['no_name'], $pagename);
    $HandleActions['browse']($pagename, $auth);
    exit();

  }
  return $user;
}

function HtpasswdGetFormPasswd($pagename, $auth, $pwtype, $salt = '', $adm = true) {
  global $PCache, $MessagesFmt, $HtpasswordMsgFmt, $HtpasswordTypes, $HandleActions;
  $plain = $_REQUEST['passwd'];
  if($plain != $_REQUEST['passwd2']) {
    if($adm) {
      $PCache[$pagename]['idxusr'] = $_REQUEST['idxusr'];
      $PCache[$pagename]['nameusr'] = $_REQUEST['nameusr'];
      $PCache[$pagename]['curgrp'] = $_REQUEST['curgrp'];
      $PCache[$pagename]['idxgrp'] = $_REQUEST['idxgrp'];
      $PCache[$pagename]['namegrp'] = $_REQUEST['namegrp'];
      $PCache[$pagename]['focus'] = 'passwd';
    }
    $MessagesFmt[] = FmtPageName($HtpasswordMsgFmt['unmatched'], $pagename);
    $HandleActions['browse']($pagename, $auth);
    exit();
  }
  $pw = '';
  if($plain) {
    if(!$salt) {
      $salt = $HtpasswordTypes[$pwtype][1];
      if($salt && $HtpasswordTypes[$pwtype][2])
        $salt .= substr(md5(microtime() . mt_rand(10000, 32000)), 0, 8);
    }
    $pw = _crypt($plain, $salt);
  }
  return $pw;
}

function HtpasswdGetFormGroup($pagename, $auth) {
  global $PCache, $MessagesFmt, $HtpasswordMsgFmt, $HandleActions;
  $group = $_REQUEST['namegrp'];
  if(!$group) {
    $PCache[$pagename]['idxusr'] = $_REQUEST['idxusr'];
    $PCache[$pagename]['nameusr'] = $_REQUEST['nameusr'];
    $PCache[$pagename]['curgrp'] = $_REQUEST['curgrp'];
    $PCache[$pagename]['idxgrp'] = $_REQUEST['idxgrp'];
    $PCache[$pagename]['focus'] = 'namegrp';
    $MessagesFmt[] = FmtPageName($HtpasswordMsgFmt['no_group'], $pagename);
    $HandleActions['browse']($pagename, $auth);
    exit();
  }
  return $group;
}

# --- Utilities ---
function HtgroupAddUser(&$arr, $group, $user) {
  for($i = 0; $i < count($arr); $i++)
    if($arr[$i][0] == $group) {
      if($arr[$i][1]) $arr[$i][1] .= ' ';
      if(preg_match("/['\"\\s]/", $user))
        $user = "'" . preg_replace("/'/", "\\'", $user) . "'";
      $arr[$i][1] .= $user;
      break;
    }
}

function HtSetFocus($form, $name, $set = false) {
	return '<:block>' . Keep("<script language='javascript' type='text/javascript'><!--
try { document.{$form}.{$name}.focus(); }
catch(e) { document.{$form}.{$name}.focus(); } //--></script>");
}

# --- File management ---
function HtArraySort(&$arr, $flag) {
  if($flag) {
    $cmp = create_function('$x,$y', "return strcasecmp(\$x[0],\$y[0]);");
    usort($arr, $cmp);
  }
}

function LoadHtpasswd($f) {
  $arr = array();
  $fp = @fopen($f, "r");
  if($fp) {
    while($l = fgets($fp, 1024)) {
      $l = rtrim($l);
      $arr[] = explode(':', $l, 3);
    }
    fclose($fp);
  }
  return $arr;
}

function SaveHtpasswd($f, $arr) {
  global $HtpasswordSortedFile;
  HtArraySort($arr, IsEnabled($HtpasswordSortedFile, 0));
  ignore_user_abort(true);
  $fp = fopen($f, "w+");
  if(flock($fp, LOCK_EX)) {
    foreach($arr as $u)
      fputs($fp, "$u[0]:$u[1]" . ($u[2] ? ":$u[2]" : '') . "\n");
    flock($fp, LOCK_UN);
  }
  fclose($fp);
  ignore_user_abort(false);
}

function LoadHtgroup($f) {
  $arr = array();
  $fp = @fopen($f, "r");
  if($fp) {
    while($l = fgets($fp, 4096)) {
      if (preg_match('/^(\\w[^\\s:]+)\\s*:(.*)$/', trim($l), $m)) {
/*
        $gl = preg_split('/[\\s,]+/', $m[2], -1, PREG_SPLIT_NO_EMPTY);
        $arr[] = array($m[1], $gl);
#*/
        $arr[] = array($m[1], $m[2]);
      }
    }
    fclose($fp);
  }
  return $arr;
}

function SaveHtgroup($f, $arr) {
  global $HtgroupSortedFile;
  HtArraySort($arr, IsEnabled($HtgroupSortedFile, 0));
  ignore_user_abort(true);
  $fp = fopen($f, "w+");
  if(flock($fp, LOCK_EX)) {
    foreach($arr as $g)
/*      fputs($fp, "$g[0]:" . implode(' ', $g[1]) . "\n");
#*/
      fputs($fp, "$g[0]:$g[1]\n");
    flock($fp, LOCK_UN);
  }
  fclose($fp);
  ignore_user_abort(false);
}
