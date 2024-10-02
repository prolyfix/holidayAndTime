import { Controller, StringMapObserver } from '@hotwired/stimulus';


export default class extends Controller {
    static targets = ["sidebar", "content"];

    connect() {
        document.querySelectorAll('a.sidebar-action').forEach(link => {
            link.addEventListener('click', this.openSidebar.bind(this));
        });
    }

    async openSidebar(event) {
        event.preventDefault();
        const url = event.currentTarget.getAttribute('href');
        const response = await fetch(url);
        const html = await response.text();

        // Parse the fetched HTML and extract the content inside section.main-content
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const mainContent = doc.querySelector('section.content-body');

        if (mainContent) {
            const form = mainContent.querySelector('form');
            if (form && !form.getAttribute('action')) {
                // Set the form action to the current URL if it is empty
                form.setAttribute('action', url);
            }

            // Add data-action to the submit button to call the submit action
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.setAttribute('data-action', 'click->sidebar#submit');
            }
            this.contentTarget.innerHTML = mainContent.innerHTML;
        } else {
            console.error('section.main-content not found in the fetched HTML');
        }

        this.sidebarTarget.classList.add('show');
    }

    close(){
        this.sidebarTarget.classList.remove('show');
    }

    async submit(event) {
        event.preventDefault();
        const form = event.currentTarget.closest('form');
        const formData = new FormData(form);
        const actionUrl = form.getAttribute('action');

        try {
            const response = await fetch(actionUrl, {
                method: 'POST',
                body: formData,
            });
            console.log(response);
            if (response.ok) {
                this.close();
                // Handle successful form submission (e.g., close sidebar, show success message, etc.)
            } else {
                console.error('Form submission failed:', response.statusText);
                // Handle form submission error (e.g., show error message)
            }
        } catch (error) {
            console.error('Error submitting form:', error);
            // Handle network or other errors
        }
    }
}