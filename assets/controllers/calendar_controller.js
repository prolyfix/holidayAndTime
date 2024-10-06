import { Controller, StringMapObserver } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["filterButton","filterSelect"];
    initialize() {  
    }
    connect() {
        console.log("Calendar controller connected");
    }

    filter(event) {
        event.preventDefault();

        const url = new URL(window.location.href);
        const params = url.searchParams;

        console.log(this.filterSelectTarget);


        this.filterSelectTargets.forEach((element) => {
            const name = element.getAttribute("name");
            const value = element.value;
            if(value.length == 0){
                params.delete(name);
            }
            params.set(name, value);
            console.log(params);
        });

        window.location.href = url.toString();
    }
}