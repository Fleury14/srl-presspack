export default {
    init() {
      const toggleBtn = document.getElementsByClassName('toggleResults');
      Array.prototype.forEach.call(toggleBtn, button => {
        button.addEventListener('click', (e) => {
          e.target.nextElementSibling.classList.toggle('hide-results');
        })
      })
      
    },
    finalize() {
      // JavaScript to be fired on the home page, after the init JS
    },
  };
  