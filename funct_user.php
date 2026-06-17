<?php
/*=========================================================
// File:        funct_user.php
// Description: user functions of Schedio
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


function denyAccess($back = "user.php") {
	http_response_code(403);
	linkMsg($back, "Accès non autorisé", "alert.png");
	footPage();
	exit();
}


function getProjectRecord($projectId) {
	$base = dbConnect();
	$record = dbFetchObjectPrepared($base, "SELECT * FROM project WHERE id=? LIMIT 1", "i", intval($projectId));
	dbDisconnect($base);
	return $record ?: null;
}


function getTaskRecord($taskId) {
	$base = dbConnect();
	$record = dbFetchObjectPrepared($base, "SELECT * FROM task WHERE id=? LIMIT 1", "i", intval($taskId));
	dbDisconnect($base);
	return $record ?: null;
}


function getKanbanRecord($kanbanId) {
	$base = dbConnect();
	$record = dbFetchObjectPrepared($base, "SELECT * FROM kanban WHERE id=? LIMIT 1", "i", intval($kanbanId));
	dbDisconnect($base);
	return $record ?: null;
}


function canViewProject($projectId) {
	$project = getProjectRecord($projectId);
	if (!$project) { return false; }

	$uid = intval($_SESSION["uid"]);
	$role = intval($_SESSION["role"]);

	switch ($role) {
		case 2:
			return intval($project->directeur) === $uid;
		case 3:
			return intval($project->chef) === $uid;
		case 4:
			return true;
		default:
			return false;
	}
}


function canEditProject($projectId) {
	$project = getProjectRecord($projectId);
	if (!$project) { return false; }
	return (
		intval($_SESSION["role"]) === 2 &&
		intval($project->directeur) === intval($_SESSION["uid"]) &&
		!intval($project->complete)
	);
}


function canCloseProject($projectId) {
	$project = getProjectRecord($projectId);
	if (!$project) { return false; }
	return (
		intval($_SESSION["role"]) === 2 &&
		intval($project->directeur) === intval($_SESSION["uid"]) &&
		!intval($project->complete)
	);
}


function canManageProjectTasks($projectId) {
	$project = getProjectRecord($projectId);
	if (!$project || intval($project->complete)) { return false; }

	$uid = intval($_SESSION["uid"]);
	$role = intval($_SESSION["role"]);

	if ($role === 2) {
		return intval($project->directeur) === $uid;
	}
	if ($role === 3) {
		return intval($project->chef) === $uid;
	}
	return false;
}


function canViewTask($taskId) {
	$task = getTaskRecord($taskId);
	if (!$task) { return false; }
	return canViewProject($task->projet);
}


function canEditTask($taskId) {
	$task = getTaskRecord($taskId);
	if (!$task) { return false; }
	return canManageProjectTasks($task->projet);
}


function canEditKanban($kanbanId) {
	$record = getKanbanRecord($kanbanId);
	if (!$record) { return false; }
	return intval($record->user) === intval($_SESSION["uid"]);
}


function createProject() {
	$base = dbConnect();
	$res_chefproj = mysqli_query($base, "SELECT * FROM users WHERE role='2' OR role='3'");
	$res_chapter = mysqli_query($base, "SELECT * FROM chapter");
	printf("<form method='post' id='new_project' action='user.php?action=record_project'>\n");
	printf("<fieldset>\n<legend>Ajout d'un projet</legend>\n");
	printf("<table>\n<tr>\n");
	printf("<td colspan='3'><input type='text' size='60' maxlength='60' name='nom' id='nom' placeholder='Nom' required>\n</td>");
	printf("</tr>\n<tr>\n");
	printf("<td colspan='3'><textarea name='description' id='description' cols='60' rows='3' placeholder='Description' required></textarea></td>\n");
	printf("</tr>\n<tr>\n");
	printf("<td colspan='3'>Chapitre ISO27002:&nbsp;<select name='chapter' id='chapter' required>\n");
	printf("<option selected='selected' value=''>&nbsp;</option>\n");
	while($row = mysqli_fetch_object($res_chapter)) {
		printf("<option value='%d'>%s - %s</option>\n", intval($row->id), intval($row->num), traiteStringFromBDD($row->nom));
	}
	printf("</select>\n</td>");
	printf("</tr>\n<tr>\n");
	printf("<td>Chef de projet:&nbsp;<select name='chef' id='chef' required>\n");
	printf("<option selected='selected' value=''>&nbsp;</option>\n");
	while($row = mysqli_fetch_object($res_chefproj)) {
		printf("<option value='%d'>%s %s</option>\n", intval($row->id), traiteStringFromBDD($row->prenom), traiteStringFromBDD($row->nom));
	}
	printf("</select>\n</td>\n");
	printf("<td>Date de début:&nbsp;<input type='date' name='datedebut' id='datedebut' min='%s' required></td>\n", date('Y-m-d', time()));
	printf("<td>Date de fin:&nbsp;<input type='date' name='datefin' id='datefin' min='%s' required></td>\n", date('Y-m-d', time()));
	printf("</tr>\n</table>\n</fieldset>\n");
	validForms('Enregistrer', 'user.php', true, 'record_project');
	printf("</form>\n");
	dbDisconnect($base);
	printf("<script nonce='%s'>document.getElementById('datedebut').addEventListener('change', function() {fixMinDate();});</script>\n", $_SESSION['nonce']);
}


function selectProjectModif() {
	$base = dbConnect();
	$request = sprintf("SELECT * FROM project WHERE directeur='%s'", $_SESSION['uid']);
	$result = mysqli_query($base, $request);
	printf("<form method='post' id='modif_project' action='user.php?action=modif_project'>\n");
	printf("<fieldset>\n<legend>Modification d'un projet</legend>\n");
	printf("<table>\n<tr><td>\n");
	printf("Projet:&nbsp;\n<select name='project' id='project' required>\n");
	printf("<option selected='selected' value=''>&nbsp;</option>\n");
	while($row = mysqli_fetch_object($result)) {
		if (!intval($row->complete)) {
			printf("<option value='%s'>%s</option>\n", intval($row->id), traiteStringFromBDD($row->nom));
		}
	}
	printf("</select>\n");
	printf("</td>\n</tr>\n</table>\n</fieldset>\n");
	validForms('Modifier', 'user.php', $back=False, 'select_project_modif');
	printf("</form>\n");
}


function modifProject() {
	$base = dbConnect();
	$request = sprintf("SELECT * FROM project WHERE id='%d' LIMIT 1", $_SESSION['current_project']);
	$result = mysqli_query($base, $request);
	$record = mysqli_fetch_object($result);
	$res_chefproj = mysqli_query($base, "SELECT * FROM users WHERE role='2' OR role='3'");
	$res_chapter = mysqli_query($base, "SELECT * FROM chapter");

	printf("<form method='post' id='modif_project' action='user.php?action=update_project'>\n");
	printf("<fieldset>\n<legend>Modification d'un projet</legend>\n");
	printf("<table>\n<tr>\n");
	printf("<td colspan='3'><input type='text' size='60' maxlength='60' name='nom' id='nom' value='%s' required>\n</td>", traiteStringFromBDD($record->nom));
	printf("</tr>\n<tr>\n");
	printf("<td colspan='3'><textarea name='description' id='description' cols='60' rows='3' required>%s</textarea></td>\n", htmlTextarea(traiteStringFromBDD($record->description)));
	printf("</tr>\n<tr>\n");
	printf("<td colspan='3'>Chapitre ISO27002:&nbsp;<select name='chapter' id='chapter' required>\n");
	printf("<option selected='selected' value='%d'>%s</option>\n", intval($record->chapter), getChapter($record->chapter));
	while($row = mysqli_fetch_object($res_chapter)) {
		printf("<option value='%d'>%s - %s</option>\n", intval($row->id), intval($row->num), traiteStringFromBDD($row->nom));
	}
	printf("</select>\n</td>");
	printf("</tr>\n<tr>\n");
	printf("<td>Chef de projet:&nbsp;<select name='chef' id='chef' required>\n");
	printf("<option selected='selected' value='%s'>%s</option>\n", intval($record->chef), getUser($record->chef));
	while($row = mysqli_fetch_object($res_chefproj)) {
		printf("<option value='%d'>%s %s</option>\n", intval($row->id), traiteStringFromBDD($row->prenom), traiteStringFromBDD($row->nom));
	}
	printf("</select>\n</td>\n");
	printf("<td>Date de début:&nbsp;<input type='date' name='datedebut' id='datedebut' min='%s' value='%s' required></td>\n", $record->datedebut, $record->datedebut);
	printf("<td>Date de fin:&nbsp;<input type='date' name='datefin' id='datefin' min='%s' value='%s' required></td>\n", $record->datedebut, $record->datefin);
	printf("</tr>\n</table>\n</fieldset>\n");
	validForms('Modifier', 'user.php', $back=False, 'update_project');
	printf("</form>\n");
	dbDisconnect($base);
	printf("<script nonce='%s'>document.getElementById('datedebut').addEventListener('change', function() {fixMinDate();});</script>\n", $_SESSION['nonce']);
}


function recordProject($action) {
	genSyslog(__FUNCTION__);
	switch ($action) {
		case 'add':
			$csrfAction = 'record_project';
			break;
		case 'update':
			$csrfAction = 'update_project';
			break;
		case 'close':
			$csrfAction = 'complete_project';
			break;
		default:
			return false;
	}
	if (!isCsrfValid($csrfAction)) {
		return false;
	}
	$base = dbConnect();
	switch ($action) {
		case 'add':
			if (intval($_SESSION['role']) !== 2) {
				dbDisconnect($base);
				return false;
			}
			$nom = isset($_POST['nom']) ? traiteStringToBDD($_POST['nom']) : NULL;
			$description = isset($_POST['description']) ? traiteStringToBDD($_POST['description']) : NULL;
			$chapter = isset($_POST['chapter']) ? intval(trim($_POST['chapter'])) : NULL;
			$directeur = intval($_SESSION['uid']);
			$chef = isset($_POST['chef']) ? intval(trim($_POST['chef'])) : NULL;
			$datedebut = isset($_POST['datedebut']) ? $_POST['datedebut'] : NULL;
			$datefin = isset($_POST['datefin']) ? $_POST['datefin'] : NULL;
			$dateDebutObj = DateTime::createFromFormat('Y-m-d', $datedebut);
			$dateFinObj = DateTime::createFromFormat('Y-m-d', $datefin);
			if ( empty($nom) || empty($description) || $chapter <= 0 || $chef <= 0 || !$dateDebutObj || $dateDebutObj->format('Y-m-d') !== $datedebut || !$dateFinObj || $dateFinObj->format('Y-m-d') !== $datefin || $datefin < $datedebut ) {
				dbDisconnect($base);
				return false;
			}
			$success = dbExecutePrepared($base, "INSERT INTO project (nom, description, chapter, directeur, chef, datedebut, datefin) VALUES (?, ?, ?, ?, ?, ?, ?)", "ssiiiss", $nom, $description, $chapter, $directeur, $chef, $datedebut, $datefin);
			break;
		case 'update':
			if (!isset($_SESSION['current_project'])) {
				dbDisconnect($base);
				return false;
			}
			$id = intval($_SESSION['current_project']);
			if (!canEditProject($id)) {
				dbDisconnect($base);
				return false;
			}
			$nom = isset($_POST['nom']) ? traiteStringToBDD($_POST['nom']) : NULL;
			$description = isset($_POST['description']) ? traiteStringToBDD($_POST['description']) : NULL;
			$chapter = isset($_POST['chapter']) ? intval(trim($_POST['chapter'])) : 0;
			$directeur = intval($_SESSION['uid']);
			$chef = isset($_POST['chef']) ? intval(trim($_POST['chef'])) : 0;
			$datedebut = isset($_POST['datedebut']) ? trim($_POST['datedebut']) : '';
			$datefin = isset($_POST['datefin']) ? trim($_POST['datefin']) : '';
			$dateDebutObj = DateTime::createFromFormat('Y-m-d', $datedebut);
			$dateFinObj = DateTime::createFromFormat('Y-m-d', $datefin);
			if ( empty($nom) || empty($description) || $chapter <= 0 || $chef <= 0 || !$dateDebutObj || $dateDebutObj->format('Y-m-d') !== $datedebut || !$dateFinObj || $dateFinObj->format('Y-m-d') !== $datefin || $datefin < $datedebut ) {
				dbDisconnect($base);
				return false;
			}
			$success = dbExecutePrepared($base, "UPDATE project SET nom=?, description=?, chapter=?, directeur=?, chef=?, datedebut=?, datefin=? WHERE id=?", "ssiiissi", $nom, $description, $chapter, $directeur, $chef, $datedebut, $datefin, $id);
			if ($success) {
				unset($_SESSION['current_project']);
			}
			break;
		case 'close':
			$id = isset($_POST['project_id']) ? intval($_POST['project_id']) : 0;
			if (!canCloseProject($id)) {
				dbDisconnect($base);
				return false;
			}
			$complete = 1;
			$success = dbExecutePrepared($base, "UPDATE project SET complete=? WHERE id=?", "ii", $complete, $id);
			break;
		default:
			dbDisconnect($base);
			return false;
	}
	dbDisconnect($base);
	return $success;
}


function displayProjects()
{
	$base = dbConnect();
	switch (intval($_SESSION['role'])) {
		case 2: // Directeur de projet
			$request = sprintf(
				"SELECT * FROM project WHERE directeur='%d' ORDER BY chapter ASC, complete ASC, datedebut ASC",
				intval($_SESSION['uid'])
			);
			break;
		case 4: // Manager
			$request = "SELECT * FROM project ORDER BY chapter ASC, complete ASC, datedebut ASC";
			break;
		default: // Chef de projet
			$request = sprintf(
				"SELECT * FROM project WHERE chef='%d' ORDER BY chapter ASC, complete ASC, datedebut ASC",
				intval($_SESSION['uid'])
			);
			break;
	}
	$result = mysqli_query($base, $request);
	dbDisconnect($base);
	$chapterRef = 0;
	$openTable = false;
	printf("<div class='project'>\n");
	while ($row = mysqli_fetch_object($result)) {
		$closed = isProjectClosed($row->id);
		if (intval($row->chapter) !== $chapterRef) {
			if ($openTable) {
				printf("</table>\n");
			}
			printf("<h3>%s</h3>\n", getChapter($row->chapter));
			printf("<table>\n");
			printf(
				"<tr><th>Nom</th><th>Directeur de projet</th><th>Chef de projet</th><th>Date de début</th><th>Date de fin</th><th>Avancement</th>"
			);
			if (in_array($_SESSION['role'], array('2', '4'))) {
				printf("<th>Etat</th>");
			}
			printf("</tr>\n");
			$chapterRef = intval($row->chapter);
			$openTable = true;
		}
		printf("<tr>\n");
		printf(
			"<td><a href='user.php?action=mgmt&value=%d'>%s</a></td>\n",
			intval($row->id),
			traiteStringFromBDD($row->nom)
		);
		printf("<td>%s</td>\n", getUser($row->directeur));
		printf("<td>%s</td>\n", getUser($row->chef));
		printf("<td>%s</td>\n", displayShortDate($row->datedebut));
		printf("<td>%s</td>\n", displayShortDate($row->datefin));
		if ($closed) {
			printf("<td><div class='finished'>Projet clos</div></td>\n");
		} else {
			printf("<td>%s</td>\n", projectProgressBar($row->id));
		}
		if (in_array($_SESSION['role'], array('2', '4'))) {
			if ($closed) {
				printf("<td><span class='finished'>Clos</span></td>\n");
			} elseif (computeProjectProgress($row->id) == 100) {
				if (intval($_SESSION['role']) === 2) {
					printf("<td>");
					printf("<form method='post' action='user.php?action=complete'>\n");
					csrfInput('complete_project');
					printf(
						"<input type='hidden' name='project_id' value='%d'>\n",
						intval($row->id)
					);
					printf("<input class='complete' type='submit' value='Clore'>\n");
					printf("</form>\n");
					printf("</td>\n");
				} else {
					printf("<td><span class='complete'>A clore</span></td>\n");
				}
			} else {
				printf("<td><span class='live'>Actif</span></td>\n");
			}
		}
		printf("</tr>\n");
	}
	if ($openTable) {
		printf("</table>\n");
	} else {
		printf("<table><tr><td>Aucun projet à afficher.</td></tr></table>\n");
	}
	printf("</div>\n");
}


function displayProjectHead() {
	$base = dbConnect();
	$request = sprintf("SELECT * FROM project WHERE id='%d' LIMIT 1", intval($_SESSION['project']));
	$result = mysqli_query($base, $request);
	$record = mysqli_fetch_object($result);
	$date = date_create('2000-01-01');
	dbDisconnect($base);
	printf("<div class='project'>\n<table>\n<tr>\n");
	printf("<th colspan='2' class='projet_title'>%s</th>\n",  traiteStringFromBDD($record->nom));
	printf("<th colspan='2' class='projet_detail'>%s</th>\n", htmlTextarea(traiteStringFromBDD($record->description)));
	printf("</tr>\n<tr>\n");
	printf("<th colspan='2'>%s</th>\n",  getChapter($record->chapter));
	printf("<th colspan='2'>");
	if (intval($record->complete)) {
		printf("Projet clos");
	} else {
		printf("<div class='tableauto'><div class='tablecell'>Avancement</div><div class='tablecell'>%s</div></div>", projectProgressBar($_SESSION['project']));
	}
	printf("</th>\n");
	printf("</tr>\n<tr>\n");
	printf("<th>Directeur de projet</th><td>%s</td>\n", getUser($record->directeur));
	printf("<th>Chef de projet</th><td>%s</td>\n", getUser($record->chef));
	printf("</tr>\n<tr>\n");
	printf("<th>Début</th><td>%s</td><th>Fin</th><td>%s</td>\n", displayDate($record->datedebut), displayDate($record->datefin));
	printf("</tr>\n<tr>\n");
	printf("<th>Durée</th><td>%s</td>", computeDuration($record->datedebut, $record->datefin));
	if (intval($record->complete)) {
		printf("<th colspan='2'>&nbsp;</th>\n");
	} else {
		printf("<th>Temps restant</th><td>%s</td>\n", computeDuration(date_format(date_create('now'), "Y-m-d"), $record->datefin));
	}
	printf("</tr></table></div>\n");
}


function addTask() {
	$base = dbConnect();
	$request = sprintf("SELECT * FROM project WHERE id='%d' LIMIT 1", intval($_SESSION['project']));
	$result = mysqli_query($base, $request);
	$record = mysqli_fetch_object($result);
	dbDisconnect($base);
	$today = date('Y-m-d', time());
	$interval = date_diff(date_create($record->datefin), date_create($today));
	if ($interval->invert) {
		printf("<div class='task'>\n");
		printf("<form method='post' id='new_task' action='user.php?action=record_task'>\n");
		csrfInput("record_task");
		printf("<table>\n<tr>\n");
		printf("<td><input type='text' size='40' maxlength='60' name='nom' id='nom' placeholder='Tâche' required></td>\n");
		printf("<td>De&nbsp;<input type='date' name='datedebut' id='datedebut' min='%s' max='%s' required></td>\n", $record->datedebut, $record->datefin);
		printf("<td>A&nbsp;<input type='date' name='datefin' id='datefin' min='%s' max='%s' required></td>\n", $record->datedebut, $record->datefin);
		printf("<td><input type='submit' value='+'></td>\n");
		printf("</tr>\n</table>\n");
		printf("</form>\n</div>\n");
		printf("<script nonce='%s'>document.getElementById('datedebut').addEventListener('change', function() {fixMinDate();});</script>\n", $_SESSION['nonce']);
	} else {
		printf("<div class='project'>\n");
		printf("<table><tr><td class='text-align:center;'>Date de fin de projet dépassée. Impossible de rajouter une tâche.</td></tr></table>\n");
		printf("</div>\n");
	}
}


function displayTasks($data) {
	$closed = isProjectClosed(intval($_SESSION['project']));

	printf("<div class='project'>\n");
	printf("<table>\n");
	printf("<tr>\n<th>Tâche</th><th>Début</th><th>Fin</th><th>Durée</th><th>Actions</th><th>Avancement</th></tr>\n");

	while($row = mysqli_fetch_object($data)) {
		printf("<tr>\n");

		printf("<td>%s</td>\n", traiteStringFromBDD($row->nom));
		printf("<td>%s</td>\n", displayDate($row->datedebut));
		printf("<td>%s</td>\n", displayDate($row->datefin));
		printf("<td>%s</td>\n", computeDuration($row->datedebut, $row->datefin));

		if ($closed) {
			printf("<td>&nbsp;</td>\n");
		} elseif (intval($_SESSION['role']) == 4) {
			printf(
				"<td class='center'><a class='action_plus' href='user.php?action=read_actions&value=%d'>&rarrb;</a></td>\n",
				intval($row->id)
			);
		} else {
			printf(
				"<td class='center'><a class='action_plus' href='user.php?action=actions&value=%d'>+</a></td>\n",
				intval($row->id)
			);
		}

		printf("<td><div class='tableauto'>");

		if ((!$closed) and (intval($_SESSION['role']) != 4)) {
			printf("<div class='tablecell'>");
			printf("<form method='post' action='user.php?action=task_decrease'>\n");
			csrfInput("task_decrease");
			printf(
				"<input type='hidden' name='task_id' value='%d'>\n",
				intval($row->id)
			);
			printf("<input class='project_minus' type='submit' value='-'>\n");
			printf("</form>");
			printf("</div>");
		}

		printf("<div class='tablecell'>%s</div>", taskProgressBar($row->id));

		if ((!$closed) and (intval($_SESSION['role']) != 4)) {
			printf("<div class='tablecell'>");
			printf("<form method='post' action='user.php?action=task_increase'>\n");
			csrfInput("task_increase");
			printf(
				"<input type='hidden' name='task_id' value='%d'>\n",
				intval($row->id)
			);
			printf("<input class='project_plus' type='submit' value='+'>\n");
			printf("</form>");
			printf("</div>");
		}

		printf("</div></td>\n");

		printf("</tr>\n");
	}

	printf("</table>\n</div>\n");
}


function tasksManagement() {
	$closed = isProjectClosed(intval($_SESSION['project']));
	$base = dbConnect();
	$request = sprintf("SELECT * FROM task WHERE projet='%d' ", intval($_SESSION['project']));
	$result = mysqli_query($base, $request);
	dbDisconnect($base);
	if (in_array($_SESSION['role'], array('2', '3'))) {
		if ($result->num_rows) {
			displayTasks($result);
			if (!$closed) {
				addTask();
			}
		} else {
			addTask();
		}
	} else {
		if ($result->num_rows) {
			displayTasks($result);
		}
	}
}


function projectDetail() {
	displayProjectHead();
	tasksManagement();
}


function recordNewTask() {
	$base = dbConnect();
	if (!isCsrfValid("record_task")) {
		dbDisconnect($base);
		return false;
	}
	$projet = intval($_SESSION['project']);
	if (!canManageProjectTasks($projet)) {
		dbDisconnect($base);
		return false;
	}
	$nom = isset($_POST['nom']) ? traiteStringToBDD($_POST['nom']) : NULL;
	$datedebut = isset($_POST['datedebut']) ? $_POST['datedebut'] : NULL;
	$datefin = isset($_POST['datefin']) ? $_POST['datefin'] : NULL;
	$dateDebutObj = DateTime::createFromFormat('Y-m-d', $datedebut);
	$dateFinObj = DateTime::createFromFormat('Y-m-d', $datefin);

	if (empty($nom) || !$dateDebutObj || $dateDebutObj->format('Y-m-d') !== $datedebut || !$dateFinObj || $dateFinObj->format('Y-m-d') !== $datefin || $datefin < $datedebut) {
		dbDisconnect($base);
		return false;
	}

	$success = dbExecutePrepared( $base, "INSERT INTO task (projet, nom, datedebut, datefin, avancement) VALUES (?, ?, ?, ?, 0)", "isss", $projet, $nom, $datedebut, $datefin);
	dbDisconnect($base);
	return $success;
}


function incrDecrTask($action) {
	$base = dbConnect();
	$id = isset($_POST['task_id']) ? intval($_POST['task_id']) : 0;
	$csrfAction = ($action === "increase") ? "task_increase" : "task_decrease";
	if (!isCsrfValid($csrfAction)) {
		dbDisconnect($base);
		return false;
	}
	if (!canEditTask($id)) {
		dbDisconnect($base);
		return false;
	}
	$record = dbFetchObjectPrepared($base, "SELECT avancement FROM task WHERE id=? LIMIT 1", "i", $id);
	if (!$record) {
		dbDisconnect($base);
		return false;
	}
	$progress = intval($record->avancement);
	switch ($action) {
		case 'increase':
			if ($progress < 100) { $progress += 10; }
			break;
		case 'decrease':
			if ($progress > 0) { $progress -= 10; }
			break;
		default:
			dbDisconnect($base);
			return false;
	}
	$success = dbExecutePrepared($base, "UPDATE task SET avancement=? WHERE id=?", "ii", $progress, $id);
	dbDisconnect($base);
	return $success;
}


function taskDetail() {
	displayTaskHead();
	if (intval($_SESSION['role']) != 4) {
		actionsManagement();
	} else {
		displayActions();
	}
}


function displayTaskHead() {
	$base = dbConnect();
	$request = sprintf("SELECT * FROM task WHERE id='%d' LIMIT 1", intval($_SESSION['task']));
	$result = mysqli_query($base, $request);
	$record = mysqli_fetch_object($result);
	dbDisconnect($base);
	printf("<div class='project'><table><tr>");
	printf("<td>%s</td>",  traiteStringFromBDD($record->nom));
	printf("<td>Début: %s</td>", displayDate($record->datedebut));
	printf("<td>Fin: %s</td>", displayDate($record->datefin));
	printf("<td>Durée: %s</td>", computeDuration($record->datedebut, $record->datefin));
	printf("<td><div class='tableauto'>");
	printf("<div class='tablecell'>Avancement</div>");
	printf("<div class='tablecell'>%s</div>", taskProgressBar($record->id));
	printf("</div></td>\n");
	printf("</tr></table></div>");
}


function getActionFilename() {
	global $cheminMD;
	$base = dbConnect();
	$request = sprintf("SELECT * FROM task WHERE id='%d' LIMIT 1", intval($_SESSION['task']));
	$result = mysqli_query($base, $request);
	$record = mysqli_fetch_object($result);
	dbDisconnect($base);
	return sprintf("%s%s_task%d.md", $cheminMD, str_replace('-', '', $record->datedebut), $_SESSION['task']);
}


function actionsManagement() {
	$fileName = getActionFilename();
	if ($handle = fopen($fileName, "a+")) {
		printf("<div class='project'>\n");
		printf("<form method='post' id='new_action' action='user.php?action=record_action'>\n");
		csrfInput("record_action");
		printf("<table>\n<tr>\n");
		if (filesize($fileName)) {
			$data = fread($handle, filesize($fileName));
			printf("<td><div class='actions'><textarea name='description' id='description'>%s</textarea></div></td>\n", htmlTextarea($data));
		} else {
			printf("<td><div class='actions'><textarea name='description' id='description'></textarea></div></td>\n");
		}
		fclose($handle);
		printf("</tr><tr>\n");
		printf("<td class='center'><input type='submit' value='Enregistrer'></input></td>\n");
		printf("</tr>\n</table>\n</form>\n</div>\n");
		printf("<script nonce='%s'>var simplemde = new SimpleMDE({ autoDownloadFontAwesome: false, spellChecker: false, element: document.getElementById('description') });</script>", $_SESSION['nonce']);
	} else {
		linkMsg("user.php", "Erreur d'ouverture du fichier.", "alert.png");
	}
}


function displayActions() {
	$fileName = getActionFilename();
	printf("<div class='project'>\n");
	if ($handle = fopen($fileName, "r")) {
		if (filesize($fileName)) {
			$data = fread($handle, filesize($fileName));
			printf("<textarea name='description' id='description' cols='80' rows='30' readonly>%s</textarea>\n", htmlTextarea($data));
		} else {
			printf("<textarea name='description' id='description' cols='80' rows='30' placeholder='Pas d&apos;actions enregistrées' readonly></textarea>\n");
		}
		fclose($handle);
	} else {
		printf("<textarea name='description' id='description' cols='80' rows='30' placeholder='Pas d&apos;actions enregistrées' readonly></textarea>\n");
	}
	printf("</div>\n");
}


function recordAction() {
	if (!isCsrfValid("record_action")) {
		return false;
	}
	if (!isset($_SESSION['task']) || !canEditTask($_SESSION['task'])) {
		return false;
	}
	$fileName = getActionFilename();
	if ($handle = fopen($fileName, "w")) {
		fwrite($handle, $_POST['description']);
		fclose($handle);
		return true;
	} else {
		return false;
	}
}


function displayKanbanTask($id) {
	$base = dbConnect();
	$request = sprintf("SELECT * FROM kanban WHERE id='%d' LIMIT 1", $id);
	$result = mysqli_query($base, $request);
	$record = mysqli_fetch_object($result);
	dbDisconnect($base);
	$today = date('Y-m-d', time());

	$data = sprintf("%d:%s:%s:%s:%d", $record->id, traiteStringFromBDD($record->nom), htmlTextarea(traiteStringFromBDD($record->description)), $record->datefin, $record->priority);
	$data = base64_encode($data);
	printf("<div id='task%d' class='draggable'>", $record->id);
	printf("<div class='draggable-name'>%s - P%d<div id='deltask%d' class='del_kanban'>&ndash;</div></div>", traiteStringFromBDD($record->nom), intval($record->priority), $id);
	printf("<p class='kanban_description'>%s</p>", htmlTextarea(traiteStringFromBDD($record->description)));
	$interval = date_diff(date_create($record->datefin), date_create($today));
	if ($interval->invert) {
		if ($interval->days <= 3) {
			$class = 'kanban_date_limit';
		} else {
			$class = 'kanban_date_normal';
		}
	} else {
		$class = 'kanban_date_alert';
	}
	printf("<p class='kanban_date %s'>du %s au %s (%s)</p>", $class, displayVeryShortDate($record->datedebut), displayVeryShortDate($record->datefin), computeDuration($record->datedebut, $record->datefin));
	printf("</div>");
	printf("<script nonce='%s'>document.getElementById('task%d').addEventListener('dblclick', function() {displayModifyModal(\"%s\");});</script>\n", $_SESSION['nonce'], $record->id, $data);
	printf("<script nonce='%s'>document.getElementById('deltask%d').addEventListener('click', function() {displayDelModal(\"%d\");});</script>\n", $_SESSION['nonce'], $id, $id);
}


function delKanban() {
	printf("<div id='del_kanban_form' class='modal'>");
	printf("<div class='modal_content'>");
	printf("<form method='post' id='del_kanban' action='user.php?action=del_kanban'>\n");
	csrfInput("del_kanban");
	printf("<fieldset>\n<legend>Voulez-vous supprimer la tâche?</legend>\n");
	printf("<input type='hidden' name='delid' id='delid' value='0' />\n");
	printf("<table><tr><td>\n");
	printf("<input type='submit' value='Oui' />\n");
	printf("<a class='valid' href='%s?action=kanban_rm_token'>Non</a>\n", $_SESSION['curr_script']);
	printf("</td></tr>\n</table>\n</fieldset>\n");
	printf("</form>\n");
	printf("</div></div>");
}


function addKanban() {
	printf("<div id='add_kanban_form' class='modal'>");
	printf("<div class='modal_content'>");
	printf("<form method='post' id='new_kanban' action='user.php?action=add_kanban'>\n");
	printf("<fieldset>\n<legend>Ajout d'une tâche</legend>\n");
	printf("<table>\n<tr><td colspan='2'>\n");
	printf("<input type='text' size='40' maxlength='40' name='nom' id='nom' placeholder='Nom de la tâche' required>\n");
	printf("</td></tr>\n<tr><td colspan='2'>\n");
	printf("<textarea name='description' id='description' cols='60' rows='3' placeholder='Description' required></textarea>\n");
	printf("</td></tr>\n<tr>\n");
	printf("<td>Date de fin:&nbsp;<input type='date' name='datefin' id='datefin' min='%s' required></td>\n", date('Y-m-d', time()));
	printf("<td>Priorité:&nbsp;<select name='priority' id='priority' required>\n");
	printf("<option selected='selected' value=''>&nbsp;</option>\n");
	for ($i=1; $i<=5; $i++) {
		printf("<option value='%d'>%s</option>\n", $i, $i);
	}
	printf("</select>\n</td>");
	printf("</tr>\n</table>\n</fieldset>\n");
	validForms('Enregistrer', 'kanban', true, 'add_kanban');
	printf("</form>\n");
	printf("</div></div>");
}


function modifyKanban() {
	printf("<div id='modify_kanban_form' class='modal'>");
	printf("<div class='modal_content'>");
	printf("<form method='post' id='modify_kanban' action='user.php?action=modify_kanban'>\n");
	printf("<fieldset>\n<legend>Modification d'une tâche</legend>\n");
	printf("<input type='hidden' name='uid' id='uid' value='0' />\n");
	printf("<table>\n<tr><td colspan='2'>\n");
	printf("<input type='text' size='40' maxlength='40' name='unom' id='unom' placeholder='Nom de la tâche' required>\n");
	printf("</td></tr>\n<tr><td colspan='2'>\n");
	printf("<textarea name='udescription' id='udescription' cols='60' rows='3' placeholder='Description' required></textarea>\n");
	printf("</td></tr>\n<tr>\n");
	printf("<td>Date de fin:&nbsp;<input type='date' name='udatefin' id='udatefin' min='%s' required></td>\n", date('Y-m-d', time()));
	printf("<td>Priorité:&nbsp;<select name='upriority' id='upriority' required>\n");
	printf("<option selected='selected' value=''>&nbsp;</option>\n");
	for ($i=1; $i<=5; $i++) {
		printf("<option value='%d'>%s</option>\n", $i, $i);
	}
	printf("</select>\n</td>");
	printf("</tr>\n</table>\n</fieldset>\n");
	validForms('Enregistrer', 'kanban', true, 'modify_kanban');
	printf("</form>\n");
	printf("</div></div>");
}


function displayKanban() {
	$base = dbConnect();
	$request = sprintf("SELECT * FROM progress");
	$result = mysqli_query($base, $request);
	printf("<div class='kanban'>\n");
	while($row = mysqli_fetch_object($result)) {
		printf("<div id='%s' class='dropper %s'>\n", traiteStringFromBDD($row->nom), $row->drop_color);
		if (intval($row->id) == 1) {
			printf("<div class='kanban_title %s'>%s", $row->drag_color, traiteStringFromBDD($row->affichage));
			printf("<a id='addkanban' class='add_kanban'>+</a></div>");
		} else if (intval($row->id) == 3) {
			$req_kanban = sprintf("SELECT * FROM kanban WHERE progress='%d' AND user='%d'", $row->id, $_SESSION['uid']);
			$res_kanban = mysqli_query($base, $req_kanban);
			printf("<div class='kanban_title %s'>%s %d/3</div>", $row->drag_color, traiteStringFromBDD($row->affichage), mysqli_num_rows($res_kanban));
		} else {
			printf("<div class='kanban_title %s'>%s</div>", $row->drag_color, traiteStringFromBDD($row->affichage));
		}
		$req_kanban = sprintf("SELECT * FROM kanban WHERE progress='%d' AND user='%d' ORDER BY priority ASC, datefin ASC", $row->id, $_SESSION['uid']);
		$res_kanban = mysqli_query($base, $req_kanban);
		while($row_kanban = mysqli_fetch_object($res_kanban)) {
			displayKanbanTask($row_kanban->id, "user.php");
		}
		printf("</div>");
	}
	printf("</div>\n");
	dbDisconnect($base);
	addKanban();
	modifyKanban();
	delKanban();
	printf("<script nonce='%s'>window.schedioCsrfUpdateKanban = '%s';</script>\n", $_SESSION['nonce'], getCsrfToken("update_kanban"));
	printf("<script nonce='%s' src='js/dragdrop.js'></script>\n", $_SESSION['nonce']);
	printf("<script nonce='%s'>document.getElementById('addkanban').addEventListener('click', function() {displayAddModal();});</script>\n", $_SESSION['nonce']);
}


function recordKanban($action) {
	genSyslog(__FUNCTION__);

	switch ($action) {
		case 'add':
			$csrfAction = 'add_kanban';
			break;
		case 'update':
			$csrfAction = 'update_kanban';
			break;
		case 'modify':
			$csrfAction = 'modify_kanban';
			break;
		case 'delete':
			$csrfAction = 'del_kanban';
			break;
		default:
			return false;
	}

	if (!isCsrfValid($csrfAction)) {
		return false;
	}

	$isValidDate = function ($date) {
		$dateObj = DateTime::createFromFormat('Y-m-d', $date);
		return $dateObj && $dateObj->format('Y-m-d') === $date;
	};

	$base = dbConnect();

	switch ($action) {
		case 'add':
			$user = intval($_SESSION['uid']);
			$progress = 1;
			$nom = isset($_POST['nom']) ? traiteStringToBDD($_POST['nom']) : NULL;
			$description = isset($_POST['description']) ? traiteStringToBDD($_POST['description']) : NULL;
			$datedebut = date('Y-m-d', time());
			$datefin = isset($_POST['datefin']) ? trim($_POST['datefin']) : '';
			$priority = isset($_POST['priority']) ? intval($_POST['priority']) : 0;
			if ( $user <= 0 || empty($nom) || empty($description) || !$isValidDate($datefin) || $datefin < $datedebut || $priority < 1 || $priority > 5 ) {
				dbDisconnect($base);
				return false;
			}
			$success = dbExecutePrepared($base, "INSERT INTO kanban (user, progress, nom, description, datedebut, datefin, priority) VALUES (?, ?, ?, ?, ?, ?, ?)", "iissssi", $user, $progress, $nom, $description, $datedebut, $datefin, $priority);
			break;

		case 'update':
			$id = isset($_POST['task_id']) ? intval($_POST['task_id']) : 0;
			$progressName = isset($_POST['progress']) ? traiteStringToBDD($_POST['progress']) : NULL;
			if ( $id <= 0 || empty($progressName) || !canEditKanban($id) ) {
				dbDisconnect($base);
				return false;
			}
			$record = dbFetchObjectPrepared($base, "SELECT id FROM progress WHERE nom=? LIMIT 1", "s", $progressName);
			if (!$record) {
				dbDisconnect($base);
				return false;
			}
			$progress = intval($record->id);
			$success = dbExecutePrepared($base, "UPDATE kanban SET progress=? WHERE id=?", "ii", $progress, $id);
			break;

		case 'modify':
			$id = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
			$nom = isset($_POST['unom']) ? traiteStringToBDD($_POST['unom']) : NULL;
			$description = isset($_POST['udescription']) ? traiteStringToBDD($_POST['udescription']) : NULL;
			$datefin = isset($_POST['udatefin']) ? trim($_POST['udatefin']) : '';
			$priority = isset($_POST['upriority']) ? intval($_POST['upriority']) : 0;
			if ( $id <= 0 || empty($nom) || empty($description) || !$isValidDate($datefin) || $priority < 1 || $priority > 5 || !canEditKanban($id) ) {
				dbDisconnect($base);
				return false;
			}
			$success = dbExecutePrepared($base, "UPDATE kanban SET nom=?, description=?, datefin=?, priority=? WHERE id=?", "sssii", $nom, $description, $datefin, $priority, $id);
			break;

		case 'delete':
			$id = isset($_POST['delid']) ? intval($_POST['delid']) : 0;
			if ($id <= 0 || !canEditKanban($id) ) {
				dbDisconnect($base);
				return false;
			}
			$success = dbExecutePrepared($base, "DELETE FROM kanban WHERE id=?", "i", $id);
			break;
		default:
			dbDisconnect($base);
			return false;

	}
	dbDisconnect($base);
	return $success;
}


function displayGantts() {
	printf("<script nonce='%s'>document.body.addEventListener('load', loadGantt());</script>", $_SESSION['nonce']);
	printf("<div class='project'>\n");
	printf("<div id='ganttGraph'></div>\n");
	printf("</div>\n");
}




?>
