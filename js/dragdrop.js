
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
