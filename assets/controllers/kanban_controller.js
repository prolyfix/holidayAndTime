import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["item", "columnContent"];

    initialize() {
        this.applyStyles();
    }

    connect() {
        console.log("Kanban controller connected");
        this.itemTargets.forEach((item) => {
            item.setAttribute("draggable", "true");
            item.addEventListener("dragstart", this.dragStart.bind(this));
        });

        this.columnContentTargets.forEach((column) => {
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
        const draggableElement = document.getElementById(id);
        const dropzone = event.currentTarget;
        dropzone.appendChild(draggableElement);
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
            body: JSON.stringify({ taskId: taskId, newState: newState })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Task state updated successfully');
                } else {
                    console.error('Failed to update task state');
                }
            })
            .catch(error => console.error('Error:', error));
    }

    applyStyles() {
        const style = document.createElement('style');
        style.innerHTML = `
            .kanban-board {
                display: flex;
                gap: 20px;
            }

            .kanban-column {
                flex: 1;
                background-color: #f4f4f4;
                border-radius: 5px;
                padding: 10px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }

            .kanban-column-header {
                font-weight: bold;
                margin-bottom: 10px;
            }

            .kanban-column-content {
                min-height: 200px;
                padding: 10px;
                background-color: #fff;
                border-radius: 5px;
                box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.1);
            }

            .kanban-card {
                background-color: #fff;
                border: 1px solid #ddd;
                border-radius: 5px;
                padding: 10px;
                margin-bottom: 10px;
                box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
                cursor: grab;
            }

            .kanban-card:active {
                cursor: grabbing;
            }

            .kanban-card-title {
                font-weight: bold;
            }

            .kanban-card-description {
                margin-top: 5px;
            }

            .kanban-card-due-date {
                margin-top: 10px;
                font-size: 0.9em;
                color: #888;
            }
        `;
        document.head.appendChild(style);
    }
}