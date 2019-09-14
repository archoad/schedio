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
	printf("<form method='post' id='new_project' action='user.php?action=record_project' onsubmit='return champs_ok(this)'>\n");
	printf("<fieldset>\n<legend>Ajout d'un projet</legend>\n");
	printf("<table>\n<tr>\n");
	printf("<td colspan='3'><input type='text' size='60' maxlength='60' name='nom' id='nom' placeholder='Nom' />\n</td>");
	printf("</tr>\n<tr>\n");
	printf("<td colspan='3'><textarea name='description' id='description' cols='60' rows='3' placeholder='Description'></textarea></td>\n");
	printf("</tr>\n<tr>\n");
	printf("<td colspan='3'>Chapitre ISO27002:&nbsp;<select name='chapter' id='chapter'>\n");
	printf("<option selected='selected' value=''>&nbsp;</option>\n");
	while($row = mysqli_fetch_object($res_chapter)) {
		printf("<option value='%d'>%s - %s</option>\n", $row->id, $row->num, $row->nom);
	}
	printf("</select>\n</td>");
	printf("</tr>\n<tr>\n");
	printf("<td>Chef de projet:&nbsp;<select name='chef' id='chef'>\n");
	printf("<option selected='selected' value=''>&nbsp;</option>\n");
	while($row = mysqli_fetch_object($res_chefproj)) {
		printf("<option value='%d'>%s %s</option>\n", $row->id, $row->prenom, $row->nom);
	}
	printf("</select>\n</td>\n");
	printf("<td>Date de début:&nbsp;<input type='date' name='datedebut' id='datedebut' min='%s' /></td>\n", date('Y-m-d', time()));
	printf("<td>Date de fin:&nbsp;<input type='date' name='datefin' id='datefin' min='%s' /></td>\n", date('Y-m-d', time()));
	printf("</tr>\n</table>\n</fieldset>\n");
	validForms('Enregistrer', 'user.php');
	printf("</form>\n");
	dbDisconnect($base);
}


function selectProjectModif() {
	$base = dbConnect();
	$request = sprintf("SELECT * FROM project WHERE directeur='%s'", $_SESSION['uid']);
	$result = mysqli_query($base, $request);
	printf("<form method='post' id='modif_project' action='user.php?action=modif_project' onsubmit='return champs_ok(this)'>\n");
	printf("<fieldset>\n<legend>Modification d'un projet</legend>\n");
	printf("<table>\n<tr><td>\n");
	printf("Projet:&nbsp;\n<select name='project' id='project'>\n");
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


function modifProject($id) {
	$id = intval($id);
	$base = dbConnect();
	$request = sprintf("SELECT * FROM project WHERE id='%d' LIMIT 1", $id);
	$result = mysqli_query($base, $request);
	$record = mysqli_fetch_object($result);
	$res_chefproj = mysqli_query($base, "SELECT * FROM users WHERE role='2' OR role='3'");
	$res_chapter = mysqli_query($base, "SELECT * FROM chapter");

	printf("<form method='post' id='modif_project' action='user.php?action=update_project' onsubmit='return champs_ok(this)'>\n");
	printf("<fieldset>\n<legend>Modification d'un projet</legend>\n");
	printf("<input type='hidden' size='3' maxlength='3' name='id_project' id='id_project' value='%s'/>\n", $id);
	printf("<table>\n<tr>\n");
	printf("<td colspan='3'><input type='text' size='60' maxlength='60' name='nom' id='nom' value=\"%s\" />\n</td>", traiteStringFromBDD($record->nom));
	printf("</tr>\n<tr>\n");
	printf("<td colspan='3'><textarea name='description' id='description' cols='60' rows='3'>%s</textarea></td>\n", traiteStringFromBDD($record->description));
	printf("</tr>\n<tr>\n");
	printf("<td colspan='3'>Chapitre ISO27002:&nbsp;<select name='chapter' id='chapter'>\n");
	printf("<option selected='selected' value='%d'>%s</option>\n", intval($record->chapter), getChapter($record->chapter));
	while($row = mysqli_fetch_object($res_chapter)) {
		printf("<option value='%d'>%s - %s</option>\n", $row->id, $row->num, $row->nom);
	}
	printf("</select>\n</td>");
	printf("</tr>\n<tr>\n");
	printf("<td>Chef de projet:&nbsp;<select name='chef' id='chef'>\n");
	printf("<option selected='selected' value='%s'>%s</option>\n", intval($record->chef), getUser($record->chef));
	while($row = mysqli_fetch_object($res_chefproj)) {
		printf("<option value='%d'>%s %s</option>\n", $row->id, $row->prenom, $row->nom);
	}
	printf("</select>\n</td>\n");
	printf("<td>Date de début:&nbsp;<input type='date' name='datedebut' id='datedebut' min='%s' value='%s' /></td>\n", $record->datedebut, $record->datedebut);
	printf("<td>Date de fin:&nbsp;<input type='date' name='datefin' id='datefin' min='%s' value='%s' /></td>\n", $record->datedebut, $record->datefin);
	printf("</tr>\n</table>\n</fieldset>\n");
	validForms('Modifier', 'user.php', $back=False);
	printf("</form>\n");
	dbDisconnect($base);
}


function recorProject($action) {
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
			$id = isset($_POST['id_project']) ? intval(trim($_POST['id_project'])) : NULL;
			$request = sprintf("UPDATE project SET nom='%s', description='%s', chapter='%d', directeur='%d', chef='%d', datedebut='%s', datefin='%s' WHERE id='%d'", $nom, $description, $chapter, $directeur, $chef, $datedebut, $datefin, $id);
			break;
		case 'close':
			$request = sprintf("UPDATE project SET complete='1' WHERE id='%d'", $_GET['value']);
			break;
	}
	if (mysqli_query($base, $request)) {
		switch ($action) {
			case 'add':
				return mysqli_insert_id($base);
				break;
			case 'update':
				return true;
				break;
			case 'close':
				return true;
				break;
		}
	} else {
		return false;
	}
	dbDisconnect($base);
}


function displayProjects() {
	$base = dbConnect();
	if (intval($_SESSION['role']) == 2) {
		$request = sprintf("SELECT * FROM project WHERE directeur='%d' ORDER BY chapter", intval($_SESSION['uid']));
	} else {
		$request = sprintf("SELECT * FROM project WHERE chef='%d' ORDER BY chapter", intval($_SESSION['uid']));
	}
	$result = mysqli_query($base, $request);
	dbDisconnect($base);
	$chapterRef = 0;
	printf("<div class='project'>\n");
	while($row = mysqli_fetch_object($result)) {
		if (intval($row->chapter) <> $chapterRef) {
			if ($chapterRef<>0) { printf("</table>\n"); }
			printf("<h3>%s</h3>", getChapter($row->chapter));
			printf("<table>\n<tr><th>Nom</th><th>Directeur de projet</th><th>Chef de projet</th><th>Date de début</th><th>date de fin</th><th>Avancement</th>");
			if (intval($_SESSION['role']) == 2) {
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
		printf("<td>%s</td>", projectProgressBar($row->id));
		if (intval($_SESSION['role']) == 2) {
			if (computeProjectProgress($row->id) == 100) {
				if (intval($row->complete)) {
					printf("<td><span class='finished'>Clos</span></td>");
				} else {
					printf("<td><a class='complete' href='user.php?action=complete&value=%d'>Clore</a></td>", $row->id);
				}
			} else {
				printf("<td><span class='live'>Actif</span></td>");
			}
		}
		printf("</tr>\n");
	}
	printf("</table>\n</div>");
}


function displayHead() {
	$base = dbConnect();
	$request = sprintf("SELECT * FROM project WHERE id='%d' LIMIT 1", intval($_SESSION['project']));
	$result = mysqli_query($base, $request);
	$record = mysqli_fetch_object($result);
	dbDisconnect($base);
	printf("<div class='project'>\n<table>\n<tr>\n");
	printf("<th colspan='2' width='50%%'><h2>%s</h2>\n",  traiteStringFromBDD($record->nom));
	printf("<th colspan='2' width='50%%'><p>%s</p></td>\n", traiteStringFromBDD($record->description));
	printf("</tr>\n<tr>\n");
	printf("<th colspan='4'>");
	if (intval($record->complete)) {
		printf("Projet clos");
	} else {
		printf("<div style='display: table; margin: auto;'><div style='display: table-cell;'>Avancement</div><div style='display: table-cell;'>%s</div></div>", projectProgressBar($_SESSION['project']));
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
	$base = dbConnect();
	$request = sprintf("SELECT * FROM project WHERE id='%d' LIMIT 1", intval($_SESSION['project']));
	$result = mysqli_query($base, $request);
	$record = mysqli_fetch_object($result);
	dbDisconnect($base);
	$today = date('Y-m-d', time());
	$interval = date_diff(date_create($record->datefin), date_create($today));
	if ($interval->invert) {
		printf("<div class='task'>\n");
		printf("<form method='post' id='new_task' action='user.php?action=record_task' onsubmit='return champs_ok(this)'>\n");
		printf("<table>\n<tr>\n");
		printf("<td><input type='text' size='40' maxlength='60' name='nom' id='nom' placeholder='Tâche' /></td>\n");
		printf("<td>De&nbsp;<input type='date' name='datedebut' id='datedebut' min='%s' max='%s' /></td>\n", $record->datedebut, $record->datefin);
		printf("<td>A&nbsp;<input type='date' name='datefin' id='datefin' min='%s' max='%s' /></td>\n", $record->datedebut, $record->datefin);
		printf("<td><input type='submit' value='+'></td>\n");
		printf("</tr>\n</table>\n");
		printf("</form>\n</div>\n");
	} else {
		printf("<div class='project'>\n");
		printf("<table><tr><td style='text-align:center;'>Date de fin de projet dépassée. Impossible de rajouter une tâche.</td></tr></table>\n");
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
		} else {
			printf("<td style='text-align:center;'><a class='project_plus' href='user.php?action=update_action&value=%d'>+</a></td>\n", $row->id);
		}
		printf("<td><div style='display: table; margin: auto;'>");
		if (!$closed) {
			printf("<div style='display: table-cell;'><a class='project_minus' href='user.php?action=task_decrease&value=%d'>-</a></div>", $row->id);
		}
		printf("<div style='display: table-cell;'>%s</div>", taskProgressBar($row->id));
		if (!$closed) {
			printf("<div style='display: table-cell;'><a class='project_plus' href='user.php?action=task_increase&value=%d'>+</a></div>", $row->id);
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
	if ($result->num_rows) {
		displayTasks($result);
		if (!$closed) {
			addTask();
		}
	} else {
		addTask();
	}
}


function projectDetail() {
	displayHead();
	tasksManagement();
}


function recorTask($action) {
	$base = dbConnect();
	$id = intval($_GET['value']);
	$request = sprintf("SELECT avancement FROM task WHERE id='%d' LIMIT 1", $id);
	$result = mysqli_query($base,$request);
	$record = mysqli_fetch_object($result);
	$progress = intval($record->avancement);
	$projet = intval($_SESSION['project']);
	$nom = isset($_POST['nom']) ? traiteStringToBDD($_POST['nom']) : NULL;
	$datedebut = isset($_POST['datedebut']) ? $_POST['datedebut'] : NULL;
	$datefin = isset($_POST['datefin']) ? $_POST['datefin'] : NULL;
	switch ($action) {
		case 'add':
			$request = sprintf("INSERT INTO task (projet, nom, datedebut, datefin, avancement) VALUES ('%d', '%s', '%s', '%s', '0')", $projet, $nom, $datedebut, $datefin);
			break;
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
		switch ($action) {
			case 'add':
				return mysqli_insert_id($base);
				break;
			case 'increase':
				return $id;
				break;
			case 'decrease':
				return $id;
				break;
		}
	} else {
		return false;
	}
	dbDisconnect($base);
}




?>
