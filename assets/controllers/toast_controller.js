import { Controller, StringMapObserver } from '@hotwired/stimulus';

export default class extends Controller {

    static targets = ['toastContainer'];

    initialize(){
          this.toastContainerTarget.addEventListener("awesome", (event) => {
            this.new(event);
          });
    }


    new(event) {
        const toastContainer = this.toastContainerTarget;
        const toast = document.createElement("div");
        toast.classList.add("toast");
        toast.setAttribute("role", "alert");
        toast.setAttribute("aria-live", "assertive");
        toast.setAttribute("aria-atomic", "true");
        const toastHeader = document.createElement("div");
        toastHeader.classList.add("toast-header");
        const toastTitle = document.createElement("strong");
        toastTitle.classList.add("me-auto");
        toastTitle.textContent = "Message";

        const toastCloseButton = document.createElement("button");
        toastCloseButton.classList.add("btn-close");
        toastCloseButton.setAttribute("data-bs-dismiss", "toast");
        toastCloseButton.setAttribute("aria-label", "Close");

        const toastBody = document.createElement("div");
        toastBody.classList.add("toast-body");
        toastBody.textContent = event.detail.text;

        toastHeader.appendChild(toastTitle);
        toastHeader.appendChild(toastCloseButton);
        toast.appendChild(toastHeader);
        toast.appendChild(toastBody);

        toastContainer.appendChild(toast);

        const bootstrapToast = new bootstrap.Toast(toast);
        bootstrapToast.show();
    }


    close() {
        event.currentTarget.closest(".toast").remove();
        this.new();
    }
    
}