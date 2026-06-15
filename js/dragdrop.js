
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
				if (!elt) {
					return;
				}
				elt.style.borderStyle = 'solid';
				elt.style.borderWidth = '1px';
				var target = e.target;
				while (target && !target.classList.contains('dropper')) {
					target = target.parentNode;
				}
				if (!target) {
					return;
				}
				target.classList.remove('drop_hover');
				var draggedElement = dndHandler.draggedElement;
				if (!draggedElement) {
					return;
				}
				var clonedElement = draggedElement.cloneNode(true);
				clonedElement = target.appendChild(clonedElement);
				dndHandler.applyDragEvents(clonedElement);
				draggedElement.parentNode.removeChild(draggedElement);
				var url = window.location.pathname;
				var filename = url.substring(url.lastIndexOf('/') + 1);
				var formData = new FormData();
				formData.append('task_id', data.substring(4));
				formData.append('progress', target.id);
				formData.append('csrf_token', window.schedioCsrfUpdateKanban);
				fetch(filename + '?action=update_kanban', {
					method: 'POST',
					body: formData,
					credentials: 'same-origin'
				}).then(function(response) {
					if (response.ok) {
						window.location.assign(filename + '?action=kanban');
					} else {
						alert('Erreur de mise à jour du Kanban');
						window.location.assign(filename + '?action=kanban');
					}
				}).catch(function() {
					alert('Erreur réseau lors de la mise à jour du Kanban');
					window.location.assign(filename + '?action=kanban');
				});
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
			if (droppers[i].childElementCount >= 8) {
				continue;
			} else {
				dndHandler.applyDropEvents(droppers[i]);
			}
		} else {
			dndHandler.applyDropEvents(droppers[i]);
		}
	}
})();
