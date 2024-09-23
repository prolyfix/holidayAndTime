import { Controller, StringMapObserver } from '@hotwired/stimulus';
import Timer from '../utilities/timer.js';
export default class extends Controller {
  static targets = ['commentable', 'choseAction'];
  initialize() {

    this.timer = new Timer();
    this.timer.setDisplay('h:i:s');
    this.retrieveElapsedTime().then((data) => {
      this.timer.setElapsedTime(data.elapsedTime);
      this.timer.isBreak = data.isBreak;
      if(data.commentable){
        this.commentableTarget.value = data.commentable;
        document.getElementById('commentableId').value = data.commentableId;
      }
      document.getElementById('timer').innerHTML = this.timer.getTime();
      if (data.message == 'error') {
        isOpen = false;
      }
      
      if (data.elapsedTime > 0) {
        document.getElementById('break').style.display = 'inline-block';
        document.getElementById('start').style.display = 'none';
      }
      if (data.isBreak) {
        document.getElementById('start').style.display = 'inline-block';
        document.getElementById('break').style.display = 'none';
      }
    });
    this.timer.start();
  }


  connect() {

  }

  saveComment(){
    fetch('/ajax/saveComment', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        comment: document.getElementById('relation').value,
        commentableId: document.getElementById('commentableId').value
      })
    }).then(response => response.json())
      .then(data => {
        document.getElementById('comment').value = '';
      })
      .catch(function (error) {
        console.log(error);
      });
  }


  start() {
    console.log("start");
    this.timer.start();
    fetch('/ajax/start').then(response => response.json())
      .catch(function (error) {
        console.log(error);
      });
  }



  stop() {
    console.log("stop");
    let elapsedTime = this.timer.stop();
    fetch('/ajax/stop', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        elapsedTime: elapsedTime
      })
    }).then(response => response.json())
      this.timer.stop()
  }


  startWorking(event) {
    const name = event.currentTarget.dataset.name;  
    fetch('/ajax/startWorking', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        commentable: event.currentTarget.dataset.value
      })
    }).then(response => response.json())
      .then(data => {
        this.retrieveElapsedTime().then((data) => { this.timer.setElapsedTime(data.elapsedTime); })
        console.log(event)
        this.commentableTarget.value = name;
        this.timer.start();
        let list = document.getElementById('commentable-list')
        list.classList.add('hidden');
        list.innerHTML = '';

      });

  }

  retrieveElapsedTime() {
    return new Promise((resolve, reject) => {
      axios.get('/ajax/retrieveElapsedTime')
      .then(function (response) {
        resolve(response.data);
      })
      .catch(function (error) {
        reject(error);
      })
    })
  }

  changeCommentable(){
    document.getElementById('actualCommentable').style.display  = 'none';
    document.getElementById('comment').style.display  = 'inline-block'; 
    document.getElementById('cancelChangeCommentable').style.display  = 'inline-block';
    document.getElementById('changeCommentable').style.display  = 'none';
    
  }

  cancelChangeCommentable(){
    document.getElementById('actualCommentable').style.display  = 'inline-block';
    document.getElementById('comment').style.display  = 'none'; 
    document.getElementById('cancelChangeCommentable').style.display  = 'none';
    document.getElementById('changeCommentable').style.display  = 'inline-block';
  }

  retrieveList() {
    let search = this.commentableTarget.value;
    console.log(search);
    if(search.length < 2){
      let list = document.getElementById('commentable-list')
      list.classList.add('hidden');
      return
    } 

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
          const playButton = document.createElement('button');

          // create play button
          playButton.classList.add('btn', 'btn-play');
          playButton.innerHTML = '<i class="fa fa-play"></i>';
          playButton.dataset.action = "click->time#startWorking";
          playButton.dataset.value = item.id;
          playButton.dataset.name = item.name;
          playButton.setAttribute('data-time-target', 'chosenAction');

          // Append buttons to the div
          div.appendChild(playButton);

          listContainer.appendChild(div);
          listContainer.classList.remove('hidden');
        });
      });
  }

}
