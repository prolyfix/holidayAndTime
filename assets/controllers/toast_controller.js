import { Controller, StringMapObserver } from '@hotwired/stimulus';

export default class extends Controller {
    newToast() {
        const toastContainer = document.querySelector("#toast_container");

        const toast = document.createElement("div");
        toast.classList.add("toast");
        toast.setAttribute("role", "alert");
        toast.setAttribute("aria-live", "assertive");
        toast.setAttribute("aria-atomic", "true");

        const toastHeader = document.createElement("div");
        toastHeader.classList.add("toast-header");

        const toastTitle = document.createElement("strong");
        toastTitle.classList.add("me-auto");
        toastTitle.textContent = "Toast Title";

        const toastCloseButton = document.createElement("button");
        toastCloseButton.classList.add("btn-close");
        toastCloseButton.setAttribute("data-bs-dismiss", "toast");
        toastCloseButton.setAttribute("aria-label", "Close");

        const toastBody = document.createElement("div");
        toastBody.classList.add("toast-body");
        toastBody.textContent = "This is the toast message.";

        toastHeader.appendChild(toastTitle);
        toastHeader.appendChild(toastCloseButton);
        toast.appendChild(toastHeader);
        toast.appendChild(toastBody);

        toastContainer.appendChild(toast);

        const bootstrapToast = new bootstrap.Toast(toast);
        bootstrapToast.show();
    }
}