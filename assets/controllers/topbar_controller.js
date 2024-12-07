import { Controller, StringMapObserver } from '@hotwired/stimulus';

export default class extends Controller {
    initialize(){
        alert("icinia");
    }

    toggleNext(event) {
        event.preventDefault();
        event.stopPropagation();
        const next = event.currentTarget.nextElementSibling;
        console.log(next);
        next.classList.toggle('show');
    }
}