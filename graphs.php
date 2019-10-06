<?php
/*=========================================================
// File:        graphs.php
// Description: graphs with chart.js page of Schedio
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
session_start();
$authorizedRole = array('2', '3', '4');
isSessionValid($authorizedRole);
header('Content-Type: application/json');


$colors = array('#4E79A7', '#A0CBE8', '#F28E2B', '#FFBE7D', '#59A14F', '#8CD17D', '#B6992D', '#F1CE63', '#499894', '#86BCB6', '#E15759', '#FF9D9A', '#79706E', '#BAB0AC', '#D37295', '#FABFD2', '#B07AA1', '#D4A6C8', '#9D7660', '#D7B5A6');


function hex2rgb($color, $alpha) {
	$color = substr($color, 1);
	list($r, $g, $b) = array($color[0].$color[1], $color[2].$color[3], $color[4].$color[5]);
	$r = hexdec($r);
	$g = hexdec($g);
	$b = hexdec($b);
	return sprintf("rgba(%d,%d,%d,%s)", $r, $g, $b, $alpha);
}


function formatDateStart($date) {
	return strftime("%Y-%m-%d %H:%M", strtotime($date));
}


function formatDateEnd($date) {
	$tmp = strftime("%Y-%m-%d", strtotime($date));
	$tmp = $tmp.' 23:59';
	return $tmp;
}


function getProjects() {
	$projects = array();
	$base = dbConnect();
	if (intval($_SESSION['role']) == 3) {
		$request = sprintf("SELECT * FROM project WHERE chef='%d' ", intval($_SESSION['uid']));
	} elseif (intval($_SESSION['role']) == 2) {
		$request = sprintf("SELECT * FROM project WHERE directeur='%d' ", intval($_SESSION['uid']));
	} else {
		$request = sprintf("SELECT * FROM project ");
	}
	$result = mysqli_query($base, $request);
	dbDisconnect($base);
	while($row = mysqli_fetch_object($result)) {
		$closed = isProjectClosed($row->id);
		if (!$closed) {
			$projects[] = array(
				'id' => 100 + intval($row->id),
				'content' => sprintf("<b><a href='user.php?action=mgmt&value=%d'>%s</a></b><br />Avancement: %d%%", $row->id, traiteStringFromBDD($row->nom), computeProjectProgress($row->id)),
				'chapter' => intval($row->chapter),
				'treeLevel' => 2,
			);
		}
	}
	return $projects;
}


function getTasks($projects) {
	global $colors;
	$counter = 0;
	$tasks = array();
	foreach ($projects as $p) {
		$base = dbConnect();
		$request = sprintf("SELECT * FROM task WHERE projet='%d' ", intval($p['id'])-100);
		$result = mysqli_query($base, $request);
		dbDisconnect($base);
		while($row = mysqli_fetch_object($result)) {
			$tasks[] = array(
				'id' => 10000 + intval($row->id),
				'content' => traiteStringFromBDD($row->nom),
				'start' => formatDateStart($row->datedebut),
				'end' => formatDateEnd($row->datefin),
				'style' => sprintf("padding:2px; font-size: 8pt; border-color: %s; background-color: %s;", hex2rgb($colors[$counter], '1.0'), hex2rgb($colors[$counter], '0.5')),
				'group' => intval($p['id']),
				'value' => $row->avancement.'%',
				'title' => traiteStringFromBDD($row->nom),
			);
		}
		$counter++;
	}
	return $tasks;
}


function getChapters($projects) {
	$chapters = array();
	$temp = array();
	$base = dbConnect();
	foreach ($projects as $p) {
		$temp[$p['chapter']][] = $p['id'];
	}
	foreach ($temp as $chapID => $projID) {
		$request = sprintf("SELECT * FROM chapter WHERE id='%d' LIMIT 1", $chapID);
		$result = mysqli_query($base, $request);
		$row = mysqli_fetch_object($result);
		$chapters[] = array(
			'id' => intval($row->id),
			'content' => sprintf("<b>%d - %s</b>", $row->num, traiteStringFromBDD($row->shortname)),
			'nestedGroups' => $projID,
			'treeLevel' => 1,
			'showNested' => true,
		);
	}
	dbDisconnect($base);
	return $chapters;
}


$projects = getProjects();
$chapters = getChapters($projects);
$tasks = getTasks($projects);

$groups = array();
foreach ($chapters as $elt) { $groups[] = $elt; }
foreach ($projects as $elt) { $groups[] = $elt; }

$rawdata = array('groups' => $groups, 'tasks' => $tasks);
echo json_encode($rawdata);

?>
