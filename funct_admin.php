<?php
/*=========================================================
// File:        funct_admin.php
// Description: admin functions of Schedio
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

function maintenanceBDD() {
	$base = dbConnect();
	$request = "select table_name from information_schema.tables
where table_schema='schedio' ";
	$result = mysqli_query($base, $request);
	$tableNames = '';
	while ($row = mysqli_fetch_object($result)) {
		$tableNames = $tableNames.$row->table_name.', ';
	}
	$tableNames = rtrim($tableNames, ', ');
	$actions = ['CHECK', 'OPTIMIZE', 'REPAIR', 'ANALYZE'];
	printf("<div class='project'>\n");
	foreach ($actions as $value) {
		$request = sprintf("%s TABLE %s", $value, $tableNames);
		if ($result = mysqli_query($base, $request)) {
			printf("<table>\n");
			printf("<tr><th>Nom de la table</th><th>Opération</th><th>Type de message</th><th>Message</th></tr>\n");
			while ($row = mysqli_fetch_object($result)) {
				printf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>\n", $row->Table, $row->Op, $row->Msg_type, $row->Msg_text);
			}
			printf("</table>\n");
		} else {
			printf("%s: %s\n", mysqli_errno($base), mysqli_error($base));
		}
	}
	printf("</div>\n");
	dbDisconnect($base);
}


function createUser() {
	$base = dbConnect();
	$req_role = "SELECT id,intitule FROM role WHERE id<>'1'";
	$res_role = mysqli_query($base, $req_role);
	printf("<form method='post' id='new_user' action='admin.php?action=record_user' onsubmit='return champs_ok(this)'>\n");
	printf("<fieldset>\n<legend>Ajout d'un utilisateur</legend>\n");
	printf("<table>\n<tr><td colspan='3'>\n");
	printf("<input type='text' size='20' maxlength='20' name='prenom' id='prenom' placeholder='Prénom de l&apos;utilisateur' />\n");
	printf("<input type='text' size='20' maxlength='20' name='nom' id='nom' placeholder='Nom de l&apos;utilisateur' />\n");
	printf("Fonction:&nbsp;<select name='role' id='role' onchange='cleanEtbs()'>\n");
	printf("<option selected='selected' value=''>&nbsp;</option>\n");
	while($row = mysqli_fetch_object($res_role)) {
		printf("<option value='%d'>%s</option>\n", $row->id, $row->intitule);
	}
	printf("</select>\n");
	printf("</td></tr>\n<tr><td colspan='3'>\n");
	printf("<input type='text' size='50' maxlength='50' name='login' id='login' placeholder='Identifiant (prenom.nom)' />\n");
	printf("<input type='password' size='20' maxlength='20' name='passwd' id='passwd' placeholder='Mot de passe' />\n");
	printf("</td></tr>\n</table>\n</fieldset>\n");
	validForms('Enregistrer', 'admin.php');
	printf("</form>\n");
	dbDisconnect($base);
}


function selectUserModif() {
	$base = dbConnect();
	$request = "SELECT * FROM users WHERE role<>'1'";
	$result = mysqli_query($base, $request);
	printf("<form method='post' id='modif_user' action='admin.php?action=modif_user' onsubmit='return champs_ok(this)'>\n");
	printf("<fieldset>\n<legend>Modification d'un utilisateur</legend>\n");
	printf("<table>\n<tr><td>\n");
	printf("Utilisateur:&nbsp;\n<select name='user' id='user'>\n");
	printf("<option selected='selected' value=''>&nbsp;</option>\n");
	while($row=mysqli_fetch_object($result)) {
		printf("<option value='%s'>%s %s</option>\n", $row->id, $row->prenom, $row->nom);
	}
	printf("</select>\n");
	printf("</td>\n</tr>\n</table>\n</fieldset>\n");
	validForms('Modifier', 'admin.php', $back=False);
	printf("</form>\n");
}


function modifUser($id) {
	$id = intval($id);
	$base = dbConnect();
	$request = sprintf("SELECT * FROM users WHERE id='%d' LIMIT 1", $id);
	$result = mysqli_query($base, $request);
	$record = mysqli_fetch_object($result);
	$req_role = "SELECT id,intitule FROM role WHERE id<>'1'";
	$res_role = mysqli_query($base, $req_role);

	printf("<form method='post' id='modif_user' action='admin.php?action=update_user' onsubmit='return user_champs_ok(this)'>\n");
	printf("<fieldset>\n<legend>Modification d'un utilisateur</legend>\n");
	printf("<table>\n<tr><td colspan='3'>\n");
	printf("<input type='hidden' size='3' maxlength='3' name='id_user' id='id_user' value='%s'/>\n", $id);
	printf("Prénom:&nbsp;<input type='text' size='20' maxlength='20' name='prenom' id='prenom' value=\"%s\" />\n", traiteStringFromBDD($record->prenom));
	printf("Nom:&nbsp;<input type='text' size='20' maxlength='20' name='nom' id='nom' value=\"%s\" />\n", traiteStringFromBDD($record->nom));
	printf("Fonction:&nbsp;<select name='role' id='role' onchange='cleanEtbs()'>\n");
	printf("<option selected='selected' value='%d'>%s</option>\n", intval($record->role), getRole(intval($record->role)));
	while($row=mysqli_fetch_object($res_role)) {
		printf("<option value='%d'>%s</option>\n", $row->id, $row->intitule);
	}
	printf("</select>\n");
	printf("</td></tr>\n<tr><td colspan='3'>\n");
	printf("Identifiant&nbsp;<input type='text' size='50' maxlength='50' name='login' id='login' value=\"%s\" />\n", traiteStringFromBDD($record->login));
	printf("</td></tr>\n</table>\n</fieldset>\n");
	validForms('Modifier', 'admin.php', $back=False);
	printf("</form>\n");
	dbDisconnect($base);
}


function recordUser($action) {
	$base = dbConnect();
	if ($action === 'update') {
		$id = isset($_POST['id_user']) ? intval(trim($_POST['id_user'])) : NULL;
	}
	$prenom = isset($_POST['prenom']) ? traiteStringToBDD($_POST['prenom']) : NULL;
	$nom = isset($_POST['nom']) ? traiteStringToBDD($_POST['nom']) : NULL;
	$role = isset($_POST['role']) ? intval(trim($_POST['role'])) : NULL;
	$login = isset($_POST['login']) ? traiteStringToBDD($_POST['login']) : NULL;
	switch ($action) {
		case 'add':
			$passwd = isset($_POST['passwd']) ?  traiteStringToBDD($_POST['passwd']) : NULL;
			$passwd = password_hash($passwd, PASSWORD_BCRYPT);
			$request = sprintf("INSERT INTO users (prenom, nom, role, login, password) VALUES ('%s', '%s', '%d', '%s', '%s')", $prenom, $nom, $role, $login, $passwd);
			break;
		case 'update':
			$request = sprintf("UPDATE users SET prenom='%s', nom='%s', role='%d', login='%s' WHERE id='%d'", $prenom, $nom, $role, $login, $id);
			break;
	}
	if (mysqli_query($base, $request)) {
		switch ($action) {
			case 'add':
				return mysqli_insert_id($base);
				break;
			case 'update':
				return $id;
				break;
		}
	} else {
		return false;
	}
	dbDisconnect($base);
}



?>
