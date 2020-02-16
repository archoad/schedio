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


function kanban_ok(form) {
	for(i=0; i<form.elements.length; i++) {
		if (form.elements[i].value === '') {
			alert('Formulaire incomplet');
			form.elements[i].style.backgroundColor='#FFC7C7';
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


function confirmDelete() {
	var answer = confirm("Voulez-vous supprimer cette tâche?");
	return answer;
}


(function() {
	var dndHandler = {
		draggedElement: null,

		applyDragEvents: function(element) {
			element.draggable = true;
			var dndHandler = this;
			element.addEventListener('dragstart', function(e) {
				dndHandler.draggedElement = e.target;
				var elt = document.getElementById(e.target.id);
				elt.style.borderStyle = 'dashed';
				elt.style.borderWidth = '2px';
				e.dataTransfer.setData('text/plain', e.target.id);
			}, false);
		},

		applyDropEvents: function(dropper) {
			dropper.addEventListener('dragover', function(e) {
				e.preventDefault();
				this.classList.add('drop_hover');
			}, false);
			dropper.addEventListener('dragleave', function(e) {
				this.classList.remove('drop_hover');
			}, false);
			var dndHandler = this;
			dropper.addEventListener('drop', function(e) {
				e.preventDefault();
				var data = e.dataTransfer.getData('text/plain');
				var elt = document.getElementById(data);
				elt.style.borderStyle = 'solid';
				elt.style.borderWidth = '1px';
				var target = e.target;
				draggedElement = dndHandler.draggedElement;
				clonedElement = draggedElement.cloneNode(true);
				while(target.className.indexOf('dropper') == -1) {
					target = target.parentNode;
				}
				target.classList.remove('drop_hover');
				clonedElement = target.appendChild(clonedElement);
				dndHandler.applyDragEvents(clonedElement);
				draggedElement.parentNode.removeChild(draggedElement);
				var url = window.location.pathname;
				var filename = url.substring(url.lastIndexOf('/')+1);
				var new_href=filename+'?action=update_kanban&progress='+target.id+'&task='+data.substring(4);
				window.location.assign(new_href);
			}, false);
		}
	};

	var elements = document.querySelectorAll('.draggable');
	var elementsLen = elements.length;
	for(var i = 0 ; i < elementsLen ; i++) {
		dndHandler.applyDragEvents(elements[i]);
	}
	var droppers = document.querySelectorAll('.dropper');
	var droppersLen = droppers.length;
	for(var i = 0 ; i < droppersLen ; i++) {
		if (droppers[i].id == 'progress') {
			if (droppers[i].childElementCount >= 4) {
				continue;
			} else {
				dndHandler.applyDropEvents(droppers[i]);
			}
		} else {
			dndHandler.applyDropEvents(droppers[i]);
		}
	}
})();
