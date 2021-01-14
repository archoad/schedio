<?php
/*=========================================================
// File:        config.php
// Description: configuration of Schedio
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


// Nom de la machine hébergeant le serveur MySQL
$servername = 'localhost';
// Nom de la base de données
$dbname = 'schedio';
// Nom de l'utilisateur autorisé à se connecter sur la BDD
$login = 'web';
// Mot de passe de connexion
$passwd = 'webphpsql';
// Titre de l'application
$appli_titre = "Schedio - Gestion de projet";
$appli_titre_short = "Schedio";
// Thème CSS
$cssTheme = 'green'; // glp, beige, blue, green
// Image accueil
$auhtPict = 'pict/accueil.png';
// Mode captcha
$captchaMode = 'num'; // 'txt' or 'num'
// Webauthn attestation mode
$attestationMode = 'direct'; // 'none' or 'indirect' or 'direct'
// Session length
$sessionDuration = 3600; // 60 minutes



return array(
	'servername' => $servername,
	'dbname' => $dbname,
	'login' => $login,
	'passwd' => $passwd,
	'appli_titre' => $appli_titre,
	'appli_titre_short' => $appli_titre_short,
	'cssTheme' => $cssTheme,
	'auhtPict' => $auhtPict,
	'captchaMode' => $captchaMode,
	'attestationMode' => $attestationMode,
	'sessionDuration' => $sessionDuration
);

?>
