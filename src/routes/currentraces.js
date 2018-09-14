export default {
    init() {
      const toggleBtn = document.getElementsByClassName('toggleResults');
      Array.prototype.forEach.call(toggleBtn, button => {
        button.addEventListener('click', (e) => {
          e.target.nextElementSibling.classList.toggle('hide-results');
        })
      });

      const raceClocks = document.querySelectorAll('.race-clock');
      raceClocks.forEach(clock => {
        console.log(`Date now: ${Math.floor(Date.now() / 1000)}, Start time: ${clock.dataset.start}`);
        setInterval(() => {
          const secondsElapsed = Math.floor(Date.now() / 1000) - clock.dataset.start;
          clock.innerHTML = parseTime(secondsElapsed);
        }, 1000)
      })
      
      function parseTime(timeInSeconds) {
        let s = Math.floor(timeInSeconds % 60);
        let m = Math.floor(timeInSeconds / 60 % 60);
        let h = Math.floor(timeInSeconds / 3600);
        if (s < 10) s = '0' + s;
        if (m < 10) m = '0' + m;
        return `${h}:${m}:${s}`;
      }
 
    },
    finalize() {
      // JavaScript to be fired on the home page, after the init JS
    },
  };
  