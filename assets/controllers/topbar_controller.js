import { Controller, StringMapObserver } from '@hotwired/stimulus';

export default class extends Controller {
    initialize(){
    }

    toggleNext(event) {
        event.preventDefault();
        event.stopPropagation();
        const next = event.currentTarget.nextElementSibling;
        this.hideAllDropdowns();
        next.classList.toggle('show');
    }

    hideAllDropdowns(event) {
        const dropdowns = document.querySelectorAll('.dropdown-menu');
        dropdowns.forEach((dropdown) => {
            dropdown.classList.remove('show');
        });
    }
}