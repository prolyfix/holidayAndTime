import { Controller, StringMapObserver } from '@hotwired/stimulus';
import Timer from '../utilities/timer.js';
export default class extends Controller {
  static targets = ['commentable','choseAction'];
  initialize() {
  }
  connect() {
    this.timer = new Timer();
  }

  startWorking(){
    fetch('/ajax/startWorking', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        commentable: event.currentTarget.dataset.value
      })
    })

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
        commentable: event.currentTarget.dataset.value
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
          const playButton = document.createElement('button');

          // create play button
          playButton.classList.add('btn', 'btn-play');
          playButton.innerHTML = '<i class="fa fa-play"></i>';
          playButton.dataset.action ="click->time#startWorking";
          playButton.dataset.value = item.id;
          playButton.setAttribute('data-time-target', 'chosenAction');

          // Append buttons to the div
          div.appendChild(playButton);

          listContainer.appendChild(div);
          listContainer.classList.remove('hidden');
        });
      });
  }

}
