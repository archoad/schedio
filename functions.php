<?php
/*=========================================================
// File:        functions.php
// Description: global functions of Schedio
// Created:     2019-09-08
// Licence:     GPL-3.0-or-later
// Copyright 2019 Michel Dubois

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <https://www.gnu.org/licenses/>.
=========================================================*/




// --------------------
// Définition des variables de base
// Nom de la machine hébergeant le serveur MySQL
$servername = 'localhost';
// Nom de la base de données
$dbname = 'schedio';
// Nom de l'utilisateur autorisé à se connecter sur la BDD
$login = 'web';
// Mot de passe de connexion
$passwd = 'webphpsql';
// Titre de l'application
$appli_titre = ("Schedio - Gestion de projet");
$appli_titre_short = ("Schedio");
// Thème CSS
$cssTheme = 'standard';
// Image accueil
$auhtPict = 'pict/accueil.png';
// Mode captcha
$captchaMode = 'num'; // 'txt' or 'num'
// --------------------




// --------------------
// Définition des variables internes à l'application
// Ne pas modifier ces variables !
date_default_timezone_set('Europe/Paris');
setlocale(LC_ALL, 'fr_FR.utf8');
ini_set('error_reporting', -1);
ini_set('display_error', 1);
ini_set('session.use_trans_sid', 0);
//ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cache_limiter', 'nocache');
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.cookie_lifetime', 0);
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.gc_probability', 1);
ini_set('session.gc_maxlifetime', 1800); // 30 min
ini_set('session.sid_length', 48);
ini_set('session.sid_bits_per_character', 6);
ini_set('session.cookie_httponly', 1);
ini_set('session.entropy_length', 32);
ini_set('session.entropy_file', '/dev/urandom');
ini_set('session.hash_function', 'sha256');
ini_set('filter.default', 'full_special_chars');
ini_set('filter.default_flags', 0);

$cspReport = "csp_parser.php";
$server_path = dirname($_SERVER['SCRIPT_FILENAME']);
$cheminMD = sprintf("%s/data/", $server_path);
// --------------------


function menuAdmin() {
	genSyslog(__FUNCTION__);
	$_SESSION['curr_script'] = 'admin.php';
	printf("<div class='row'>\n");
	printf("<div class='column left'>\n");
	linkMsg("admin.php?action=new_user", "Ajouter un utilisateur", "add_user.png", 'menu');
	linkMsg("admin.php?action=modif_user", "Modifier un utilisateur", "modif_user.png", 'menu');
	printf("</div>\n<div class='column right'>\n");
	linkMsg("admin.php?action=maintenance", "Maintenance de la Base de Données", "bdd.png", 'menu');
	linkMsg("admin.php?action=password", "Changer de mot de passe", "cadenas.png", 'menu');
	printf("</div>\n</div>");
}


function menuUser() {
	genSyslog(__FUNCTION__);
	$_SESSION['curr_script'] = 'user.php';
	printf("<div class='row'>\n");
	printf("<div class='column left'>\n");
	linkMsg("user.php?action=project_mgmt", "Gestion de projet", "project_mgmt.png", 'menu');
	linkMsg("user.php?action=kanban", "Gestion du temps (Kanban)", "kanban.png", 'menu');
	linkMsg("user.php?action=password", "Changer de mot de passe", "cadenas.png", 'menu');
	printf("</div>\n<div class='column right'>\n");
	linkMsg("user.php?action=gantt", "Diagramme de Gantt", "gantt.png", 'menu');
	if ($_SESSION['role']==='2') {
		linkMsg("user.php?action=new_project", "Ajouter un projet", "add_project.png", 'menu');
		linkMsg("user.php?action=modif_project", "Modifier un projet", "modif_project.png", 'menu');
	}
	printf("</div>\n</div>");

}


function dbConnect(){
	global $servername, $dbname, $login, $passwd;
	$link = mysqli_connect($servername, $login, $passwd, $dbname);
	if (!$link) {
		$msg = sprintf("Erreur de connexion: %d (%s)", mysqli_connect_errno(),  mysqli_connect_error());
		linkMsg("evalsmsi.php", $msg, "alert.png");
		footPage();
	} else {
		mysqli_set_charset($link , 'utf8');
		return $link;
	}
}


function dbDisconnect($dbh){
	mysqli_close($dbh);
	$dbh=0;
}


function destroySession() {
	genSyslog(__FUNCTION__);
	session_unset();
	session_destroy();
	session_write_close();
	setcookie(session_name(),'',0,'/');
	header('Location: schedio.php');
}


function isSessionValid($role) {
	genSyslog(__FUNCTION__);
	if (!isset($_SESSION['uid']) OR (!in_array($_SESSION['role'], $role))) {
		destroySession();
		exit();
	}
}


function infoSession() {
	$_SESSION['rand'] = genNonce();
	$infoDay = sprintf("%s - %s", $_SESSION['day'], $_SESSION['hour']);
	$infoNav = sprintf("%s - %s - %s", $_SESSION['os'], $_SESSION['browser'], $_SESSION['ipaddr']);
	$infoUser = sprintf("Connecté en tant que <b>%s %s</b>", $_SESSION['prenom'], $_SESSION['nom']);
	$logoff = sprintf("<a href='schedio.php?rand=%s&action=disconnect'>Déconnexion&nbsp;<img alt='logoff' src='pict/turnoff.png' width='10'></a>", $_SESSION['rand']);
	return sprintf("Powered by σχέδιο - %s - %s - %s - %s", $infoDay, $infoNav, $infoUser, $logoff);
}


function detectIP() {
	if (isset($_SERVER)) {
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$usedIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
			$usedIP = $_SERVER['HTTP_CLIENT_IP'];
		} else {
			$usedIP = $_SERVER['REMOTE_ADDR'];
		}
	} else {
		if (getenv('HTTP_X_FORWARDED_FOR'))
			$usedIP = getenv('HTTP_X_FORWARDED_FOR');
		elseif (getenv('HTTP_CLIENT_IP'))
			$usedIP = getenv('HTTP_CLIENT_IP');
		else
			$usedIP = getenv('REMOTE_ADDR');
	}
	return $usedIP;
}


function detectBrowser() {
	$BrowserList = array (
		'Firefox' => '/Firefox/',
		'Chrome' => '/Chrome/',
		'Opera' => '/Opera/',
		'Safari' => '/Safari/',
		'Internet Explorer V6' => '/MSIE 6/',
		'Internet Explorer V7' => '/MSIE 7/',
		'Internet Explorer V8' => '/MSIE 8/',
		'Internet Explorer' => '/MSIE/'
	);
	foreach($BrowserList as $CurrBrowser=>$Match) {
		if (preg_match($Match, $_SERVER['HTTP_USER_AGENT'])) {
			break;
		}
	}
	return $CurrBrowser;
}


function detectOS() {
	$txt = $_SERVER['HTTP_USER_AGENT'];
	$OSList = array (
		'Windows 3.11' => '/Win16/',
		'Windows 95' => '/(Windows 95)|(Win95)|(Windows_95)/',
		'Windows 98' => '/(Windows 98)|(Win98)/',
		'Windows 2000' => '/(Windows NT 5.0)|(Windows 2000)/',
 		'Windows XP' => '/(Windows NT 5.1)|(Windows XP)/',
		'Windows Server 2003' => '/(Windows NT 5.2)/',
		'Windows Vista' => '/(Windows NT 6.0)/',
		'Windows 7' => '/(Windows NT 6.1)/',
		'Windows NT 4.0' => '/(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)/',
		'Windows ME' => '/Windows ME/',
		'Open BSD' => '/OpenBSD/',
		'Sun OS' => '/SunOS/',
		'iOS' => '/(iPhone)|(iPad)/',
		'Android' => '/(Android)/',
		'Linux' => '/(Linux)|(X11)/',
		'Mac OSX' => '/(Mac_PowerPC)|(Macintosh)/',
		'QNX' => '/QNX/',
		'BeOS' => '/BeOS/',
		'Search Bot'=>'/(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp)|(MSNBot)|(Ask Jeeves/Teoma)|(ia_archiver)/'
	);
	foreach($OSList as $currOS=>$match) {
		if (preg_match($match, $txt)) {
			break;
		}
	}
	return $currOS;
}


function set_var_utf8(){
	ini_set('mbstring.internal_encoding', 'UTF-8');
	ini_set('mbstring.http_input', 'UTF-8');
	ini_set('mbstring.http_output', 'UTF-8');
	ini_set('mbstring.detect_order', 'auto');
}


function genNonce() {
	$nonce = random_bytes(8);
	return base64_encode($nonce);
}


function genCspPolicy() {
	global $cspReport;
	$_SESSION['nonce'] = genNonce();
	$cspPolicy = "Content-Security-Policy: ";
	$cspPolicy .= "default-src 'none' ; ";
	$cspPolicy .= sprintf("script-src 'nonce-%s' ; ", $_SESSION['nonce']);
	$cspPolicy .= sprintf("style-src 'nonce-%s' ; ", $_SESSION['nonce']);
	$cspPolicy .= "img-src 'self' ; ";
	$cspPolicy .= "font-src 'self' ; ";
	$cspPolicy .= "connect-src 'self' ; ";
	$cspPolicy .= "frame-ancestors 'none' ; ";
	$cspPolicy .= "base-uri 'none' ; ";
	$cspPolicy .= sprintf("report-uri %s ; ", $cspReport);
	return $cspPolicy;
}


function genSyslog($caller) {
	global $progVersion;
	$log = array();
	$log[] = array('program' => 'schedio', 'version' => $progVersion);
	$log[] = array('function' => $caller);
	if (isset($_SESSION['login'])) {
		$log[] = array('login' => $_SESSION['login']);
	}
	if (isset($_SESSION['id_etab'])) {
		$log[] = array('etablissement' => $_SESSION['id_etab']);
	}
	if (isset($_SESSION['quiz'])) {
		$log[] = array('quiz' => $_SESSION['quiz']);
	}
	openlog("evalsmsi", LOG_PID, LOG_SYSLOG);
	syslog(LOG_INFO, json_encode($log));
	closelog();
}


function headPage($titre, $sousTitre=''){
	genSyslog(__FUNCTION__);
	set_var_utf8();
	header("cache-control: no-cache, must-revalidate");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Content-type: text/html; charset=utf-8");
	header('X-Content-Type-Options: "nosniff"');
	header("X-XSS-Protection: 1; mode=block");
	header("X-Frame-Options: deny");
	printf("<!DOCTYPE html>\n<html lang='fr-FR'>\n<head>\n");
	printf("<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />\n");
	printf("<meta http-equiv='refresh' content='600'>");
	printf("<link rel='icon' type='image/png' href='pict/favicon.png' />\n");
	printf("<link href='js/vis-timeline-graph2d.min.css' rel='stylesheet' type='text/css' media='all' />\n");
	printf("<link href='styles.php' rel='StyleSheet' type='text/css' media='all' />\n");
	printf("<script src='js/schedio.js'></script>");
	printf("<title>%s</title>\n", $titre);
	printf("</head>\n<body>\n<h1>%s</h1>\n", $titre);
	if ($sousTitre !== '') {
		printf("<h2>%s</h2>\n", $sousTitre);
	}
}


function footPage($link='', $msg=''){
	genSyslog(__FUNCTION__);
	if ($_SESSION['role']==='100') {
		printf("<div class='footer'>\n");
		printf("Aide en ligne - Retour à la page d'accueil <a href='schedio.php' class='btnWarning'>cliquer ici</a>\n");
		printf("</div>\n");
		printf("</body>\n</html>\n");
	} else {
		if (strlen($link) AND strlen($msg)) {
			printf("<div class='foot'>\n");
			printf("<a href='%s'>%s</a>\n", $link, $msg);
			printf("</div>\n");
			printf("<p>&nbsp;</p>");
		}
		printf("<div class='footer'>\n");
		printf("%s\n", infoSession());
		printf("</div>\n");
		printf("</body>\n</html>\n");
	}
}


function validForms($msg, $url, $back=True) {
	$_SESSION['token'] = generateToken();
	printf("<fieldset>\n<legend>Validation</legend>\n");
	printf("<table><tr><td>\n");
	printf("<input type='submit' value='%s' />\n", $msg);
	if ($back) {
		printf("<input type='reset' value='Effacer' />\n");
	}
	printf("<a class='valid' href='%s?action=rm_token'>Revenir</a>\n", $url);
	printf("</td></tr>\n</table>\n</fieldset>\n");
}


function linkMsg($link, $msg, $img, $class='msg') {
	printf("<div class='%s'>\n", $class);
	printf("<div><img src='pict/%s' alt='info' /></div>\n", $img);
	if ($link==='#') {
		printf("<div><p>%s</p></div>\n", $msg);
	} else {
		printf("<div><a href='%s'>%s</a></div>\n", $link, $msg);
	}
	printf("</div>\n");
}


function traiteStringToBDD($str) {
	$str = str_split($str);
	$temp = '';
	for($i=0; $i<count($str); $i++) {
		switch ($str[$i]) {
			case '+':
			case '=':
			case '|':
				$temp .= ' ';
				break;
			default:
				$temp .= $str[$i];
				break;
		}
	}
	$temp = str_split($temp);
	$output = '';
	for($i=0; $i<count($temp); $i++) {
		if (isset($temp[$i+1])) {
			$chrNum = sprintf("%d%d", ord($temp[$i]), ord($temp[$i+1]));
			switch ($chrNum) {
				case '4039': // remove ('
				case '3941': // remove ')
				case '4041': // remove ()
				case '4747': // remove //
					$output .= ' ';
					$i += 1;
					break;
				default:
					$output .= $temp[$i];
					break;
			}
		} else {
			$output .= $temp[$i];
		}
	}
	$output = strip_tags($output);
	return htmlspecialchars($output, ENT_QUOTES, 'UTF-8');
}


function traiteStringFromBDD($str){
	return htmlspecialchars_decode($str, ENT_QUOTES);
}


function generateToken() {
	$token = hash('sha3-256', random_bytes(32));
	return $token;
}


function changePassword() {
	genSyslog(__FUNCTION__);
	$script = $_SESSION['curr_script'];
	$base = dbConnect();
	$request = sprintf("SELECT * FROM users WHERE login='%s' LIMIT 1", $_SESSION['login']);
	$result=mysqli_query($base, $request);
	dbDisconnect($base);
	if (mysqli_num_rows($result)) {
		$row = mysqli_fetch_array($result);
		printf("<form method='post' id='chg_password' action='%s?action=chg_password'>\n", $script);
		printf("<fieldset>\n<legend>Changement de mot de passe</legend>\n");
		printf("<table>\n<tr><td>\n");
		printf("<input type='password' size='50' maxlength='50' name='new1' id='new1' placeholder='Nouveau mot de passe' autocomplete='new-password' required />\n");
		printf("</td></tr>\n<tr><td>\n");
		printf("<input type='password' size='50' maxlength='50' name='new2' id='new2' placeholder='Saisissez à nouveau le mot de passe' autocomplete='new-password' required/>\n");
		printf("</td></tr>\n</table>\n");
		printf("</fieldset>\n");
		validForms('Enregistrer', $script);
		printf("</form>\n");
		printf("<script>document.getElementById('new1').addEventListener('change', function(){validatePattern();});</script>\n");
		printf("<script>document.getElementById('new2').addEventListener('change', function(){validatePassword();});</script>\n");
	} else {
		linkMsg($script, "Erreur de compte.", "alert.png");
		footPage($script, "Accueil");
	}
}


function recordNewPassword($passwd) {
	genSyslog(__FUNCTION__);
	$base = dbConnect();
	$passwd = password_hash($passwd, PASSWORD_BCRYPT);
	$request = sprintf("UPDATE users SET password='%s' WHERE login='%s'", $passwd, $_SESSION['login']);
	if (isset($_SESSION['token'])) {
		unset($_SESSION['token']);
		if (mysqli_query($base, $request)) {
			dbDisconnect($base);
			return true;
		} else {
			dbDisconnect($base);
			return false;
		}
	} else {
		dbDisconnect($base);
		return false;
	}
	return false;
}


function getRole($id) {
	$base = dbConnect();
	$request = sprintf("SELECT intitule FROM role WHERE id='%d' LIMIT 1", intval($id));
	$result = mysqli_query($base, $request);
	$row = mysqli_fetch_object($result);
	dbDisconnect($base);
	return $row->intitule;
}


function getChapter($id) {
	$base = dbConnect();
	$request = sprintf("SELECT * FROM chapter WHERE id='%d' LIMIT 1", intval($id));
	$result = mysqli_query($base, $request);
	$row = mysqli_fetch_object($result);
	dbDisconnect($base);
	return sprintf("%d - %s", $row->num, $row->nom);
}


function getUser($id) {
	$base = dbConnect();
	$request = sprintf("SELECT * FROM users WHERE id='%d' LIMIT 1", intval($id));
	$result = mysqli_query($base, $request);
	$row = mysqli_fetch_object($result);
	dbDisconnect($base);
	return sprintf("%s %s", $row->prenom, $row->nom);
}


function displayDate($date) {
	return strftime("%d %B %Y", strtotime($date));
}


function displayShortDate($date) {
	return strftime("%d %b %Y", strtotime($date));
}


function displayVeryShortDate($date) {
	return strftime("%d %b", strtotime($date));
}


function computeDuration($begin, $end) {
	$interval = date_diff(date_create($begin), date_create($end));
	if ($interval->invert) {
		return "Retard de ".$interval->format('%a jours');
	} else {
		return $interval->format('%a jours');
	}
}


function taskProgressBar($id) {
	$base = dbConnect();
	$request = sprintf("SELECT avancement FROM task WHERE id='%d' LIMIT 1", intval($id));
	$result = mysqli_query($base, $request);
	$record = mysqli_fetch_object($result);
	dbDisconnect($base);
	$percentage = intval($record->avancement);
	$result = sprintf("<div class='task-container'>\n<div class='task-background'>\n<div class='task-foreground' style='width: %d%%'>%d%%</div>\n</div>\n</div>\n", $percentage, $percentage);
	return $result;
}


function computeProjectProgress($id) {
	$val = 0;
	$base = dbConnect();
	$request = sprintf("SELECT avancement FROM task WHERE projet='%d' ", intval($id));
	$result = mysqli_query($base, $request);
	$max = intval($result->num_rows) * 100;
	while ($row = mysqli_fetch_object($result)) {
		$val += intval($row->avancement);
	}
	dbDisconnect($base);
	if ($max) {
		return round(100 * $val / $max);
	} else {
		return 0;
	}
}


function projectProgressBar($id) {
	$percentage = computeProjectProgress($id);
	$result = sprintf("<div class='project-container'>\n<div class='project-background'>\n<div class='project-foreground' style='width: %d%%'>%d%%</div>\n</div>\n</div>\n", $percentage, $percentage);
	return $result;
}


function isProjectClosed($id) {
	$base = dbConnect();
	$request = sprintf("SELECT complete FROM project WHERE id='%d' LIMIT 1", intval($id));
	$result = mysqli_query($base, $request);
	$record = mysqli_fetch_object($result);
	dbDisconnect($base);
	if (intval($record->complete)) {
		return true;
	} else {
		return false;
	}
}


?>
