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
		printf("<option value='%d'>%s - %s</option>\n", $row->id, $row->num, $row->nom);
	}
	printf("</select>\n</td>");
	printf("</tr>\n<tr>\n");
	printf("<td>Chef de projet:&nbsp;<select name='chef' id='chef' required>\n");
	printf("<option selected='selected' value=''>&nbsp;</option>\n");
	while($row = mysqli_fetch_object($res_chefproj)) {
		printf("<option value='%d'>%s %s</option>\n", $row->id, $row->prenom, $row->nom);
	}
	printf("</select>\n</td>\n");
	printf("<td>Date de début:&nbsp;<input type='date' name='datedebut' id='datedebut' min='%s' required></td>\n", date('Y-m-d', time()));
	printf("<td>Date de fin:&nbsp;<input type='date' name='datefin' id='datefin' min='%s' required></td>\n", date('Y-m-d', time()));
	printf("</tr>\n</table>\n</fieldset>\n");
	validForms('Enregistrer', 'user.php');
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
			printf("<option value='%s'>%s</option>\n", $row->id, traiteStringFromBDD($row->nom));
		}
	}
	printf("</select>\n");
	printf("</td>\n</tr>\n</table>\n</fieldset>\n");
	validForms('Modifier', 'user.php', $back=False);
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
	printf("<td colspan='3'><textarea name='description' id='description' cols='60' rows='3' required>%s</textarea></td>\n", traiteStringFromBDD($record->description));
	printf("</tr>\n<tr>\n");
	printf("<td colspan='3'>Chapitre ISO27002:&nbsp;<select name='chapter' id='chapter' required>\n");
	printf("<option selected='selected' value='%d'>%s</option>\n", intval($record->chapter), getChapter($record->chapter));
	while($row = mysqli_fetch_object($res_chapter)) {
		printf("<option value='%d'>%s - %s</option>\n", $row->id, $row->num, $row->nom);
	}
	printf("</select>\n</td>");
	printf("</tr>\n<tr>\n");
	printf("<td>Chef de projet:&nbsp;<select name='chef' id='chef' required>\n");
	printf("<option selected='selected' value='%s'>%s</option>\n", intval($record->chef), getUser($record->chef));
	while($row = mysqli_fetch_object($res_chefproj)) {
		printf("<option value='%d'>%s %s</option>\n", $row->id, $row->prenom, $row->nom);
	}
	printf("</select>\n</td>\n");
	printf("<td>Date de début:&nbsp;<input type='date' name='datedebut' id='datedebut' min='%s' value='%s' required></td>\n", $record->datedebut, $record->datedebut);
	printf("<td>Date de fin:&nbsp;<input type='date' name='datefin' id='datefin' min='%s' value='%s' required></td>\n", $record->datedebut, $record->datefin);
	printf("</tr>\n</table>\n</fieldset>\n");
	validForms('Modifier', 'user.php', $back=False);
	printf("</form>\n");
	dbDisconnect($base);
	printf("<script nonce='%s'>document.getElementById('datedebut').addEventListener('change', function() {fixMinDate();});</script>\n", $_SESSION['nonce']);
}


function recordProject($action) {
	genSyslog(__FUNCTION__);
	$base = dbConnect();
	$nom = isset($_POST['nom']) ? traiteStringToBDD($_POST['nom']) : NULL;
	$description = isset($_POST['description']) ? traiteStringToBDD($_POST['description']) : NULL;
	$chapter = isset($_POST['chapter']) ? intval(trim($_POST['chapter'])) : NULL;
	$directeur = intval($_SESSION['uid']);
	$chef = isset($_POST['chef']) ? intval(trim($_POST['chef'])) : NULL;
	$datedebut = isset($_POST['datedebut']) ? $_POST['datedebut'] : NULL;
	$datefin = isset($_POST['datefin']) ? $_POST['datefin'] : NULL;
	switch ($action) {
		case 'add':
			$request = sprintf("INSERT INTO project (nom, description, chapter, directeur, chef, datedebut, datefin) VALUES ('%s', '%s', '%d', '%d', '%d', '%s', '%s')", $nom, $description, $chapter, $directeur, $chef, $datedebut, $datefin);
			break;
		case 'update':
			$id = intval($_SESSION['current_project']);
			$request = sprintf("UPDATE project SET nom='%s', description='%s', chapter='%d', directeur='%d', chef='%d', datedebut='%s', datefin='%s' WHERE id='%d'", $nom, $description, $chapter, $directeur, $chef, $datedebut, $datefin, $id);
			break;
		case 'close':
			$request = sprintf("UPDATE project SET complete='1' WHERE id='%d'", $_GET['value']);
			break;
		default:
			return false;
	}
	if (isset($_SESSION['token'])) {
		unset($_SESSION['token']);
		if (mysqli_query($base, $request)) {
			switch ($action) {
				case 'add':
				case 'close':
					dbDisconnect($base);
					return true;
					break;
				case 'update':
					unset($_SESSION['current_project']);
					dbDisconnect($base);
					return true;
					break;
			}
		} else {
			dbDisconnect($base);
			return false;
		}
	} else {
		return false;
	}
}


function displayProjects() {
	$base = dbConnect();
	switch (intval($_SESSION['role'])) {
		case 2: // Directeur de projet
			$request = sprintf("SELECT * FROM project WHERE directeur='%d' ORDER BY chapter ASC, complete ASC, datedebut ASC", intval($_SESSION['uid']));
			break;
		case 4: // Manager
			$request = sprintf("SELECT * FROM project ORDER BY chapter ASC, complete ASC, datedebut ASC");
			break;
		default: // Chef de projet
			$request = sprintf("SELECT * FROM project WHERE chef='%d' ORDER BY chapter ASC, complete ASC, datedebut ASC", intval($_SESSION['uid']));
			break;
	}
	$result = mysqli_query($base, $request);
	dbDisconnect($base);
	$chapterRef = 0;
	printf("<div class='project'>\n");
	while($row = mysqli_fetch_object($result)) {
		$closed = isProjectClosed($row->id);
		if (!$closed) {
			if (intval($row->chapter) <> $chapterRef) {
				if ($chapterRef<>0) { printf("</table>\n"); }
				printf("<h3>%s</h3>", getChapter($row->chapter));
				printf("<table>\n<tr><th>Nom</th><th>Directeur de projet</th><th>Chef de projet</th><th>Date de début</th><th>date de fin</th><th>Avancement</th>");
				if (in_array($_SESSION['role'], array('2', '4'))) {
					printf("<th>Etat</th>");
				}
				printf("</tr>");
				$chapterRef = intval($row->chapter);
			}
			printf("<tr>\n");
			printf("<td><a href='user.php?action=mgmt&value=%d'>%s</a></td>", $row->id, traiteStringFromBDD($row->nom));
			printf("<td>%s</td>", getUser($row->directeur));
			printf("<td>%s</td>", getUser($row->chef));
			printf("<td>%s</td>", displayShortDate($row->datedebut));
			printf("<td>%s</td>", displayShortDate($row->datefin));
			if ((intval($_SESSION['role']) == 3) and (intval($row->complete))) {
				printf("<td><div class='finished'>Projet clos</div></td>");
			} else {
				printf("<td>%s</td>", projectProgressBar($row->id));
			}
			if (in_array($_SESSION['role'], array('2', '4'))) {
				if (computeProjectProgress($row->id) == 100) {
					if (intval($row->complete)) {
						printf("<td><span class='finished'>Clos</span></td>");
					} else {
						if (intval($_SESSION['role']) == 2) {
							printf("<td><a class='complete' href='user.php?action=complete&value=%d'>Clore</a></td>", $row->id);
						}
						if (intval($_SESSION['role']) == 4) {
							printf("<td><span class='complete'>A clore</span></td>", $row->id);
						}
					}
				} else {
					printf("<td><span class='live'>Actif</span></td>");
				}
			}
			printf("</tr>\n");
		}
	}
	printf("</table>\n</div>");
}


function displayProjectHead() {
	$base = dbConnect();
	$request = sprintf("SELECT * FROM project WHERE id='%d' LIMIT 1", intval($_SESSION['project']));
	$result = mysqli_query($base, $request);
	$record = mysqli_fetch_object($result);
	dbDisconnect($base);
	printf("<div class='project'>\n<table>\n<tr>\n");
	printf("<th colspan='2' class='projet_title'>%s</th>\n",  traiteStringFromBDD($record->nom));
	printf("<th colspan='2' class='projet_detail'>%s</th>\n", traiteStringFromBDD($record->description));
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
		printf("<th>Temps restant</th><td>%s</td>\n", computeDuration(strftime("%Y-%m-%d", time()), $record->datefin));
	}
	printf("</tr></table></div>\n");
}


function addTask() {
	$_SESSION['token'] = generateToken();
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
			printf("<td class='center'><a class='action_plus' href='user.php?action=read_actions&value=%d'>&rarrb;</a></td>\n", $row->id);
		} else {
			printf("<td class='center'><a class='action_plus' href='user.php?action=actions&value=%d'>+</a></td>\n", $row->id);
		}
		printf("<td><div class='tableauto'>");
		if ((!$closed) and (intval($_SESSION['role']) != 4)) {
			printf("<div class='tablecell'><a class='project_minus' href='user.php?action=task_decrease&value=%d'>-</a></div>", $row->id);
		}
		printf("<div class='tablecell'>%s</div>", taskProgressBar($row->id));
		if ((!$closed) and (intval($_SESSION['role']) != 4)) {
			printf("<div class='tablecell'><a class='project_plus' href='user.php?action=task_increase&value=%d'>+</a></div>", $row->id);
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
	$projet = intval($_SESSION['project']);
	$nom = isset($_POST['nom']) ? traiteStringToBDD($_POST['nom']) : NULL;
	$datedebut = isset($_POST['datedebut']) ? $_POST['datedebut'] : NULL;
	$datefin = isset($_POST['datefin']) ? $_POST['datefin'] : NULL;
	$request = sprintf("INSERT INTO task (projet, nom, datedebut, datefin, avancement) VALUES ('%d', '%s', '%s', '%s', '0')", $projet, $nom, $datedebut, $datefin);
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
		return false;
	}
}


function incrDecrTask($action) {
	$base = dbConnect();
	$id = intval($_GET['value']);
	$request = sprintf("SELECT avancement FROM task WHERE id='%d' LIMIT 1", $id);
	$result = mysqli_query($base,$request);
	$record = mysqli_fetch_object($result);
	$progress = intval($record->avancement);
	switch ($action) {
		case 'increase':
			if ($progress < 100) { $progress += 10; }
			$request = sprintf("UPDATE task SET avancement='%d' WHERE id='%d' ", $progress, $id);
			break;
		case 'decrease':
			if ($progress > 0) { $progress -= 10; }
			$request = sprintf("UPDATE task SET avancement='%d' WHERE id='%d' ", $progress, $id);
			break;
	}
	if (mysqli_query($base, $request)) {
		dbDisconnect($base);
		return true;
	} else {
		dbDisconnect($base);
		return false;
	}
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
		printf("<table>\n<tr>\n");
		if (filesize($fileName)) {
			$data = fread($handle, filesize($fileName));
			printf("<td><div class='actions'><textarea name='description' id='description'>%s</textarea></div></td>\n", $data);
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
			printf("<textarea name='description' id='description' cols='80' rows='30' readonly>%s</textarea>\n", $data);
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

	$data = sprintf("%d:%s:%s:%s:%d", $record->id, traiteStringFromBDD($record->nom), traiteStringFromBDD($record->description), $record->datefin, $record->priority);
	$data = base64_encode($data);
	printf("<div id='task%d' class='draggable'>", $record->id);
	printf("<div class='draggable-name'>%s - P%d<div id='deltask%d' class='del_kanban'>&ndash;</div></div>", traiteStringFromBDD($record->nom), intval($record->priority), $id);
	printf("<p class='kanban_description'>%s</p>", traiteStringFromBDD($record->description));
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
	if (isset($_SESSION['token'])) { unset($_SESSION['token']); }
	$_SESSION['token'] = generateToken();
	printf("<form method='post' id='del_kanban' action='user.php?action=del_kanban'>\n");
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
	validForms('Enregistrer', 'kanban');
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
	validForms('Enregistrer', 'kanban');
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
	printf("<script nonce='%s' src='js/dragdrop.js'></script>\n", $_SESSION['nonce']);
	printf("<script nonce='%s'>document.getElementById('addkanban').addEventListener('click', function() {displayAddModal();});</script>\n", $_SESSION['nonce']);
}


function recordKanban($action) {
	$base = dbConnect();
	switch ($action) {
		case 'add':
			$user = intval($_SESSION['uid']);
			$progress = 1;
			$nom = isset($_POST['nom']) ? traiteStringToBDD($_POST['nom']) : NULL;
			$description = isset($_POST['description']) ? traiteStringToBDD($_POST['description']) : NULL;
			$datedebut = date('Y-m-d', time());
			$datefin = isset($_POST['datefin']) ? $_POST['datefin'] : NULL;
			$priority = isset($_POST['priority']) ? intval($_POST['priority']) : NULL;
			$request = sprintf("INSERT INTO kanban (user, progress, nom, description, datedebut, datefin, priority) VALUES ('%d', '%d', '%s', '%s', '%s', '%s', '%d')", $user, $progress, $nom, $description, $datedebut, $datefin, $priority);
			break;
		case 'update':
			$request = sprintf("SELECT id FROM progress WHERE nom='%s' LIMIT 1", $_GET['progress']);
			$result = mysqli_query($base, $request);
			$record = mysqli_fetch_object($result);
			$request = sprintf("UPDATE kanban SET progress='%d' WHERE id='%d'", $record->id, $_GET['task']);
			break;
		case 'modify':
			$id = isset($_POST['uid']) ? intval($_POST['uid']) : NULL;
			$nom = isset($_POST['unom']) ? traiteStringToBDD($_POST['unom']) : NULL;
			$description = isset($_POST['udescription']) ? traiteStringToBDD($_POST['udescription']) : NULL;
			$datefin = isset($_POST['udatefin']) ? $_POST['udatefin'] : NULL;
			$priority = isset($_POST['upriority']) ? intval($_POST['upriority']) : NULL;
			$request = sprintf("UPDATE kanban SET nom='%s', description='%s', datefin='%s', priority='%d' WHERE id='%d'", $nom, $description, $datefin, $priority, $id);
			break;
		case 'delete':
			$request = sprintf("DELETE FROM kanban WHERE id='%d'", $_POST['delid']);
			break;
	}
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
		return false;
	}
}


function displayGantts() {
	printf("<script nonce='%s'>document.body.addEventListener('load', loadGantt());</script>", $_SESSION['nonce']);
	printf("<div class='project'>\n");
	printf("<div id='ganttGraph'></div>\n");
	printf("</div>\n");
}




?>
