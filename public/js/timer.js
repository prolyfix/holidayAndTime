export class Timer {
    constructor() {
        this.startTime = 0;
        this.elapsedTime = 0;
        this.timerInterval = null;
        this.timerIntervalMinutes = null;
        this.isStarted = false
        this.display = 'h:i'
        this.isBreak = false
    }

    start() {
        if (this.timerInterval) return; // Prevent multiple intervals
        this.isStarted = true;
        this.isBreak = false;
        this.startTime = Date.now() - this.elapsedTime;
        console.log(Date.now());
        this.timerInterval = setInterval(() => {
            this.elapsedTime = this.elapsedTime + 1000;
            const event = new CustomEvent('timerSecond', { detail: this.getTime() });
            document.dispatchEvent(event);
        }, 1000);
        this.timerIntervalMinutes = setInterval(() => {
            const event = new CustomEvent('timerMinute', { detail: this.getTime() });
            document.dispatchEvent(event);
        }, 60000);
    }

    break() {
        clearInterval(this.timerInterval);
        clearInterval(this.timerIntervalMinutes);
        this.timerInterval = null;
        this.isBreak = true;
    }
    stop() {
        clearInterval(this.timerInterval);
        clearInterval(this.timerIntervalMinutes);
        var temp = this.elapsedTime;
        this.elapsedTime = 0;
        this.timerInterval = null;
        return temp;
    }
    setDisplay(display) {
        this.display = display;
    }
    getTime() {
        let seconds = Math.floor(this.elapsedTime / 1000) % 60;
        let minutes = Math.floor(this.elapsedTime / (1000 * 60)) % 60;
        let hours = Math.floor(this.elapsedTime / (1000 * 60 * 60)) % 24;
        if (this.diplay = 'h:i') {
            return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
        }
        return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }

    setElapsedTime(time) {
        console.log(time);
        this.elapsedTime = time*1000*60;
        this.start();
    }
    getElapsedTime() {
        return this.elapsedTime;
    }
}