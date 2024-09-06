import { Controller } from "stimulus";

export default class extends Controller {
    static targets = ["item"];
    initialize() {
    }   
    connect() {
        console.log("Kanban controller connected");
        this.itemTargets.forEach((item) => {
            item.addEventListener("dragstart", this.dragStart.bind(this));
            item.addEventListener("dragover", this.dragOver.bind(this));
            item.addEventListener("drop", this.drop.bind(this));
        });
    }

    dragStart(event) {
        alert("coucou");
        event.dataTransfer.setData("text/plain", event.target.id);
    }

    dragOver(event) {
        event.preventDefault();
    }

    drop(event) {
        event.preventDefault();
        const itemId = event.dataTransfer.getData("text/plain");
        const item = document.getElementById(itemId);
        const target = event.currentTarget;
        target.appendChild(item);
    }
}