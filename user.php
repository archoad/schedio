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
$authorizedRole = array('2', '3', '4');
isSessionValid($authorizedRole);
headPage($appli_titre, sprintf("%s %s - %s", $_SESSION['prenom'], $_SESSION['nom'], getRole($_SESSION['role'])));




if (isset($_GET['action'])) {
	switch ($_GET['action']) {

	case 'new_project':
		createProject();
		footPage();
		break;

	case 'record_project':
		if (recordProject('add')) {
			linkMsg($_SESSION['curr_script'], "Projet ajouté dans la base", "ok.png");
		} else {
			linkMsg($_SESSION['curr_script'], "Erreur d'enregistrement", "alert.png");
		}
		footPage();
		break;

	case 'modif_project':
		if (empty($_POST['project'])) {
			selectProjectModif();
		} else {
			$_SESSION['current_project'] = $_POST['project'];
			modifProject();
		}
		footPage();
		break;

	case 'update_project':
		if (recordProject('update')) {
			linkMsg($_SESSION['curr_script'], "Projet modifié dans la base", "ok.png");
		} else {
			linkMsg($_SESSION['curr_script'], "Erreur de modification", "alert.png");
		}
		footPage();
		break;

	case 'project_mgmt':
		displayProjects();
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

	case 'record_task':
		if (recordNewTask()) {
			header("Location: ".$_SESSION['curr_script']."?action=mgmt&value=".$_SESSION['project']);
		} else {
			linkMsg($_SESSION['curr_script'], "Erreur d'enregistrement", "alert.png");
			footPage();
		}
		break;

	case 'task_increase':
		if (incrDecrTask('increase')) {
			header("Location: ".$_SESSION['curr_script']."?action=mgmt&value=".$_SESSION['project']);
		} else {
			linkMsg($_SESSION['curr_script'], "Erreur d'enregistrement", "alert.png");
			footPage();
		}
		break;

	case 'task_decrease':
		if (incrDecrTask('decrease')) {
			header("Location: ".$_SESSION['curr_script']."?action=mgmt&value=".$_SESSION['project']);
		} else {
			linkMsg($_SESSION['curr_script'], "Erreur d'enregistrement", "alert.png");
			footPage();
		}
		break;

	case 'actions':
		if (isset($_GET['value'])) {
			$_SESSION['task'] = intval($_GET['value']);
			taskDetail();
			footPage($_SESSION['curr_script']."?action=mgmt&value=".$_SESSION['project'], "Accueil");
		} else {
			header("Location: ".$_SESSION['curr_script']."?action=mgmt");
		}
		break;

	case 'read_actions':
		if (isset($_GET['value'])) {
			$_SESSION['task'] = intval($_GET['value']);
			taskDetail();
			footPage($_SESSION['curr_script']."?action=mgmt&value=".$_SESSION['project'], "Accueil");
		} else {
			header("Location: ".$_SESSION['curr_script']."?action=mgmt");
		}
		break;

	case 'mgmt':
		if (isset($_GET['value'])) {
			$_SESSION['project'] = intval($_GET['value']);
			$referer = explode('=', $_SERVER['HTTP_REFERER'])[1];
			if ($referer == 'gantt') {
				displayProjectHead();
				footPage($_SESSION['curr_script']."?action=gantt", "Accueil");
			} else {
				projectDetail();
				footPage($_SESSION['curr_script']."?action=project_mgmt", "Accueil");
			}
		} else {
			header("Location: ".$_SESSION['curr_script']."?action=project_mgmt");
		}
		break;

	case 'complete':
		if (isset($_GET['value'])) {
			if (recordProject('close')) {
				header("Location: ".$_SESSION['curr_script']."?action=project_mgmt");
			} else {
				linkMsg($_SESSION['curr_script'], "Erreur d'enregistrement", "alert.png");
				footPage();
			}
		} else {
			header("Location: ".$_SESSION['curr_script']."?action=project_mgmt");
		}
		break;

	case 'record_action':
		if (recordAction()) {
			header("Location: ".$_SESSION['curr_script']."?action=mgmt&value=".$_SESSION['project']);
		} else {
			linkMsg($_SESSION['curr_script'], "Erreur d'enregistrement", "alert.png");
			footPage();
		}
		break;

	case 'kanban':
		displayKanban();
		footPage($_SESSION['curr_script'], "Accueil");
		break;

	case 'add_kanban':
		if (recordKanban('add')) {
			header("Location: ".$_SESSION['curr_script']."?action=kanban");
		} else {
			linkMsg($_SESSION['curr_script'], "Erreur d'enregistrement", "alert.png");
			footPage();
		}
		break;

	case 'update_kanban':
		if (recordKanban('update')) {
			header("Location: ".$_SESSION['curr_script']."?action=kanban");
		} else {
			linkMsg($_SESSION['curr_script'], "Erreur d'enregistrement", "alert.png");
			footPage();
		}
		break;

	case 'modify_kanban':
		if (recordKanban('modify')) {
			header("Location: ".$_SESSION['curr_script']."?action=kanban");
		} else {
			linkMsg($_SESSION['curr_script'], "Erreur d'enregistrement", "alert.png");
			footPage();
		}
		break;

	case 'del_kanban':
		if (isset($_GET['kid'])) {
			if (recordKanban('delete')) {
				header("Location: ".$_SESSION['curr_script']."?action=kanban");
			} else {
				linkMsg($_SESSION['curr_script'], "Erreur d'effacement", "alert.png");
				footPage();
			}
		} else {
			header("Location: ".$_SESSION['curr_script']."?action=kanban");
		}
		break;

	case 'gantt':
		displayGantts();
		footPage($_SESSION['curr_script'], "Accueil");
		break;

	case 'rm_token':
		if (isset($_SESSION['token'])) {
			unset($_SESSION['token']);
		}
		menuUser();
		footPage();
		break;

	default:
		if (isset($_SESSION['token'])) {
			unset($_SESSION['token']);
		}
		menuUser();
		footPage();
	}
} else {
	menuUser();
	footPage();
}



?>

<script src='js/graphs.js'></script>
<script src='js/vis-timeline-graph2d.min.js'></script>
