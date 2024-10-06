import { Controller } from '@hotwired/stimulus';
export default class extends Controller {
    static targets = ["card", "widgetTable"];

    connect() {
        this.cardTargets.forEach(card => {
            card.setAttribute("draggable", true);
            card.addEventListener("dragstart", this.dragStart.bind(this));
        });

        this.widgetTableTarget.addEventListener("dragover", this.dragOver.bind(this));
        this.widgetTableTarget.addEventListener("drop", this.drop.bind(this));
    }

    dragStart(event) {
        event.dataTransfer.setData("text/plain", event.target.id);
    }

    dragOver(event) {
        event.preventDefault();
    }

    drop(event) {
        event.preventDefault();
        const id = event.dataTransfer.getData("text");
        const draggableElement = document.getElementById(id);
        this.widgetTableTarget.appendChild(draggableElement);
    }
}