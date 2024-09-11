import { Controller, StringMapObserver } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["filterButton"];
    initialize() {  
    }
    connect() {
        console.log("Calendar controller connected");
    }

    filter(event) {
        event.preventDefault();

        const url = new URL(window.location.href);
        const params = url.searchParams;

        // Get the values from your filter inputs or select elements
        const group = null;
        const user = null;
        const year = null;

        // Update the URL parameters
        //params.set("group", group);
        //params.set("user", user);
        //params.set("year", year);

        // Redirect to the updated URL
        window.location.href = url.toString();
    }
}