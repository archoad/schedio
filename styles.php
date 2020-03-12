<?php
/*=========================================================
// File:        styles.php
// Description: styles for Schedio
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




header('content-type: text/css; charset: UTF-8');
ob_start('ob_gzhandler');
header('Cache-Control: max-age=31536000, must-revalidate');
session_start();

switch ($_SESSION['theme']) {
	case 'standard':
		$bg0Color = '#eae7dc';
		$bg1Color = '#d8c3a5';
		$bg2Color = '#8e8d8a';
		$myBlueLight = '#74b4e8';
		$myBlueDark = '#51a4e8';
		$myOrangeLight = '#e8b874';
		$myOrangeDark = '#e8a951';
		$myRedLight = '#e88074';
		$myRedDark = '#e86051';
		$myGreenLight = '#74e874';
		$myGreenDark = '#51e851';
		break;
	case 'old':
		$bg0Color = '#e9e4db';
		$bg1Color = '#9eb1bb';
		$bg2Color = '#4d636f';
		$myBlueLight = '#337ab7';
		$myBlueDark = '#286090';
		$myOrangeLight = '#f0ad4e';
		$myOrangeDark = '#ec971f';
		$myRedLight = '#d9534f';
		$myRedDark = '#c9302c';
		$myGreenLight = '#5cb85c';
		$myGreenDark = '#449d44';
		break;
	case 'glp':
		$bg0Color = '#ffffff';
		$bg1Color = '#ffcb05';
		$bg2Color = '#003da5';
		$myBlueLight = '#337ab7';
		$myBlueDark = '#286090';
		$myOrangeLight = '#ffcb05';
		$myOrangeDark = '#ffcb05';
		$myRedLight = '#e56a54';
		$myRedDark = '#e56a54';
		$myGreenLight = '#5cb85c';
		$myGreenDark = '#5cb85c';
		break;
}

?>

@charset "utf-8";

@font-face {
	font-family: Montserrat;
	font-style: normal;
	font-weight: 400;
	font-display: swap;
	src: local('Montserrat-Regular'), url(data/montserrat.woff2) format('woff2');
	unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
}

:root {
	--bg0Color: <?php echo $bg0Color; ?>;
	--bg1Color: <?php echo $bg1Color; ?>;
	--bg2Color: <?php echo $bg2Color; ?>;
	--myBlueLight: <?php echo $myBlueLight; ?>;
	--myBlueDark: <?php echo $myBlueDark; ?>;
	--myOrangeLight: <?php echo $myOrangeLight; ?>;
	--myOrangeDark: <?php echo $myOrangeDark; ?>;
	--myRedLight: <?php echo $myRedLight; ?>;
	--myRedDark: <?php echo $myRedDark; ?>;
	--myGreenLight: <?php echo $myGreenLight; ?>;
	--myGreenDark: <?php echo $myGreenDark; ?>;
	--textDarkColor:#444444;
	--textClearColor:#f5f7f8;
	--shadowNormal: 6px 6px 6px rgba(0, 0, 0, 0.2);
	--shadowHover: 2px 2px 2px rgba(0, 0, 0, 0.2);
	--shadowAuth: 0 0 20px 0 rgba(0, 0, 0, 0.2), 0 5px 5px 0 rgba(0, 0, 0, 0.24);
}

* {
	box-sizing: border-box;
}

html {
	height: 100%;
}

body {
	width: 100%;
	height: 100%;
	margin: 0;
	background-color: var(--bg0Color);
	font-family: Montserrat;
	font-size: 10pt;
}

h1 {
	text-align:center;
	font-size:12pt;
}

h2 {
	text-align:center;
	font-size:12pt;
	font-weight: normal;
	letter-spacing: 2px;
	color:var(--myRedDark);
}

h3 {
	font-size:8pt;
}

h4 {
	text-align:center;
	font-size:10pt;
	font-weight: normal;
	letter-spacing: 2px;
	color:var(--myBlueDark);
}

p, ul, ol {
	text-align:left;
	font-size:8pt;
	margin-left:0px;
	padding-left:14px;
}

a {
	text-align:center;
	color:var(--textDarkColor);
	font-size:10pt;
	text-decoration:none;
}

li {
	margin-bottom:8px;
	padding-left:0px;
}

fieldset {
	width: 80%;
	margin: 2px auto 30px auto;
	border-color: var(--bg2Color);
	border-radius:8px;
}

legend {
	padding-left: 5px;
	padding-right: 5px;
	border: none;
	font-size: 12pt;
	color: var(--bg2Color);
}

input, select {
	padding: 6px;
	margin: 6px;
	border-width: 1px;
	border-style: solid;
	border-color: var(--bg1Color);
	border-radius: 4px;
	box-sizing: border-box;
	font-size: 10pt;
	font-family: Montserrat;
}

input[type=button],
input[type=submit],
input[type=reset] {
	cursor: pointer;
}

input:invalid,
select:invalid,
textarea:invalid {
	border-color: var(--myRedDark);
}

input:valid,
select:valid,
textarea:valid {
	border-color: var(--bg1Color);
}

table {
	width: 80%;
	margin: 5px auto 5px auto;
	border-collapse: separate;
	border-spacing: 0px 2px;
}

tr {
	vertical-align: middle;
}

tr:hover,
tr:active {
	background-color: var(--myOrangeLight);
}

th {
	text-align: center;
	background: var(--bg2Color);
	color: var(--textClearColor);
	font-weight: normal;
	padding: 5px;
}

td {
	text-align: center;
	padding: 2px;
}

dl {
	font-size: 10pt;
}

dt {
	padding: 5px 0 0 0;
}

dd {
	color: var(--textDarkColor);
	padding: 0 0 5px 0;
}

.none {
	display: none;
}

.block {
	display: block;
}

.protected {
	background-color: #ffc7c7;
}

.modifquiz {
	width: 12%;
}

.assesssynth {
	width: 120px;
}

.fontvingt {
	font-size: 20pt;
}

textarea {
	display: block;
	margin-left: auto;
	margin-right: auto;
	padding: 5px;
	border-width: 1px;
	border-style: solid;
	border-color: var(--bg0Color);
	border-radius: 5px;
	resize: none;
	outline: none;
	box-shadow: var(--shadowHover);
	font-size: 10pt;
	font-family: Montserrat;
}

.pleft {
	text-align: left;
}

.row {
	margin-left: auto;
	margin-right: auto;
	width: 90%;
}

.row:after {
	content: "";
	display: table;
	clear: both;
}

.column {
	float: left;
	padding: 10px;
}

.left {
	width: 50%;
}

.right {
	width: 50%
}

.largeleft {
	width: 70%;
}

.littleright {
	width: 30%;
}

.onecolumn {
	width: 60%;
	margin-left: auto;
	margin-right: auto;
}

.foot {
	width: 50%;
	border-radius: 4px;
	margin: 10px auto 50px auto;
	padding: 8px 0 8px 0;
	background-color: var(--bg1Color);
	box-shadow: var(--shadowNormal);
	text-align:center;
}

.foot.focus,
.foot:focus,
.foot:hover,
.foot.active {
	box-shadow: var(--shadowHover);
}

.foot a {
	font-size: 12pt;
	letter-spacing: 4px;
}

.footer {
	position: fixed;
	left: 0;
	bottom: 0;
	width: 100%;
	background-color: var(--bg2Color);
	color: var(--textClearColor);
	text-align: center;
	padding: 4px 0 4px 0;
	font-size: 10px;
}

.footer a {
	color: var(--textClearColor);
	font-size: 10px;
}

.msg {
	width: 50%;
	border-radius: 4px;
	margin: 20px auto 20px auto;
	background-color: var(--bg1Color);
	box-shadow: var(--shadowNormal);
	display: table;
	border-collapse: separate;
	border-spacing: 20px 10px;
}

.menu {
	width: 90%;
	border-radius: 4px;
	margin: 20px auto 20px auto;
	background-color: var(--bg1Color);
	box-shadow: var(--shadowNormal);
	display: table;
}

.msg.focus,
.msg:focus,
.msg:hover,
.msg.active,
.menu.focus,
.menu:focus,
.menu:hover,
.menu.active {
	box-shadow: var(--shadowHover);
}

.msg div,
.menu div {
	display: table-cell;
	vertical-align: middle;
}

.msg img,
.menu img {
	padding: 5px;
	width: 70px;
}

.msg a {
	font-size: 12pt;
}

.msg p {
	font-size: 12pt;
}

.menu a {
	font-size: 14pt;
}

.visualization p {
	font-size: 10pt;
	text-align: center;
}

.authcont {
	width: 60%;
	margin: auto;
	padding: 12% 0 0 0;
	display: table;
}

.auth {
	width: 50%;
	display: table-cell;
	vertical-align: middle;
}

.auth img {
	display: block;
	width: 50%;
	margin: auto;
}

.help {
	width: 60px;
	margin: auto;
}

.auth form {
	width: 80%;
	margin: auto;
	padding: 45px;
	text-align: center;
	background: var(--bg2Color);
	box-shadow: var(--shadowAuth);
}

.auth input {
	background: var(--bg0Color);
	outline: 0;
	width: 100%;
	border: 0;
	margin: 0 0 15px;
	padding: 15px 15px 15px 40px;
	font-size: 14px;
	color: var(--textDarkColor);
}

.auth input[type=text] {
	background-image: url('pict/user.png');
	background-position: 5px 10px;
	background-repeat: no-repeat;
	background-size: 25px auto;
}

.auth input[type=password] {
	background-image: url('pict/cadenas.png');
	background-position: 5px 10px;
	background-repeat: no-repeat;
	background-size: 25px auto;
}

.auth input[type=submit] {
	text-transform: uppercase;
	outline: 0;
	background: var(--bg1Color);
	color: var(--textDarkColor);
	width: 100%;
	border: 0;
	padding: 15px;
	font-size: 14px;
	cursor: pointer;
	box-shadow:var(--shadowNormal);
}

.auth input:hover[type=submit],
.auth input:active[type=submit],
.auth input:focus[type=submit] {
	box-shadow:var(--shadowHover);
}

.auth p {
	margin: 0;
	padding: 0px 15px 0px 15px;
	font-size: 14px;
	text-align: center;
	color: var(--textClearColor);
}

.auth a {
	margin: 0;
	padding: 0px 15px 0px 15px;
	font-size: 12px;
	text-align: center;
	font-weight: normal;
	text-decoration: underline;
	color: var(--textClearColor);
}

.captcha {
	vertical-align: middle;
}

.captcha img {
	display: block;
	float: left;
	width: 60%;
	margin: 0;
	padding: 10px 5px 5px 5px;
}

.captcha input {
	float: right;
	background: var(--bg0Color);
	outline: 0;
	width: 40%;
	border: 0;
	padding: 15px 5px 15px 5px;
	font-size: 14px;
	color: var(--textDarkColor);
}

.captcha input[type=text] {
	background-image: none;
}

.btnValid {
	border: none;
	text-align: center;
	text-decoration: none;
	display: inline-block;
	font-size: 10px;
	cursor:pointer;
	padding: 2px;
	margin-left: 20px;
	margin-right: 20px;
	border-radius: 2px;
	color: var(--textClearColor);
	background-color: var(--myGreenLight);
}

.btnValid.focus,
.btnValid:focus,
.btnValid:hover,
.btnValid.active {
	color: var(--textClearColor);
	background-color: var(--myGreenDark);
}

.btnWarning {
	border: none;
	text-align: center;
	text-decoration: none;
	display: inline-block;
	font-size: 10px;
	cursor:pointer;
	padding: 2px;
	margin-left: 20px;
	margin-right: 20px;
	border-radius: 2px;
	color: var(--textClearColor);
	background-color: var(--myOrangeLight);
}

.btnWarning.focus,
.btnWarning:focus,
.btnWarning:hover,
.btnWarning.active {
	color: var(--textClearColor);
	background-color: var(--myOrangeDark);
}

.btnDanger {
	border: none;
	text-align: center;
	text-decoration: none;
	display: inline-block;
	font-size: 10px;
	cursor:pointer;
	padding: 2px;
	border-radius: 2px;
	color: var(--textClearColor);
	background-color: var(--myRedLight);
}

.btnDanger.focus,
.btnDanger:focus,
.btnDanger:hover,
.btnDanger.active {
	color: var(--textClearColor);
	background-color: var(--myRedDark);
}

.valid {
	padding: 6px;
	margin: 6px;
	border-width: 1px;
	border-style: solid;
	border-color: var(--bg1Color);
	border-radius: 4px;
	box-sizing: border-box;
	cursor: pointer;
	background-color: var(--textClearColor);
}

.event {
	width: 90%;
	padding: 0 6px 0 6px;
	margin-left: auto;
	margin-right: auto;
	margin-bottom: 20px;
	border-style: solid;
	border-width: 2px;
	border-color: var(--bg2Color);
	border-radius:8px;
}

.event dl {
	font-size: 9pt;
}

.event dt {
	font-weight: bold;
	padding: 4px 0 0 0;
}

.event dd {
	color: var(--textDarkColor);
	padding: 0 0 4px 0;
}

.btn_suppr {
	width:32px;
	height:32px;
	border:0px;
	margin:0px;
	padding:0px;
	background-color: transparent;
	background-image:url(pict/delete.png);
}

.alertbackground {
	background-color: rgba(65, 70, 75, 0.9);
	position: fixed;
	width: 100%;
	height: 100%;
	top: 0;
	left: 0;
}


.alert {
	position: absolute;
	top: 40%;
	left: 50%;
	transform: translate(-50%, -50%);
	width: 20%;
	background-color: rgba(120, 125, 130, 1.0);
	border-left-style: solid;
	border-left-width: 30px;
	border-left-color: var(--myRedDark);
	border-radius: 4px;
	padding: 20px 40px 20px 10px;
	color: var(--textClearColor);
	text-align: center;
	box-shadow: var(--shadowAuth);
}

.alert p {
	text-align: center;
	font-size: 14px;
}

.alert a {
	text-align: center;
	font-size: 14px;
	padding: 5px 20px 5px 20px;
}

#a {
	background:var(--bg2Color);
	width:500px;
	margin-top:5px;
	margin-bottom:20px;
	margin-left:auto;
	margin-right:auto;
}

#b {
	background-color: var(--myOrangeDark);
	height:25px;
}

#c {
	padding-top:4px;
	color:var(--textClearColor);
	font-weight:bold;
	text-align: center;
}

.assess {
	width: 100%;
	padding: 0 6px 0 6px;
	margin-left: auto;
	margin-right: auto;
	margin-bottom: 20px;
	border-style: solid;
	border-width: 2px;
	border-color: var(--bg2Color);
	border-radius:8px;
}

.assess input[type=button] {
	margin: 0;
	padding: 0;
	font-size: 8pt;
	text-align: center;
	width: 15px;
	height: 15px;
	border: none;
	font-weight: bold;
	color: var(--textClearColor);
	background-color: var(--bg2Color);
}

.assess p {
	font-size: 10pt;
}

.assess dl {
	margin-left: 4%;
	padding: 5px;
	background-color: var(--bg2Color);
}

.assess dt {
	padding: 5px 0 5px 0;
}

.assess dd {
	margin-left: 4%;
}

.assess dd.comment {
	width: 75%;
	margin-left: auto;
	margin-right: auto;
	font-size: 8pt;
	padding: 5px;
	border-left: 8px solid var(--myRedDark);
	background-color: var(--myOrangeLight);
}

.redpoint {
	margin: 0 10px 0 0;
	height: 15px;
	width: 15px;
	background-color: var(--myRedDark);
	border-radius: 50%;
	display: inline-block;
}

.orangepoint {
	margin: 0 10px 0 0;
	height: 15px;
	width: 15px;
	background-color: var(--myOrangeDark);
	border-radius: 50%;
	display: inline-block;
}

.greenpoint {
	margin: 0 10px 0 0;
	height: 15px;
	width: 15px;
	background-color: var(--myGreenDark);
	border-radius: 50%;
	display: inline-block;
}

.separation {
	border-bottom: 2px solid var(--myRedLight);
	border-right: 2px solid var(--myRedLight);
}

.vis-time-axis .vis-grid.vis-major,
.vis-time-axis .vis-grid.vis-minor {
	border-color: var(--bg1Color);
}

.vis-time-axis .vis-grid.vis-saturday,
.vis-time-axis .vis-grid.vis-sunday {
	background: #d6d3c9;
}

.vis-time-axis .vis-text {
	font-size: 10px;
}

.projet_title {
	width: 50%;
	padding: 20px 0;
	font-size: 16pt;
	font-weight: bold;
	letter-spacing: 4px;
	color:var(--myOrangeDark);
	text-align: center;
}

.projet_detail {
	width: 50%;
	font-size:8pt;
	text-align: justify;
}

.project {
	width: 90%;
	margin: auto;
}

.project h3 {
	margin: 0 5%;
	padding: 8px;
	text-align:left;
	font-size:10pt;
	font-weight: normal;
	letter-spacing: 2px;
	color:var(--textDarkColor);
	background: var(--bg2Color);
}

.project table {
	padding: 5px;
	border: 1px solid var(--textDarkColor);
	border-radius: 4px;
}

.project tr:nth-child(even) {
	background-color: var(--bg0Color);
}

.project tr:nth-child(odd) {
	background-color: var(--bg1Color);
}

.project tr:nth-child(even):hover,
.project tr:nth-child(even):active,
.project tr:nth-child(odd):hover,
.project tr:nth-child(odd):active {
	background-color: var(--myOrangeLight);
}

.project td {
	padding: 2px 5px;
	text-align: left;
}

.project textarea {
	padding: 20px;
	border: none;
	background: var(--bg0Color);
	box-shadow: none;
}

.live {
	width: 80%;
	margin: auto;
	padding: 4px 6px;
	border-radius: 4px;
	text-align: center;
	color: var(--textClearColor);
	background: var(--myGreenDark);
}

.complete {
	width: 80%;
	margin: auto;
	padding: 4px 6px;
	border-radius: 4px;
	text-align: center;
	color: var(--textClearColor);
	background: var(--myOrangeDark);
}

.finished {
	width: 80%;
	margin: auto;
	padding: 4px 6px;
	border-radius: 4px;
	text-align: center;
	color: var(--textClearColor);
	background: var(--myRedDark);
}

.project_plus {
	padding: 0 5px;
	font-size: 16px;
	border-radius: 50%;
	color: var(--textClearColor);
	background-color: var(--myGreenLight);
	box-shadow: var(--shadowAuth);
}

.project_minus {
	padding: 0 7px;
	font-size: 16px;
	border-radius: 50%;
	color: var(--textClearColor);
	background-color: var(--myGreenLight);
	box-shadow: var(--shadowAuth);
}

.action_plus {
	padding: 0 5px;
	font-size: 16px;
	border-radius: 50%;
	color: var(--textClearColor);
	background-color: var(--myBlueLight);
	box-shadow: var(--shadowAuth);
}

.project_plus:hover,
.project_minus:hover,
.action_plus:hover {
	box-shadow: var(--shadowHover);
}

.task {
	width: 90%;
	margin: auto;
}

.task input[type="submit"] {
	padding: 6px 10px;
	border: none;
	font-size: 16px;
	border-radius: 50%;
	color: var(--textClearColor);
	background-color: var(--myBlueLight);
	box-shadow: var(--shadowAuth);
}

.task input[type="submit"]:hover {
	box-shadow: var(--shadowHover);
}

.project-container {
	width: 250px;
	padding: 0 20px;
	margin: auto;
}

.project-background {
	width: 100%;
	text-align: center;
	padding: 2px;
	border-width: 1px;
	border-style: solid;
	border-color: var(--bg1Color);
	border-radius: 4px;
}

.project-foreground {
	text-align: center;
	padding: 5px;
	color: var(--textClearColor);
	background-color: var(--myRedLight);
}

.task-container {
	width: 90px;
	padding: 0 10px;
	margin: auto;
}

.task-background {
	width: 100%;
	text-align: center;
	padding: 2px 2px;
	background-color: var(--bg2Color);
}

.task-foreground {
	text-align: center;
	padding: 2px;
	color: var(--textClearColor);
	background-color: var(--myRedLight);
}

.actions {
	font-size: 11pt;
	text-align: justify;
	color: var(--textDarkColor);
}

.actions h1 {
	font-size: 14pt;
	text-align: left;
	color: var(--myBlueDark);
}

.actions h2 {
	font-size: 12pt;
	text-align: left;
	color: var(--myBlueLight);
}

.actions p, ul {
	font-size: 10pt;
	text-align: justify;
	color: var(--textDarkColor);
}

.kanban {
	display: grid;
	width:80%;
	height: 70%;
	margin: 20px auto;
	grid-template-columns: repeat(4, 1fr);
	grid-column-gap: 10px;
}

.kanban_title {
	width:95%;
	margin: 5px auto;
	padding: 8px;
	border-radius: 5px;
	text-align: center;
}

.kanban_title_red {
	background: var(--myRedLight);
}

.kanban_title_orange {
	background: var(--myOrangeLight);
}

.kanban_title_blue {
	background: var(--myBlueLight);
}

.kanban_title_green {
	background: var(--myGreenLight);
}

.dropper {
	width:100%;
	border-radius: 4px;
	overflow-y: auto;
	transition: all 200ms linear;
}

.drop_red_line {
	border: 1px solid var(--myRedLight);
}

.drop_blue_line {
	border: 1px solid var(--myBlueLight);
}

.drop_orange_line {
	border: 1px solid var(--myOrangeLight);
}

.drop_green_line {
	border: 1px solid var(--myGreenLight);
}

.drop_hover {
	border-width: 2px;
}

.draggable {
	width:90%;
	margin: 5px auto;
	padding: 5px;
	border: 1px solid var(--bg2Color);
	border-radius: 5px;
	transition: all 200ms linear;
	user-select: none;
}

.draggable-name {
	width:100%;
	padding: 5px;
	text-align: center;
	border-radius: 5px;
	color: var(--textClearColor);
	background-color: var(--bg2Color);
	cursor: move;
}

.add_kanban {
	padding: 0 5px;
	font-size: 16px;
	border-radius: 50%;
	color: var(--textClearColor);
	background-color: var(--myBlueLight);
	cursor: pointer;
	float: right;
}

.del_kanban {
	padding: 0 4px;
	font-size: 12px;
	border-radius: 50%;
	color: var(--textClearColor);
	background-color: var(--myRedLight);
	cursor: pointer;
	float: right;
}

.modal {
	display: none;
	position: fixed;
	z-index: 1;
	padding: 5%;
	left: 0;
	top: 0;
	width: 100%;
	height: 100%;
	overflow-y: auto;
	background-color: rgba(0,0,0,0.6);
}

.modal_content {
	background-color: var(--bg0Color);
	margin: auto;
	padding: 10px;
	width: 60%;
	border-radius: 5px;
}

.close {
	color: var(--textDarkColor);
	float: right;
	font-size: 25px;
	font-weight: bold;
	cursor: pointer;
}

.kanban_description {
	width:95%;
	margin: 2px auto;
	padding: 5px;
	font-size: 8pt;
	text-align: justify;
	color: var(--textDarkColor);
}

.kanban_date {
	width:95%;
	margin: 2px auto;
	padding: 5px;
	text-align: left;
	color: var(--textDarkColor);
}

.kanban_date_normal {
	border-left: 8px solid var(--myGreenLight);
}

.kanban_date_limit {
	border-left: 8px solid var(--myOrangeLight);
}

.kanban_date_alert {
	border-left: 8px solid var(--myRedLight);
}

.gantt_container {
	width: 100%;
	background: var(--myRedLight);
}

.gantt_progress {
	text-align: center;
	color: var(--textClearColor);
	background: var(--myRedDark);
}
