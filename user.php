<?php
/*=========================================================
// File:        user.php
// Description: user page of Schedio
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
include("funct_user.php");
session_start();
$authorizedRole = array('2');
isSessionValid($authorizedRole);
headPage($appli_titre, "Directeur de projet");
$script = basename($_SERVER['PHP_SELF']);

if (isset($_GET['action'])) {
	switch ($_GET['action']) {
	case 'new_project':
		createProject();
		footPage();
		break;

	case 'record_project':
		if (recorProject('add')) {
			linkMsg($script, "Projet ajouté dans la base", "ok.png");
		} else {
			linkMsg($script, "Erreur d'enregistrement", "alert.png");
		}
		footPage();
		break;

	case 'modif_project':
		if (empty($_POST['project'])) {
			selectProjectModif();
		} else {
			modifProject($_POST['project']);
		}
		footPage();
		break;

	case 'update_project':
		if ($id=recorProject('update')) {
		linkMsg($script, "Projet modifié dans la base", "ok.png");
	} else {
		linkMsg($script, "Erreur de modification", "alert.png");
	}
	footPage();
	break;

	default:
		menuUser();
		footPage();
	}
} else {
	menuUser();
	footPage();
}



?>

<script type='text/javascript' src='js/schedio.js'></script>
