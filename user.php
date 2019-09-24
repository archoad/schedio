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
$authorizedRole = array('2', '3');
isSessionValid($authorizedRole);
headPage($appli_titre, sprintf("%s %s - %s", $_SESSION['prenom'], $_SESSION['nom'], getRole($_SESSION['role'])));
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
		if ($id = recorProject('update')) {
			linkMsg($script, "Projet modifié dans la base", "ok.png");
		} else {
			linkMsg($script, "Erreur de modification", "alert.png");
		}
		footPage();
		break;

	case 'project_mgmt':
		displayProjects();
		footPage($script, "Accueil");
		break;

	case 'password':
		changePassword($script);
		footPage();
		break;

	case 'chg_password':
		if (recordNewPassword($_POST['new1'])) {
			linkMsg($script, "Mot de passe changé avec succès", "ok.png");
		} else {
			linkMsg($script, "Erreur de changement de mot de passe", "alert.png");
		}
		footPage();
		break;

	case 'record_task':
		if (recorTask('add')) {
			header("Location: ".$script."?action=mgmt&value=".$_SESSION['project']);
		} else {
			linkMsg($script, "Erreur d'enregistrement", "alert.png");
			footPage();
		}
		break;

	case 'task_increase':
		if (recorTask('increase')) {
			header("Location: ".$script."?action=mgmt&value=".$_SESSION['project']);
		} else {
			linkMsg($script, "Erreur d'enregistrement", "alert.png");
			footPage();
		}
		break;

	case 'task_decrease':
		if (recorTask('decrease')) {
			header("Location: ".$script."?action=mgmt&value=".$_SESSION['project']);
		} else {
			linkMsg($script, "Erreur d'enregistrement", "alert.png");
			footPage();
		}
		break;

	case 'actions':
		if (isset($_GET['value'])) {
			$_SESSION['task'] = intval($_GET['value']);
			taskDetail();
			footPage($script."?action=mgmt&value=".$_SESSION['project'], "Accueil");
		} else {
			header("Location: ".$script."?action=mgmt");
		}
		break;

	case 'mgmt':
		if (isset($_GET['value'])) {
			$_SESSION['project'] = intval($_GET['value']);
			projectDetail();
			footPage($script."?action=project_mgmt", "Accueil");
		} else {
			header("Location: ".$script."?action=project_mgmt");
		}
		break;

	case 'complete':
		if (isset($_GET['value'])) {
			if (recorProject('close')) {
				header("Location: ".$script."?action=project_mgmt");
			} else {
				linkMsg($script, "Erreur d'enregistrement", "alert.png");
				footPage();
			}
		} else {
			header("Location: ".$script."?action=project_mgmt");
		}
		break;

	case 'record_action':
		if (recordAction()) {
			header("Location: ".$script."?action=mgmt&value=".$_SESSION['project']);
		} else {
			linkMsg($script, "Erreur d'enregistrement", "alert.png");
			footPage();
		}
		break;

	case 'kanban':
		displayKanban();
		footPage($script, "Accueil");
		break;

	case 'add_kanban':
		if (recordKanban('add')) {
			header("Location: ".$script."?action=kanban");
		} else {
			linkMsg($script, "Erreur d'enregistrement", "alert.png");
			footPage();
		}
		break;

	case 'update_kanban':
		if (recordKanban('update')) {
			header("Location: ".$script."?action=kanban");
		} else {
			linkMsg($script, "Erreur d'enregistrement", "alert.png");
			footPage();
		}
		break;

	case 'modify_kanban':
		if (recordKanban('modify')) {
			header("Location: ".$script."?action=kanban");
		} else {
			linkMsg($script, "Erreur d'enregistrement", "alert.png");
			footPage();
		}
		break;

	case 'del_kanban':
		if (isset($_GET['kid'])) {
			if (recordKanban('delete')) {
				header("Location: ".$script."?action=kanban");
			} else {
				linkMsg($script, "Erreur d'effacement", "alert.png");
				footPage();
			}
		} else {
			header("Location: ".$script."?action=kanban");
		}
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
