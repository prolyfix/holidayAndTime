import { Controller, StringMapObserver } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["filterButton","filterSelect"];
    initialize() {  
        
    }
    connect() {
        console.log("Global controller connected");
        document.querySelectorAll('.dropdown-toggle').forEach(element => {
            element.addEventListener('click', this.toggleDropdown.bind(this));
        });
    }

    toggleDropdown(event) {
        const dropdown = event.currentTarget.nextElementSibling;
        dropdown.classList.toggle("show");
    }
}