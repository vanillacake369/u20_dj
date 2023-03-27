/**
 * 인자에 DOM 요소와 클레스명을 전달하면 해당 DOM 요소에 클레스명을 첨삭(더하거나 빼거나)해줌
 * @param {document} target 클레스명을 변경하고자 하는 DOM 요소
 * @param {string} className 첨삭하고자 하는 클레스명
 */
function toggleClassList(target, className) {
  target.classList.toggle(className);
}

/**
 * 인자에 DOM 요소와 클레스명을 전달하면 해당 DOM 요소에 클레스명을 더해줌 
 * @param {document} target 클레스명을 변경하고자 하는 DOM 요소
 * @param {string} className 더하고자 하는 클레스명
 */
function addClassList(target, className) {
  target.classList.add(className);
}

/**
 * 인자에 DOM 요소와 클레스명을 전달하면 해당 DOM 요소에 클레스명을 더해줌
 * @param {document} target 클레스명을 변경하고자 하는 DOM 요소
 * @param {string} className 더하고자 하는 클레스명
 */


function removeClassList(target, className) {
  target.classList.remove(className);
}

// 메뉴 이벤트

const Wrapper = document.querySelector('.Area')
const gnbMask_right = document.querySelector('.gnbMask_right')
const gnbMask = document.querySelector('.gnbMask')
const gnbItem = document.querySelectorAll('.gnbItem')
for (let i = 0; i < gnbItem.length; i++) {
  Wrapper.addEventListener('click', () => {
    gnbItem[i].classList.remove('gnbBack')
  })
  gnbMask_right.addEventListener('click', () => {
    gnbItem[i].classList.remove('gnbBack')
    gnbMask.classList.add('menuhide');
  })

}


var menu = document.querySelectorAll('.gnbList>li');

for (let i = 0; i < menu.length; i++) {
  var currentMenu

  function inactivate(elem) {
    elem.classList.remove('gnbBack');
  }

  function activate(elem) {
    elem.classList.add('gnbBack');

    currentMenu = elem;
  }
  function clickHandler() {    // 보통 이벤트 handler 안에 길게 쓰기 보다 함수를 쓴다.
    if (currentMenu) {
      inactivate(currentMenu);
    }
    activate(menu[i]);
  }

  menu[i].addEventListener('click', clickHandler);
}
// 메뉴 햄버거 버튼 이벤트

popupToggle('menuBtn', 'gnbMask');

function popupToggle(toggleBtn, area, closeBtnClass) {
  if (document.querySelector(`.${toggleBtn}`) !== null) {
    const BTN = document.querySelector(`.${toggleBtn}`)
    const AREA = document.querySelector(`.${area}`)

    BTN.addEventListener('click', () => {
      toggleClassList(AREA, 'menuhide')
    });


    AREA.addEventListener('click', (e) => {
      if (e.target.className === AREA.className || e.target.className === closeBtnClass) {
        toggleClassList(AREA, 'menuhide');
      };
    });
  };
}


// 페이지 네이션 js

const number = document.getElementsByClassName("page_link");

const player = document.getElementsByClassName("player_page");



function handleClick(event) {
  for (let i = 0; i < number.length; i++) {
    number[i].classList.remove("clicked");
  }
  event.target.classList.add("clicked");
}

function playerPage(event) {
  for (let i = 0; i < player.length; i++) {
    player[i].classList.remove("click");
  }
  event.target.classList.add("click");
}


function init() {
  for (let i = 0; i < number.length; i++) {
    number[i].addEventListener("click", handleClick);
  }
  for (let i = 0; i < player.length; i++) {
    player[i].addEventListener("click", playerPage);
  }
}


init()


//창 던지기 input value
function field1Format(obj) {
  if ((obj.value == "-" || obj.value == "X") && obj.value.length == 1) {
    // 실패한 경우
    rankcal();
  } else {
    obj.value = comma(uncomma(obj.value));
    fieldFinal(obj);
    rankcal();
  }
}
//등수 자동 배정 내림차순
function rankcal() {
  let re = document.querySelectorAll("#result"); //결과 요소 가져옴
  let ran = document.querySelectorAll("#rank"); //둥수 요소가져옴
  let arr1 = {};
  for (i = 0; i < re.length; i++) {
    let k = i;
    arr1[k] = re[i].value; //객체에 결과 저장
  }
  let keysSorted = Object.keys(arr1).sort(function (a, b) {
    return arr1[b] - arr1[a];
  }); //정렬
  for (i = 0; i < ran.length; i++) {
    ran[keysSorted[i]].value = i + 1; //등수대로 기입
  }
}

function fieldFinal(obj) {
  let top = "0";
  for (i = 3; i < 9; i++) {
    if (
      parseFloat(top) <
      parseFloat(
        obj.parentElement.parentElement.children[i].firstElementChild.value
      )
    ) {
      top = obj.parentElement.parentElement.children[i].firstElementChild.value;
    }
  }
  obj.parentElement.parentElement.children[9].firstElementChild.value = top;
}

// 콤마를 찍기위한부분
function comma(str) {
  str = String(str);
  if (str.length < 5) {
    return str.replace(/(\B)(?=(?:\d{2})+(?!\d))/g, "$1.");
  } else {
    return str.replace(/(\d+)(\d{2})(\d{2})/g, "$1:$2.$3");
  }
}

function uncomma(str) {
  str = String(str);
  return str.replace(/[^\d]+/g, "");
}

function tftf(str) {
  str = String(str);
  return str.toUpperCase().replace(/[^-OX]+/g, "");
}
//바람전용
function uncomma1(str) {
  str = String(str);
  return str.replace(/[^-\d]+/g, "");
}
//실격횟수 카운트
function counting(str) {
  str = String(str);
  return (str.match(/X/g) || []).length;
}


if (document.querySelector("#js-scroll") !== null) {
  document.querySelector("#js-scroll").addEventListener("click", (e) => {
    e.preventDefault();
    window.scroll({
      top: 0,
      left: 0,
    });
  });

}

// 체크박스

function selectAll(selectAll) {
  const checkboxes
    = document.getElementsByName('checklist');

  checkboxes.forEach((checkbox) => {
    checkbox.checked = selectAll.checked;
  })
}