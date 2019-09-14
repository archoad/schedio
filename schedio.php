<?php
/*=========================================================
// File:        schedio.php
// Description: main file of Schedio
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




include("functions.php");

function headPageAuth() {
	set_var_utf8();
	header("cache-control: no-cache, must-revalidate");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Content-type: text/html; charset=utf-8");
	printf("<!DOCTYPE html>\n<html lang='fr-FR'>\n<head>\n");
	printf("<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />\n");
	printf("<link rel='icon' type='image/png' href='pict/favicon.png' />\n");
	printf("<link href='styles.php' rel='StyleSheet' type='text/css' media='all' />\n");
	printf("<script type='text/javascript' src='js/schedio.js'></script>\n");
	printf("<title>Authentification</title>\n");
	printf("</head>\n<body>\n");
}


function footPageAuth() {
	printf("</body>\n</html>\n");
}


function menuAuth($msg='') {
	global $auhtPict;
	initiateNullSession();
	headPageAuth();
	printf("<div class='authcont'>\n");
	printf("<div class='auth'>\n");
	printf("<img src=%s alt='CyberSécurité' />", $auhtPict);
	printf("</div>\n<div class='auth'>\n");
	printf("<form method='post' id='auth' action='schedio.php?action=connect' onsubmit='return champs_ok(this)'>\n");
	printf("<input type='text' size='20' maxlength='20' name='login' id='login' placeholder='Identifiant' />\n");
	printf("<input type='password' size='20' maxlength='20' name='password' id='password' placeholder='Mot de passe' />\n");
	printf("<input type='submit' id='valid' value='Connexion' />\n");
	if ($msg<>'') {
		printf("<img src='pict/help.png' alt='Aide' style='width:30px;' />");
		printf("<p>%s</p>\n", $msg);
		printf("<a href='aide.php'>(Afficher l'aide en ligne)</a>\n");
	}
	printf("</form>\n</div>\n</div>\n");
	footPageAuth();
}


function authentification($login, $password) {
	$base = dbConnect();
	$request = sprintf("SELECT * FROM users WHERE login='%s' LIMIT 1", $login);
	$result = mysqli_query($base, $request);
	$row = mysqli_fetch_object($result);
	dbDisconnect($base);
	if (isset($row)) {
		if (($login === $row->login) and (password_verify($password, $row->password))) {
			return $row;
		} else {
			return false;
		}
	} else {
		return false;
	}
}


function initiateSession($data) {
	global $mode;
	session_regenerate_id();
	date_default_timezone_set('Europe/Paris');
	$date = getdate();
	$annee = $date['year'];
	$_SESSION['mode'] = $mode;
	$_SESSION['day'] = mb_strtolower(strftime("%A %d %B %Y", time()));
	$_SESSION['hour'] = mb_strtolower(strftime("%H:%M", time()));
	$_SESSION['os'] = detectOS();
	$_SESSION['browser'] = detectBrowser();
	$_SESSION['ipaddr'] = detectIP();
	$_SESSION['uid'] = $data->id;
	$_SESSION['nom'] = $data->nom;
	$_SESSION['prenom'] = $data->prenom;
	$_SESSION['role'] = $data->role;
	$_SESSION['login'] = $data->login;
	$_SESSION['annee'] = $annee;
}


function initiateNullSession() {
	global $mode;
	session_regenerate_id();
	$_SESSION['mode'] = $mode;
	$_SESSION['role'] = '100';
	$_SESSION['uid'] = 'null';
}


function redirectUser($data) {
	global $appli_titre;
	initiateSession($data);
	$role = getRole($_SESSION['role']);
	switch ($_SESSION['role']) {
		case '1': // Administrateur
			headPage($appli_titre, sprintf("%s %s - %s", $_SESSION['prenom'], $_SESSION['nom'], $role));
			menuAdmin();
			footPage();
			break;
		case '2': // Directeur de projet
		case '3': // Chef de projet
			headPage($appli_titre, sprintf("%s %s - %s", $_SESSION['prenom'], $_SESSION['nom'], $role));
			menuUser();
			footPage();
			break;
		default:
			destroySession();
			menuAuth();
			break;
	}
}


session_start();
if (isset($_GET['action'])) {
	switch ($_GET['action']) {
		case 'connect':
			$data = authentification($_POST['login'], $_POST['password']);
			if ($data) {
				redirectUser($data);
			} else {
				menuAuth("Erreur d'authentification");
				exit();
			}
			break;
		case 'disconnect':
			menuAuth();
			break;
		default:
			break;
	}
} else {
	menuAuth();
	exit;
}

?>
