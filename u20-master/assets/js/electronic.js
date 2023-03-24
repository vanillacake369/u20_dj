for (let i = 30; i < 100; i++) {
  document.querySelector('.rankingBox').innerHTML += `
<li class="rankingItem">
  <div class="rankingItemWrapper">
    <span class="wrapperLank">${i + 1}</span>
    <span>Woo Sang Hyeok</span>
    <span><span class="CR">CR</span> 11.48</span>
    <div style="background: url(assets/images/u20_national_flag/korea.png) 0% 0% / cover no-repeat;"></div>
  </div>
</li>
`
};

const electronicDisplay = document.querySelector('.container');
const rankingDisplay = document.querySelector('.rankingBox');
const title = document.querySelector(".title")
const titleDisplay = document.querySelector('.titleBox');
const containerImg = document.querySelector(".containerImg");
const rankingItem = document.querySelectorAll(".rankingItem");
const rankingItemWrapper = document.querySelectorAll('.rankingItemWrapper');
const wrapperLank = document.querySelectorAll(".wrapperLank");

const defaultDisplay = "container";
const defaultTitleDisplay = "titleBox";
const defaultRankingItem = "rankingItem"
const defaultRankingItemWrapper = "rankingItemWrapper";
const fadeIn = "container animate__animated animate__fadeInLeft animate__delay-01s animate__fast";
const fadeOut = "container animate__animated animate__fadeOutRight animate__delay-01s animate__fast";
const titleFadeIn = "container animate__animated animate__fadeIn animate__delay-01s animate__fast"
const titleFadeOut = "container animate__animated animate__fadeOut animate__delay-01s animate__fast"
const fadeInUp = "rankingItemWrapper animate__animated animate__fadeInUp animate__delay-01s animate__faster";
const fadeOutUp = "rankingItemWrapper animate__animated animate__fadeOutUp animate__delay-01s animate__faster";
const fadeInUp2 = "rankingItem animate__animated animate__fadeInUp animate__delay-01s animate__faster";
const fadeOutUp2 = "rankingItem animate__animated animate__fadeOutUp animate__delay-01s animate__faster";

let j = 0;
const MAX_LENGTH = rankingItem.length;
const widthMargin = document.body.clientWidth - 1890;
const heightMargin = document.body.clientHeight - containerImg.clientHeight;
electronicDisplay.style.transform = `translate(${widthMargin / 2}px, 0)`

class RankingTable {
  static responsiveTitleSize() { if (title.clientWidth > 890) { title.style.fontSize = "68px"; title.style.lineHeight = "1em"; }; };
  static animationShowDisplay(key) { document.addEventListener("keydown", (e) => { if (e.code === `Key${key.toUpperCase()}`) { electronicDisplay.className = fadeIn; electronicDisplay.style.display = "block"; }; this.responsiveTitleSize(); }); };
  static animationHideDisplay(key) { document.addEventListener("keydown", (e) => { if (e.code === `Key${key.toUpperCase()}`) { electronicDisplay.className = fadeOut; electronicDisplay.style.display = "block"; }; this.responsiveTitleSize(); }); };
  static nonAnimationShowDisplay(key) { document.addEventListener("keydown", (e) => { if (e.code === `Key${key.toUpperCase()}`) { electronicDisplay.className = defaultDisplay; electronicDisplay.style.display = "block"; }; this.responsiveTitleSize(); }); };
  static nonAnimationHideDisplay(key) { document.addEventListener("keydown", (e) => { if (e.code === `Key${key.toUpperCase()}`) { electronicDisplay.className = defaultDisplay; electronicDisplay.style.display = "none"; }; this.responsiveTitleSize(); }); };
  static animationShowAllRank(key) { document.addEventListener("keydown", (e) => { if (e.code === `Key${key.toUpperCase()}`) { for (let i = 0; i < rankingItemWrapper.length; i++) { rankingItem[i].className = fadeInUp2; rankingItem[i].style.display = "block"; rankingItemWrapper[i].className = fadeInUp; rankingItemWrapper[i].style.display = "flex"; }; }; }); };
  static nonAnimationShowAllRank(key) { document.addEventListener("keydown", (e) => { if (e.code === `Key${key.toUpperCase()}`) { for (let i = 0; i < rankingItemWrapper.length; i++) { rankingItem[i].className = defaultRankingItem; rankingItem[i].style.display = "block"; rankingItemWrapper[i].className = defaultRankingItemWrapper; rankingItemWrapper[i].style.display = "flex"; }; }; }); };
  static animationHideAllRank(key) { document.addEventListener("keydown", (e) => { if (e.code === `Key${key.toUpperCase()}`) { for (let i = 0; i < rankingItemWrapper.length; i++) { rankingItem[i].className = fadeOutUp2; rankingItemWrapper[i].className = fadeOutUp; }; for (let i = 0; i < rankingItemWrapper.length; i++) { setTimeout(() => { rankingItem[i].style.display = "none"; rankingItemWrapper[i].style.display = "none"; }, 400); }; }; }); };
  static nonAnimationHideAllRank(key) { document.addEventListener("keydown", (e) => { if (e.code === `Key${key.toUpperCase()}`) { for (let i = 0; i < rankingItemWrapper.length; i++) { rankingItem[i].className = defaultRankingItem; rankingItemWrapper[i].className = defaultRankingItemWrapper; rankingItem[i].style.display = "none"; rankingItemWrapper[i].style.display = "none"; }; }; }); };
  static animationShowPrevRank(key) { document.addEventListener("keydown", (e) => { if (e.code === `Key${key.toUpperCase()}`) { for (let i = 0; i < rankingItemWrapper.length; i++) { rankingItem[i].className = defaultRankingItem; rankingItem[i].style.display = "none"; rankingItemWrapper[i].className = defaultRankingItemWrapper; rankingItemWrapper[i].style.display = "none"; }; j < 8 ? j = 0 : j = j - 8; let NUM = j; let count = 0; function print() { if (wrapperLank[NUM].innerText === "") { count = 8; } else { rankingItem[NUM].className = fadeInUp2; rankingItem[NUM].style.display = "block"; rankingItemWrapper[NUM].style.display = "flex"; NUM++; count++; }; }; let interval = setInterval(() => { document.addEventListener("keydown", () => { count = 8; }); if (count === 8) { clearInterval(interval); } else { print(j); }; }, 600); }; }); };
  static animationShowNextRank(key) { document.addEventListener("keydown", (e) => { if (e.code === `Key${key.toUpperCase()}`) { for (let i = 0; i < rankingItemWrapper.length; i++) { rankingItem[i].className = defaultRankingItem; rankingItem[i].style.display = "none"; rankingItemWrapper[i].className = defaultRankingItemWrapper; rankingItemWrapper[i].style.display = "none"; }; j + 8 < MAX_LENGTH ? j = j + 8 : j = j; let NUM = j; let count = 0; function print() { if (wrapperLank[NUM].innerText === "") { count = 8; } else { rankingItem[NUM].className = fadeInUp2; rankingItem[NUM].style.display = "block"; rankingItemWrapper[NUM].style.display = "flex"; NUM++; count++; }; }; let interval = setInterval(() => { document.addEventListener("keydown", () => { count = 8; }); if (count === 8 || NUM === MAX_LENGTH) { clearInterval(interval); } else { print(); }; }, 600); }; }); };
  static nonAnimationShowNextRank(key) { document.addEventListener("keydown", (e) => { if (e.code === `Key${key.toUpperCase()}`) { for (let i = 0; i < rankingItemWrapper.length; i++) { rankingItem[i].className = defaultRankingItem; rankingItem[i].style.display = "none"; rankingItemWrapper[i].className = defaultRankingItemWrapper; rankingItemWrapper[i].style.display = "none"; }; j + 8 < MAX_LENGTH ? j = j + 8 : j = j; let NUM = j; let count = 0; function print() { if (wrapperLank[NUM].innerText === "") { count = 8; } else { rankingItem[NUM].className = defaultRankingItem; rankingItem[NUM].style.display = "block"; rankingItemWrapper[NUM].style.display = "flex"; NUM++; count++; }; }; let interval = setInterval(() => { document.addEventListener("keydown", () => { count = 8; }); if (count === 8 || NUM === MAX_LENGTH) { clearInterval(interval); } else { print(); }; }, 0); }; }); };
  static nonAnimationShowPrevRank(key) { document.addEventListener("keydown", (e) => { if (e.code === `Key${key.toUpperCase()}`) { for (let i = 0; i < rankingItemWrapper.length; i++) { rankingItem[i].className = defaultRankingItem; rankingItem[i].style.display = "none"; rankingItemWrapper[i].className = defaultRankingItemWrapper; rankingItemWrapper[i].style.display = "none"; }; j < 8 ? j = 0 : j = j - 8; let NUM = j; let count = 0; function print() { if (wrapperLank[NUM].innerText === "") { count = 8; } else { rankingItem[NUM].className = defaultRankingItem; rankingItem[NUM].style.display = "block"; rankingItemWrapper[NUM].style.display = "flex"; NUM++; count++; }; }; let interval = setInterval(() => { document.addEventListener("keydown", () => { count = 8; }); if (count === 8) { clearInterval(interval); } else { print(j); }; }, 0); }; }); };
};

function animation() {
  RankingTable.animationShowDisplay("q");
  RankingTable.animationHideDisplay("w");
  RankingTable.animationShowAllRank("z");
  RankingTable.animationHideAllRank("x");
  RankingTable.animationShowPrevRank("c");
  RankingTable.animationShowNextRank("v");
};

function nonAnimation() {
  RankingTable.nonAnimationShowDisplay("q");
  RankingTable.nonAnimationHideDisplay("w");
  RankingTable.nonAnimationShowAllRank("z");
  RankingTable.nonAnimationHideAllRank("x");
  RankingTable.nonAnimationShowPrevRank("c");
  RankingTable.nonAnimationShowNextRank("v");
};

animation();
// nonAnimation();