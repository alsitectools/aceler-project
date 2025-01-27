<style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap');

* {
  box-sizing: border-box;
}

.saving-container {
  width: 100%;
  max-width: 520px;
  text-align: center;
  color: #fff;
  position: relative;
  margin: 0 32px;
}

.saving-container::before {
  content: '';
  position: absolute;
  width: 100%;
  height: 3px;
  background-color: #fff;
  bottom: 0;
  left: 0;
  border-radius: 10px;
  animation: movingLine 2.4s infinite ease-in-out;
}

@keyframes movingLine {
  0% {
    opacity: 0;
    width: 0;
  }

  33.3%, 66% {
    opacity: 0.8;
    width: 100%;
  }

  85% {
    width: 0;
    left: initial;
    right: 0;
    opacity: 1;
  }

  100% {
    opacity: 0;
    width: 0;
  }
}

.saving-text {
  font-size: 5vw;
  line-height: 64px;
  letter-spacing: 10px;
  margin-bottom: 32px;
  display: flex;
  justify-content: space-evenly;
}

.saving-text span {
  animation: moveLetters 2.4s infinite ease-in-out;
  transform: translatex(0);
  position: relative;
  display: inline-block;
  opacity: 0;
  text-shadow: 0px 2px 10px rgba(46, 74, 81, 0.3);
}

.saving-text span:nth-child(1) {
  animation-delay: 0.1s;
}

.saving-text span:nth-child(2) {
  animation-delay: 0.2s;
}

.saving-text span:nth-child(3) {
  animation-delay: 0.3s;
}

.saving-text span:nth-child(4) {
  animation-delay: 0.4s;
}

.saving-text span:nth-child(5) {
  animation-delay: 0.5s;
}

.saving-text span:nth-child(6) {
  animation-delay: 0.6s;
}

.saving-text span:nth-child(7) {
  animation-delay: 0.7s;
}

@keyframes moveLetters {
  0% {
    transform: translateX(-15vw);
    opacity: 0;
  }

  33.3%, 66% {
    transform: translateX(0);
    opacity: 1;
  }

  100% {
    transform: translateX(15vw);
    opacity: 0;
  }
}

#saving-overlay{
    background-color: #00000069;
    position: fixed; 
    top: 0; 
    left: 0; 
    width: 100%; 
    height: 100%; 
    display: flex; 
    justify-content: center; 
    align-items: center; 
    z-index: 9999;
}
</style>
<div id="saving-overlay" style="display: none;">
  <div class="saving-container">
    <div class="saving-text">
      <span>S</span>
      <span>A</span>
      <span>V</span>
      <span>I</span>
      <span>N</span>
      <span>G</span>
    </div>
  </div>
</div>