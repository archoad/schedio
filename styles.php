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

switch ($_SESSION['mode']) {
	case 'standard':
		$bg0Color = '#e9e4db';
		$bg1Color = '#9eb1bb';
		$bg2Color = '#4d636f';
		$bg3Color = '#bab6af';
		$myOrangeLight = '#f0ad4e';
		$myOrangeDark = '#ec971f';
		$myRedLight = '#d9534f';
		$myRedDark = '#c9302c';
		$myGreenLight = '#5cb85c';
		$myGreenDark = '#449d44';
		break;
	case 'laposte':
		$bg0Color = '#ffffff';
		$bg1Color = '#ffcb05';
		$bg2Color = '#003da5';
		$bg3Color = '#ffffff';
		$myOrangeLight = '#ffcb05';
		$myOrangeDark = '#ffcb05';
		$myRedLight = '#e56a54';
		$myRedDark = '#e56a54';
		$myGreenLight = '#5cb85c';
		$myGreenDark = '#5cb85c';
		break;
	default:
		$bg0Color = '#e9e4db';
		$bg1Color = '#9eb1bb';
		$bg2Color = '#4d636f';
		$bg3Color = '#bab6af';
		$myOrangeLight = '#f0ad4e';
		$myOrangeDark = '#ec971f';
		$myRedLight = '#d9534f';
		$myRedDark = '#c9302c';
		$myGreenLight = '#5cb85c';
		$myGreenDark = '#449d44';
		break;
}
?>

@charset "utf-8";

:root {
	--bg0Color: <?php echo $bg0Color; ?>;
	--bg1Color: <?php echo $bg1Color; ?>;
	--bg2Color: <?php echo $bg2Color; ?>;
	--bg3Color: <?php echo $bg3Color; ?>;
	--myBlueLight: #337ab7;
	--myBlueDark: #286090;
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
	font-family: Tahoma;
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
}

input[type=button],
input[type=submit],
input[type=reset] {
	cursor: pointer;
}

table {
	width: 80%;
	margin-top: 5px;
	margin-bottom: 5px;
	margin-left: auto;
	margin-right: auto;
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
}

td {
	text-align: center;
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
	display: flex;
	width: 60%;
	margin-left: auto;
	margin-right: auto;
	padding: 12% 0 0 0;
}

.auth {
	width: 360px;
	flex: 50%;
}

.auth img {
	display: block;
	margin-left: auto;
	margin-right: auto;
	width:230px;
}

.auth form {
	position: relative;
	background: var(--bg2Color);
	max-width: 360px;
	margin: 0 auto 100px auto;
	padding: 45px;
	text-align: center;
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
	background-size: 25px Auto;
}

.auth input[type=password] {
	background-image: url('pict/cadenas.png');
	background-position: 5px 10px;
	background-repeat: no-repeat;
	background-size: 25px Auto;
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
	background-color: var(--bg3Color);
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

.vis-time-axis.vis-background {
	background-color: var(--$bg0Color);
}

.vis-item,
.vis-item.vis-selected {
	border-color: var(--textDarkColor);
	background-color: var(--textDarkColor);
}

.vis-time-axis .vis-grid.vis-major {
	border-color: var(--bg1Color);
}

.vis-time-axis .vis-grid.vis-minor {
	border-color: var(--bg1Color);
}
