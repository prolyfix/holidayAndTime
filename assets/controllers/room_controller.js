import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["slot","employee"];

    connect() {
        this.employeeTargets.forEach((employee) => {
            employee.setAttribute("draggable", "true");
            employee.addEventListener("dragstart", this.dragStart.bind(this));
        });
        this.slotTargets.forEach((column) => {
            column.addEventListener("dragover", this.dragOver.bind(this));
            column.addEventListener("drop", this.drop.bind(this));
        });
    }

    dragStart(event) {
        console.log("dragStart");
        event.dataTransfer.setData("text/plain", event.target.id);
        event.dataTransfer.effectAllowed = "move";
    }

    dragOver(event) {
        console.log("dragOver");
        event.preventDefault();
        event.dataTransfer.dropEffect = "move";
    }

    drop(event) {
        console.log("drop");
        event.preventDefault();
        const id = event.dataTransfer.getData("text/plain");
        console.log(id);
        const draggableElement = document.getElementById(id);
        const dropzone = event.currentTarget;
    
        // Create a new element
        const newElement = draggableElement.cloneNode(true);
        newElement.id = `new-${id}-${Date.now()}`; // Ensure unique ID
        newElement.classList.add('resizable-element');
    
        // Append the new element to the drop zone
        if (dropzone instanceof Node) {
            dropzone.appendChild(newElement);
        } else {
            console.error('Dropzone is not a valid Node');
        }
    
        // Make the new element resizable
        if (typeof $ !== 'undefined' && $.fn.resizable) {
            $(newElement).resizable();
        } else {
            console.error('jQuery or jQuery UI is not loaded');
        }
    
        event.dataTransfer.clearData();
    
        // Extract task ID and new state from the element and dropzone
        const taskId = id.split('-')[1]; // Assuming ID format is "todo-1", "inprogress-2", etc.
        const newState = dropzone.closest('.kanban-column').querySelector('.kanban-column-header').textContent.trim();
    
        // Send POST request to update task state
        fetch('/ajax/update-task-state', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                taskId: taskId,
                newState: newState,
            }),
        })
        .then(response => response.json())
        .then(data => {
            console.log('Task state updated:', data);
        })
        .catch(error => {
            console.error('Error updating task state:', error);
        });
    }
    resize(event) {
        const element = event.target;
        const deltaY = event.clientY - element.offsetTop;
        const newHeight = deltaY + element.offsetHeight;

        element.style.height = `${newHeight}px`;
    }
}