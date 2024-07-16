import { Timer } from './timer.js';

function start() {
    axios.get('/ajax/start')
    .catch(function (error) {
        console.log(error);
    });
}
function stop(elapsedTime){
    axios.post('/ajax/stop',{elapsedTime: elapsedTime},{ headers: {
            'Content-Type': 'application/json'
    }})
    .catch(function (error) {
        console.log(error);
    });
}
function breakTime(){
    axios.post('/ajax/break',{elapsedTime: timer.getElapsedTime(), isBreak: timer.isBreak},{ headers: {
        'Content-Type': 'application/json'
      }})
    .catch(function (error) {
        console.log(error);
    });
}
function retrieveElapsedTime(){
    return new Promise((resolve,reject) => { axios.get('/ajax/retrieveElapsedTime')
        .then(function (response) {
            resolve(response.data);
        })
        .catch(function (error) {
            reject(error);
        }) 
    })
}

let timer = new Timer();
timer.setDisplay('h:i:s');
retrieveElapsedTime().then((data) => {
    timer.setElapsedTime(data.elapsedTime);
    timer.isBreak = data.isBreak;
    document.getElementById('timer').innerHTML = timer.getTime();
    if(data.elapsedTime > 0){
        document.getElementById('break').style.display = 'inline-block';
        document.getElementById('start').style.display = 'none';
    }
    if(data.isBreak){
        document.getElementById('start').style.display = 'inline-block';
        document.getElementById('break').style.display = 'none';        
    }
});
document.addEventListener('DOMContentLoaded', function() {
    var startButton = document.getElementById('start');
    if (startButton) {
        startButton.addEventListener('click', function() {
            start();
            timer.isBreak = false;
            document.getElementById('break').style.display = 'inline-block';
            this.style.display = 'none';    
            timer.start();
        });

    }
    var startButton = document.getElementById('stop');
    if (startButton) {
        startButton.addEventListener('click', function() {
            var elapsedTime = timer.getElapsedTime();
            stop(elapsedTime);
            timer.stop();
        });
    }    
    var startButton = document.getElementById('break');
    if (startButton) {
        startButton.addEventListener('click', function() {
            document.getElementById('start').style.display = 'inline-block';
            this.style.display = 'none';    
            timer.isBreak = true;
            timer.break();
            breakTime();
        });
    }      
});
document.addEventListener('timerSecond', (e) => {
    document.getElementById('timer').innerHTML = e.detail // e.detail will contain the time
});
document.addEventListener('timerMinute', (e) => {
    breakTime();
});