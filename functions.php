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
$mode = 'standard'; // 'laposte' , 'standard'
// Image accueil
$auhtPict = 'pict/accueil.png';
// --------------------




// --------------------
// Définition des variables internes à l'application
// Ne pas modifier ces variables !
date_default_timezone_set('Europe/Paris');
setlocale(LC_ALL, 'fr_FR.utf8');

ini_set('display_errors','0');
ini_set('session.use_trans_sid', 0);
ini_set('session.use_cookie', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cache_limiter', 'nocache');
ini_set('session.gc_probability', 1);
ini_set('session.gc_maxlifetime', 1800); // 30 min
ini_set('session.cookie_httponly', 1);
ini_set('session.entropy_length', 32);
ini_set('session.entropy_file', '/dev/urandom');
ini_set('session.hash_function', 'sha256');

$server_path = dirname($_SERVER['SCRIPT_FILENAME']);
$cheminMD = sprintf("%s/data/", $server_path);
// --------------------



function dbConnect(){
	global $servername, $dbname, $login, $passwd;
	$dbh = mysqli_connect($servername, $login, $passwd) or die("Problème de connexion");
	mysqli_select_db($dbh, $dbname) or die("problème avec la table");
	mysqli_query($dbh, "SET NAMES 'utf8'");
	return $dbh;
}


function dbDisconnect($dbh){
	mysqli_close($dbh);
	$dbh=0;
}


function destroySession() {
	session_destroy();
	unset($_SESSION);
}


function isSessionValid($role) {
	if (!isset($_SESSION['uid']) OR (!in_array($_SESSION['role'], $role))) {
		destroySession();
		header('Location: schedio.php');
		exit();
	}
}


function infoSession() {
	$infoDay = sprintf("%s - %s", $_SESSION['day'], $_SESSION['hour']);
	$infoNav = sprintf("%s - %s - %s", $_SESSION['os'], $_SESSION['browser'], $_SESSION['ipaddr']);
	$infoUser = sprintf("Connecté en tant que <b>%s %s</b>", $_SESSION['prenom'], $_SESSION['nom']);
	$logoff = sprintf("<a href='schedio.php?action=disconnect'>Déconnexion&nbsp;<img border='0' alt='logoff' src='pict/turnoff.png' width='10'></a>");
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


function get_var_utf8(){
	$param1 = ini_get('mbstring.internal_encoding');
	$param2 = ini_get('mbstring.http_input');
	$param3 = ini_get('mbstring.http_output');
	$param4 = ini_get('mbstring.detect_order');
	printf("<b>%s %s %s %s</b>", $param1, $param2, $param3, $param4);
}


function headPage($titre, $sousTitre=''){
	set_var_utf8();
	header("cache-control: no-cache, must-revalidate");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Content-type: text/html; charset=utf-8");
	printf("<!DOCTYPE html>\n<html lang='fr-FR'>\n<head>\n");
	printf("<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />\n");
	printf("<link rel='icon' type='image/png' href='pict/favicon.png' />\n");
	printf("<link href='styles.php' rel='StyleSheet' type='text/css' media='all' />\n");
	printf("<title>%s</title>\n", $titre);
	printf("</head>\n<body>\n<h1>%s</h1>\n", $titre);
	if ($sousTitre !== '') {
		printf("<h2>%s</h2>\n", $sousTitre);
	} else {
		printf("<h2>%s</h2>\n", uidToEtbs());
	}
}


function footPage($link='', $msg=''){
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
	printf("<fieldset>\n<legend>Validation</legend>\n");
	printf("<table><tr><td>\n");
	printf("<input type='submit' value='%s' />\n", $msg);
	if ($back) {
		printf("<input type='reset' value='Effacer' />\n");
	}
	printf("<a class='valid' href='%s'>Revenir</a>\n", $url);
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
	$str = trim($str);
	if (!get_magic_quotes_gpc()) {
		$str = addslashes($str);
	}
	return htmlentities($str, ENT_QUOTES, 'UTF-8');
}


function traiteStringFromBDD($str){
	$str = trim($str);
	$str = stripslashes($str);
	return html_entity_decode($str, ENT_QUOTES, 'UTF-8');
}


function changePassword($script) {
	$base = dbConnect();
	$request = sprintf("SELECT * FROM users WHERE login='%s' LIMIT 1", $_SESSION['login']);
	$result=mysqli_query($base, $request);
	dbDisconnect($base);
	if (mysqli_num_rows($result)) {
		$row = mysqli_fetch_array($result);
		printf("<form method='post' id='chg_password' action='%s?action=chg_password' onsubmit='return password_ok(this)'>\n", $script);
		printf("<fieldset>\n<legend>Changement de mot de passe</legend>\n");
		printf("<table>\n<tr><td>\n");
		printf("<input type='password' size='50' maxlength='50' name='new1' id='new1' placeholder='Nouveau mot de passe' />\n");
		printf("</td></tr>\n<tr><td>\n");
		printf("<input type='password' size='50' maxlength='50' name='new2' id='new2' placeholder='Saisissez à nouveau le mot de passe'/>\n");
		printf("</td></tr>\n</table>\n");
		printf("</fieldset>\n");
		validForms('Enregistrer', $script);
		printf("</form>\n");
	} else {
		linkMsg("#", "Erreur de compte.", "alert.png");
		footPage($script, "Accueil");
	}
}


function recordNewPassword($passwd) {
	$base = dbConnect();
	$passwd = password_hash($passwd, PASSWORD_BCRYPT);
	$request = sprintf("UPDATE users SET password='%s' WHERE login='%s'", $passwd, $_SESSION['login']);
	if (mysqli_query($base, $request)) {
		return true;
	} else {
		return false;
	}
	dbDisconnect($base);
}


function menuAdmin() {
	printf("<div class='row'>\n");
	printf("<div class='column left'>\n");
	linkMsg("admin.php?action=new_user", "Ajouter un utilisateur", "add_user.png", 'menu');
	linkMsg("admin.php?action=modif_user", "Modifier un utilisateur", "modif_user.png", 'menu');
	printf("</div>\n<div class='column right'>\n");
	linkMsg("admin.php?action=maintenance", "Maintenance de la Base de Données", "bdd.png", 'menu');
	printf("</div>\n</div>");
}


function menuUser() {
	printf("<div class='row'>\n");
	printf("<div class='column left'>\n");
	linkMsg("user.php?action=project_mgmt", "Gestion de projet", "project_mgmt.png", 'menu');
	linkMsg("user.php?action=password", "Changer de mot de passe", "cadenas.png", 'menu');
	printf("</div>\n<div class='column right'>\n");
	if ($_SESSION['role']==='2') {
		linkMsg("user.php?action=new_project", "Ajouter un projet", "add_project.png", 'menu');
		linkMsg("user.php?action=modif_project", "Modifier un projet", "modif_project.png", 'menu');
	}
	printf("</div>\n</div>");

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
