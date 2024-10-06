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
    
        // Add a resize handle to the new element
        const resizeHandle = document.createElement('div');
        resizeHandle.className = 'resize-handle';
        newElement.appendChild(resizeHandle);
    
        // Append the new element to the drop zone
        if (dropzone instanceof Node) {
            dropzone.appendChild(newElement);
        } else {
            console.error('Dropzone is not a valid Node');
        }
    
        // Implement resizing logic
        let isResizing = false;
    
        resizeHandle.addEventListener('mousedown', (e) => {
            isResizing = true;
            document.addEventListener('mousemove', resize);
            document.addEventListener('mouseup', stopResize);
        });
    
        function resize(e) {
            if (isResizing) {
                newElement.style.height = e.pageY - newElement.getBoundingClientRect().top + 'px';
            }
        }
    
        function stopResize() {
            isResizing = false;
            document.removeEventListener('mousemove', resize);
            document.removeEventListener('mouseup', stopResize);
        }
    
        event.dataTransfer.clearData();
    }
}