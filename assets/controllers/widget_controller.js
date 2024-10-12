import { Controller } from '@hotwired/stimulus';
export default class extends Controller {
    static targets = ["card", "widgetTable"];

    connect() {
        this.cardTargets.forEach(card => {
            card.setAttribute("draggable", true);
            card.addEventListener("dragstart", this.dragStart.bind(this));
        });

        this.widgetTableTargets.forEach(widgeTable => {
            console.log(widgeTable);
            widgeTable.addEventListener("dragover", this.dragOver.bind(this))
            widgeTable.addEventListener("drop", this.drop.bind(this));
        });
        this.retrieveCurrentWidgetPos();

        
    }

    retrieveCurrentWidgetPos() {
        fetch('/ajax/getWidgetPos', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            console.log('Success:', data);
            data.forEach(widget => {
                const row = widget.row;
                const column = widget.column;
                const widgetTable = document.querySelector(`[data-row="${row}"][data-column="${column}"]`);
                widgetTable.innerHTML = widget.widgetEl;
            });
        })
        .catch((error) => {
            console.error('Error:', error);
        });
    }


    dragStart(event) {
        event.dataTransfer.setData("text/plain", event.target.id);
    }

    dragOver(event) {
        event.preventDefault();
    }

    delete(event) {
        const widgetId = event.currentTarget.dataset.widgetid;
        fetch('/ajax/deleteWidget', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                widgetId: widgetId
            }),
        })
        .then(response => response.json())
        .then(data => {
            console.log('Success:', data);
            const widget = document.querySelector(`[data-widgetid="${widgetId}"]`);
            widget.parentElement.remove();
        })
        .catch((error) => {
            console.error('Error:', error);
        });
    }

    drop(event) {
        event.preventDefault();
        const id = event.dataTransfer.getData("text");
        console.log(id)
        const draggableElement = document.getElementById(id);
        console.log(draggableElement);
        const width = draggableElement.dataset.width;
        draggableElement.style.width = width ;
        if (draggableElement instanceof Node) {
            event.currentTarget.appendChild(draggableElement);
            const row = event.currentTarget.dataset.row
            const column = event.currentTarget.dataset.column
            const widgetId = draggableElement.dataset.widgetid;
            fetch('/ajax/newWidgetPos', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    row: row,
                    column: column,
                    widgetId: widgetId
                }),
            })
            .then(response => response.json())
            .then(data => {
                console.log('Success:', data);
            })
            .catch((error) => {
                console.error('Error:', error);
            });
        } else {
            console.error('Draggable element is not a valid Node');
        }
    }
}