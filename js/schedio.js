function validatePattern() {
	var pattern = /^(?=.*[a-z])(?=.*[0-9])(?=.{6,})/;
	var pass1 = document.getElementById('new1').value;
	if (pass1.match(pattern)) {
		document.getElementById('new1').setCustomValidity('');
	} else {
		document.getElementById('new1').setCustomValidity('Doit contenir majuscules, minuscules, chiffres et au moins 6 caractères');
	}
}


function validatePassword() {
	var pass1 = document.getElementById('new1').value;
	var pass2 = document.getElementById('new2').value;
	if (pass1 != pass2) {
		document.getElementById('new2').setCustomValidity('Les mots de passe ne correspondent pas');
	} else {
		document.getElementById('new2').setCustomValidity('');
	}
}


function fixMinDate() {
	let elt = document.getElementById('datedebut');
	document.getElementById('datefin').min = elt.value;
}


function base64DecodeUnicode(str) {
	percentEncodedStr = atob(str).split('').map(function(c) {
		return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
	}).join('');
	return decodeURIComponent(percentEncodedStr);
}


function displayAddModal() {
	var modal = document.getElementById('add_kanban_form');
	modal.style.display = 'block';
}


function displayModifyModal(data) {
	items = base64DecodeUnicode(data).split(':');
	var modal = document.getElementById('modify_kanban_form');
	document.getElementById('uid').value = items[0];
	document.getElementById('unom').value = items[1];
	document.getElementById('udescription').value = items[2];
	document.getElementById('udatefin').value = items[3];
	document.getElementById('upriority').value = items[4];
	modal.style.display = 'block';
}


function displayDelModal(id) {
	var modal = document.getElementById('del_kanban_form');
	document.getElementById('delid').value = id;
	modal.style.display = 'block';
}


function confirmDelete() {
	var answer = confirm("Voulez-vous supprimer cette tâche?");
	return answer;
}
