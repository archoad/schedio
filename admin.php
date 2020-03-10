<?php
/*=========================================================
// File:        admin.php
// Description: administrator page of Schedio
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
include("funct_admin.php");
session_start();
$authorizedRole = array('1');
isSessionValid($authorizedRole);
headPage($appli_titre, "Administration");



if (isset($_GET['action'])) {
	switch ($_GET['action']) {
	case 'new_user':
		createUser();
		footPage();
		break;

	case 'record_user':
		if (recordUser('add')) {
			linkMsg($_SESSION['curr_script'], "Utilisateur ajouté dans la base", "ok.png");
		} else {
			linkMsg($_SESSION['curr_script'], "Erreur d'enregistrement", "alert.png");
		}
		footPage();
		break;

	case 'modif_user':
		if (empty($_POST['user'])) {
			selectUserModif();
		} else {
			$_SESSION['current_user'] = $_POST['user'];
			modifUser();
		}
		footPage();
		break;

	case 'update_user':
		if (recordUser('update')) {
		linkMsg($_SESSION['curr_script'], "Utilisateur modifié dans la base", "ok.png");
	} else {
		linkMsg($_SESSION['curr_script'], "Erreur de modification", "alert.png");
	}
	footPage();
	break;

	case 'maintenance':
		maintenanceBDD();
		footPage($_SESSION['curr_script'], "Accueil");
		break;

	case 'password':
		changePassword();
		footPage();
		break;

	case 'chg_password':
		if (recordNewPassword($_POST['new1'])) {
			linkMsg($_SESSION['curr_script'], "Mot de passe changé avec succès", "ok.png");
		} else {
			linkMsg($_SESSION['curr_script'], "Erreur de changement de mot de passe", "alert.png");
		}
		footPage();
		break;

	case 'rm_token':
		if (isset($_SESSION['token'])) {
			unset($_SESSION['token']);
		}
		menuAdmin();
		footPage();
		break;

	default:
		if (isset($_SESSION['token'])) {
			unset($_SESSION['token']);
		}
		menuAdmin();
		footPage();
		break;
	}
} else {
	menuAdmin();
	footPage();
}

?>
