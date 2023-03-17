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

const Wrapper = document.querySelector(".Area");
const gnbMask_right = document.querySelector(".gnbMask_right");
const gnbMask = document.querySelector(".gnbMask");
const gnbItem = document.querySelectorAll(".gnbItem");
for (let i = 0; i < gnbItem.length; i++) {
  Wrapper.addEventListener("click", () => {
    gnbItem[i].classList.remove("gnbBack");
  });
  gnbMask_right.addEventListener("click", () => {
    gnbItem[i].classList.remove("gnbBack");
    gnbMask.classList.add("menuhide");
  });
}

var menu = document.querySelectorAll(".gnbList>li");

for (let i = 0; i < menu.length; i++) {
  var currentMenu;

  function inactivate(elem) {
    elem.classList.remove("gnbBack");
  }

  function activate(elem) {
    elem.classList.add("gnbBack");

    currentMenu = elem;
  }
  function clickHandler() {
    // 보통 이벤트 handler 안에 길게 쓰기 보다 함수를 쓴다.
    if (currentMenu) {
      inactivate(currentMenu);
    }
    activate(menu[i]);
  }

  menu[i].addEventListener("click", clickHandler);
}
// 메뉴 햄버거 버튼 이벤트

popupToggle("menuBtn", "gnbMask");
const body = document.querySelector("body");

function popupToggle(toggleBtn, area, closeBtnClass) {
  if (document.querySelector(`.${toggleBtn}`) !== null) {
    const BTN = document.querySelector(`.${toggleBtn}`);
    const AREA = document.querySelector(`.${area}`);

    BTN.addEventListener("click", () => {
      toggleClassList(AREA, "menuhide");
    });

    AREA.addEventListener("click", (e) => {
      if (
        e.target.className === AREA.className ||
        e.target.className === closeBtnClass
      ) {
        toggleClassList(AREA, "menuhide");
      }
    });
  }
}
const menuBtn = document.querySelector(".menuBtn");
if (document.querySelector(".menuBtn") !== null) {
  menuBtn.addEventListener("click", () => {
    addClassList(body, "prevent");
  });
  gnbMask_right.addEventListener("click", () => {
    removeClassList(body, "prevent");
  });
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

init();

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
  const checkboxes = document.getElementsByName("checklist");

  checkboxes.forEach((checkbox) => {
    checkbox.checked = selectAll.checked;
  });
}

// 참가자 관리 경기 목록
const popupItem = document.querySelectorAll(".popup_BTN");
const item_popup = document.querySelectorAll(".item_popup");

for (let i = 0; i < popupItem.length; i++) {
  popupItem[i].addEventListener("click", () => {
    closeAllPopups();
    togglePopup(i);
  });
}

document.addEventListener("click", (event) => {
  const isPopupItem = Array.from(popupItem).some((item) =>
    item.contains(event.target)
  );
  const isPopupContent = Array.from(item_popup).some((item) =>
    item.contains(event.target)
  );
  if (!isPopupItem && !isPopupContent) {
    closeAllPopups();
  }
});

function togglePopup(index) {
  if (item_popup[index].style.display === "none") {
    item_popup[index].style.display = "block";
  } else {
    item_popup[index].style.display = "none";
  }
}

function closeAllPopups() {
  for (let i = 0; i < item_popup.length; i++) {
    item_popup[i].style.display = "none";
  }
}

// 조 편성하기 js 오류 수정
let numbers;

const addColumnBtns = document.querySelectorAll(".add-column-btn");

for (let i = 0; i < addColumnBtns.length; i++) {
  addColumnBtns[i].addEventListener("click", () => {
    const filedTables = document.querySelectorAll(".filed2_Table");
    const currentTable = filedTables[i];
    const rowsCount = currentTable.rows.length - 1;

    const newRow = currentTable.insertRow();
    const Cell1 = newRow.insertCell(0);
    const Cell2 = newRow.insertCell(1);

    Cell1.innerHTML = `<td><input type="text" class="number" value="${rowsCount}" name="lane[]" disabled></td>`;
    Cell2.innerHTML =
      '<td><div class="copy-value"><select name="playerid[]"></select></div></td>';

    // option 복제
    const options = $("#copy-value select option").clone();
    const select = $(Cell2).find("select");
    options.appendTo(select);

    // select2 적용
    select.select2({
      dropdownParent: $(Cell2).find(".copy-value"),
    });
  });
}

// 조 편성하기 삭제 버튼
function deleteColumn(index) {
  const filedTables = document.querySelectorAll(".filed2_Table");
  const currentTable = filedTables[index];

  const tableRows = currentTable.querySelectorAll("tbody tr");
  if (tableRows.length === 1) {
    return; // 행이 1개일 때는 삭제하지 않음
  }

  currentTable.deleteRow(currentTable.rows.length - 1); // 마지막 행 삭제
}

const deleteColumnBtns = document.querySelectorAll(".delete-column-btn");
for (let i = 0; i < deleteColumnBtns.length; i++) {
  deleteColumnBtns[i].addEventListener("click", () => {
    deleteColumn(i);
  });
}

// ad카드 부분
if (document.querySelector(".AD_front_name") !== null) {
  const adFrontName = document.querySelector(".AD_front_name p");
  const adBackName = document.querySelector(".AD_back_name p");
  const BackName = document.querySelector(".AD_back_name");
  const FrontName = document.querySelector(".AD_front_name");

  // AD_front_name이 15글자 이상일 경우, 폰트 크기를 10px로 축소하고 카드 크기를 70% 축소합니다.
  if (adFrontName.textContent.length >= 15) {
    adFrontName.style.zoom = "0.6";
  }

  if (adBackName.textContent.length >= 38) {
    adBackName.style.zoom = "0.6";
  }
}

// 화면 정중앙에 팝업 함수
// @vanillacake369
// @pageURL : 팝업 url 주소
// @pageTitle : 팝업 제목
// @popupWinWidth : 팝업창 가로
// @popupWinHeight : 팝업창 세로
function createPopupWin(pageURL, pageTitle, popupWinWidth, popupWinHeight) {
  var left = (screen.width - popupWinWidth) / 2;
  var top = (screen.height - popupWinHeight) / 4;

  var myWindow = window.open(
    pageURL,
    pageTitle,
    "resizable=yes, width=" +
      popupWinWidth +
      ", height=" +
      popupWinHeight +
      ", top=" +
      top +
      ", left=" +
      left
  );
}

// 참가자 ID 사용 팝업함수
// @junwon, @vanillacake369
// @id : 코치의 id값
// @openURL : form에 대해 open 될 view 담당 페이지 URL
function updatePop(id, entry_who, openURL) {
  // 값 집어 넣기
  let form = createHiddenIdForm("post", "", entry_who, id);
  document.body.appendChild(form);

  //팝업 만들기
  var pop_title = id;
  createPopupWin("", pop_title, 1100, 900);
  var forms = form;
  forms.target = pop_title;
  forms.action = openURL;
  forms.submit();
}

// 참가자 삭제 함수
// @junwon
// @id_num : 삭제하고자하는 참가자 id
function coach_Delete(id_num) {
  let form = createHiddenIdForm("post", "", "coach_delete", id_num);
  document.body.appendChild(form);
  form.submit();
}

function judge_Delete(id_num) {
  let form = createHiddenIdForm("post", "", "judge_delete", id_num);
  document.body.appendChild(form);
  form.submit();
}

function athlete_Delete(id_num) {
  let form = createHiddenIdForm("post", "", "athlete_delete", id_num);
  document.body.appendChild(form);
  form.submit();
}

function director_Delete(id_num) {
  let form = createHiddenIdForm("post", "", "director_delete", id_num);
  document.body.appendChild(form);
  form.submit();
}

//관리자 삭제 함수
// @potatoeunbi
function admin_Delete(account) {
  let form = createHiddenIdForm("post", "", "admin_delete", account);
  document.body.appendChild(form);
  form.submit();
}

//일정 삭제 함수
// @potatoeunbi
function schedule_Delete(id_num) {
  let form = createHiddenIdForm("post", "", "schedule_delete", id_num);
  document.body.appendChild(form);
  form.submit();
}

//국가 삭제 함수!!!
function country_Delete(country) {
  let form = createHiddenIdForm("post", "", "country_delete", country);
  document.body.appendChild(form);
  form.submit();
}

//경기 삭제 함수!!!
function sport_Delete(country) {
  let form = createHiddenIdForm("post", "", "sports_delete", country);
  document.body.appendChild(form);
  form.submit();
}

//정말로 삭제할 것인지 물어봄
//@potatoeunbi
//심판이나 관리자는 account로 부탁드립니다요.
function confirmDelete(account, table) {
  var deleteConfirm = confirm("정말로 삭제하시겠습니까?");
  if (deleteConfirm) {
    if (table == "admin") admin_Delete(account);
    else if (table == "schedule") schedule_Delete(account);
    else if (table == "country") country_Delete(account);
    else if (table == "judge") judge_Delete(account);
    else if (table == "athlete") athlete_Delete(account);
    else if (table == "sports") sport_Delete(account);
    else if (table == "coach") coach_Delete(account);
    else if (table == "director") director_Delete(account);
  }
}

// 참가자 ID 사용 팝업함수
// @junwon, @vanillacake369
// @id : 코치의 id값
// @openURL : form에 대해 open 될 view 담당 페이지 URL
function updatePop(id, entry_who, openURL) {
  // 값 집어 넣기
  let form = createHiddenIdForm("post", "", entry_who, id);
  document.body.appendChild(form);

  //팝업 만들기
  var pop_title = id;
  createPopupWin("", pop_title, 1100, 900);
  var forms = form;
  forms.target = pop_title;
  forms.action = openURL;
  forms.submit();
}

// @vanillacake369
/**
 * 참가자 ID 발급 함수
 * @param {*} pageURL : ID 발급 URL
 * @param {*} entry_who : POST 시 전송할 참가자ID칼럼에 대한 id text
 */
function issueId(pageURL, entry_who) {
  // 체크된 참가자 id 값 가져오기 (input > value태그)
  var checkboxes = document.querySelectorAll("input.entry_check");
  var checkedId = [];
  for (var i = 0, n = checkboxes.length; i < n; i++) {
    if (checkboxes[i].checked === true) {
      checkedId.push(checkboxes[i].value);
    }
  }
  checkedId.forEach((entry) => updatePop(entry, entry_who, pageURL));
}

// 전체 선수를 선택 함수
// @vanillacake369
// @box : 전체 선택을 위한 checkbox 입력
function toggle(box) {
  var checkboxes = document.querySelectorAll("input.entry_check");
  for (var i = 0, n = checkboxes.length; i < n; i++) {
    checkboxes[i].checked = box.checked;
  }
}

// @vanillacake369
/**
 * id를 사용한 hidden form 만드는 함수
 *
 * @param {*} method : post인지, get인지
 * @param {*} name : id의 name
 * @param {*} value : id의 value
 * @returns 생성한 form 반환
 */
function createHiddenIdForm(method, action, name, value) {
  let form = document.createElement("form");
  form.setAttribute("method", method);
  form.setAttribute("action", action);
  let id = document.createElement("input");
  id.setAttribute("type", "hidden");
  id.setAttribute("name", name);
  id.setAttribute("value", value);
  form.appendChild(id);
  return form;
}

// @vanillacake369
/**
 * Set Select Box Selection By Value
 * @param eid Element ID
 * @param evalue Element Index
 */
function setSelectBoxByValue(eid, evalue) {
  var eid = document.getElementById(eid);
  for (var i = 0; i < eid.options.length; ++i) {
    if (eid.options[i].value === evalue) eid.options[i].selected = true;
  }
}

for (let i = 0; i < item_popup.length; i++) {
  item_popup[i].addEventListener("click", () => {
    item_popup[i].style.display = "none";
  });
}

//영문 아니면 입력 안 되게 하는 함수
//@JoEun1
function handleOnInput(e) {
  e.value = e.value.replace(/[^A-Za-z]/gi, "");
}

function checkNumber(event) {
  if (
    event.key === "." ||
    event.key === "-" ||
    (event.key >= 0 && event.key <= 9)
  ) {
    return true;
  }

  return false;
}

// 페이지 크기 변경 함수
// @vanillacake369
// @pageSize : 변경하고자하는 페이지의 크기
function changeTableSize(pageSize) {
  let form = createHiddenIdForm("get", "", "page_size", pageSize.value);
  const searchParams = new URLSearchParams(location.search);
  for (const param of searchParams) {
    if (param[0] != "page_size") {
      let selectSize = document.createElement("input");
      selectSize.setAttribute("type", "hidden");
      selectSize.setAttribute("name", param[0]);
      selectSize.setAttribute("value", param[1]);
      form.appendChild(selectSize);
    }
  }
  document.body.appendChild(form);
  form.submit();
}
