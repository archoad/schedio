function myAlert(txt, elt=null) {
	var msg = document.createElement('p');
	msg.appendChild(document.createTextNode(txt));

	var btnAlert = document.createElement('a')
	btnAlert.setAttribute('class', 'btnDanger');
	btnAlert.appendChild(document.createTextNode('OK'));
	btnAlert.href = '#';
	btnAlert.onclick = function() { removeAlert(elt); }

	var divAlert = document.createElement('div');
	divAlert.setAttribute('class', 'alert');
	divAlert.appendChild(msg);
	divAlert.appendChild(btnAlert);

	var divBackground = document.createElement('div');
	divBackground.setAttribute('class', 'alertbackground');
	divBackground.id = 'caution';
	divBackground.appendChild(divAlert);

	document.body.appendChild(divBackground);
}


function removeAlert(elt) {
	divAlert = document.getElementById('caution');
	document.body.removeChild(divAlert);
	if (elt != null) {
		elt.focus();
		elt.style.backgroundColor='#FFC7C7';
	}
}


function champs_ok(form) {
	for(i=0; i<form.elements.length; i++) {
		if (form.elements[i].value === '') {
			myAlert('Formulaire incomplet', form.elements[i]);
			return false;
		}
	}
	return true;
}


function password_ok(form) {
	if (form.new1.value.length < 6) {
		myAlert('Le mot de passe doit contenir plus de 6 caractères', form.new1);
		return false;
	}
	if (form.new1.value.match(/^[a-zA-Z0-9]*$/) != form.new1.value) {
		myAlert('Le mot de passe ne doit contenir que des caractères alphanumériques', form.new1);
		return false;
	}
	if (form.new1.value != form.new2.value) {
		myAlert('Erreur de saisie', form.new2);
		return false;
	}
	return true;
}


function fixMinDate(elt) {
	document.getElementById('datefin').min = elt.value;
}
