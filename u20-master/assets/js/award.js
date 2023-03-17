const awardCeremony = document.querySelector(".awardCeremony")
const first = document.querySelector(".first");
const second = document.querySelector(".second");
const third = document.querySelector(".third");
const bannerContainer = document.querySelector(".banner-container")

const defaultBannerContainer = "banner-container"
const defaultAwardCeremony = "awardCeremony";
const defaultFirst = "flag-container first";
const defaultSecond = "flag-container second";
const defaultThird = "flag-container third";

const fadeIn = "animate__animated animate__fadeIn animate__slow"
const fadeOut = "animate__animated animate__fadeOut animate__slow"
const slideInDown = "animate__animated animate__fadeInDown animate__delay-01s animate__slower";
const slideOutDown = "animate__animated animate__fadeOutDown animate__delay-01s animate__slow";

document.addEventListener("keydown", (e) => {
  if (e.code === "KeyQ") {
    bannerContainer.className = defaultBannerContainer + " " + fadeIn;
    bannerContainer.style.opacity = 1;
  } else if (e.code === "KeyW") {
    bannerContainer.className = defaultBannerContainer + " " + fadeOut;
    setTimeout(() => {
      bannerContainer.style.opacity = 0;
    }, 1000);
  } else if (e.code === "KeyA") {
    first.className = defaultFirst + " " + slideInDown;
    second.className = defaultSecond + " " + slideInDown;
    third.className = defaultThird + " " + slideInDown;
    first.style.opacity = 1;
    second.style.opacity = 1;
    third.style.opacity = 1;
  } else if (e.code === "KeyS") {
    first.className = defaultFirst + " " + slideOutDown;
    second.className = defaultSecond + " " + slideOutDown;
    third.className = defaultThird + " " + slideOutDown;
    setTimeout(() => {
      first.style.opacity = 0;
      second.style.opacity = 0;
      third.style.opacity = 0;
    }, 1000);
  } else if (e.code === "KeyD") {
    first.className = defaultFirst;
    second.className = defaultSecond;
    third.className = defaultThird;
    first.style.opacity = 1;
    second.style.opacity = 1;
    third.style.opacity = 1;
  } else if (e.code === "KeyF") {
    first.className = defaultFirst;
    second.className = defaultSecond;
    third.className = defaultThird;
    first.style.opacity = 0;
    second.style.opacity = 0;
    third.style.opacity = 0;
  } else if (e.code === "KeyZ") {
    bannerContainer.className = defaultBannerContainer + " " + fadeIn;
    bannerContainer.style.opacity = 1;
    first.className = defaultFirst + " " + slideInDown;
    second.className = defaultSecond + " " + slideInDown;
    third.className = defaultThird + " " + slideInDown;
    first.style.opacity = 1;
    second.style.opacity = 1;
    third.style.opacity = 1;
  } else if (e.code === "KeyX") {
    bannerContainer.className = defaultBannerContainer + " " + fadeOut;
    first.className = defaultFirst + " " + slideOutDown;
    second.className = defaultSecond + " " + slideOutDown;
    third.className = defaultThird + " " + slideOutDown;
    setTimeout(() => {
      bannerContainer.style.opacity = 0;
      first.style.opacity = 0;
      second.style.opacity = 0;
      third.style.opacity = 0;
    }, 1000);
  }
})