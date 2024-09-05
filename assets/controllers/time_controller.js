import { Controller, StringMapObserver } from '@hotwired/stimulus';
import Timer from '../utilities/timer.js';
export default class extends Controller {
  static targets = ['commentable'];
  initialize() {
  }
  connect() {
    //alert("ici");
    this.timer = new Timer();
  }
  retrieveList() {
    console.log(this.commentableTarget.value);
    fetch('/ajax/retrieveList', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        commentable: this.commentableTarget.value
      })
    }
    )
      .then(response => response.json())
      .then(data => {
        this.list = data;
        // Clear the existing list
        const listContainer = document.getElementById('commentable-list');
        listContainer.innerHTML = '';

        // Append new div elements based on the data
        this.list.forEach(item => {

          const div = document.createElement('div');
          div.textContent = item.name; // Assuming 'name' is the field in the data
          listContainer.appendChild(div);
          listContainer.classList.remove('hidden');
        });
      });
  }

}
