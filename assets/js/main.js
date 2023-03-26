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
// 메뉴 클릭 이벤트

for (let i = 0; i < gnbItem.length; i++) {
  gnbItem[i].addEventListener("click", () => {
    for (let j = 0; j < gnbItem.length; j++) {
      if (j !== i) {
        gnbItem[j].classList.remove("gnb_active");
        gnbItem[j].classList.remove("gnbBack");
      }
    }
    gnbItem[i].classList.remove("gnb_active");
    gnbItem[i].classList.add("gnbBack");
  });
}

let menu = document.querySelectorAll(".gnbList>li");

for (let i = 0; i < menu.length; i++) {
  let currentMenu
  console.log("1");
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

// 선수 접근 가능 구역
function venue(VenueAll) {
  const venues = document.getElementsByName("venue_code");

  venues.forEach((checkbox) => {
    checkbox.checked = !VenueAll.checked;
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

/* 조 편성 관련 js */
function getKeyByValue(object, value) {
  return Object.keys(object).find((key) => object[key] == value);
}

/**
 * @author 변상원 @1st-award
 * latest_athletes에 선택된 athlete 까지 저장
 * 각 select에 select된 값들을 latest_athletes에서 뺌
 * 선택된건 제거하고 없는건 ORIGIN_LABEL_JSON에서 채워넣음
 * 완성된 OPTION을 모든 input_text select에 복사
 */
function select_change_listener() {
  const origin_dictionary = JSON.parse(ORIGIN_LABEL_JSON);
  const label_dictionary = JSON.parse(ORIGIN_LABEL_JSON);
  const action_list = document.querySelectorAll(".select-box");
  const selected_label_key = [];
  for (let i = 0; i < action_list.length; i++) {
    const index = action_list[i].options.selectedIndex;
    const value = action_list[i].options[index].value;
    selected_label_key.push(value);
  }
  const selected_label_no_duplicated = new Set(selected_label_key);
  for (const index of selected_label_no_duplicated) {
    // 선택된 label 삭제
    delete label_dictionary[getKeyByValue(label_dictionary, index)];
  }
  for (let i = 0; i < action_list.length; i++) {
    const select_object = action_list[i];
    // 추가하기 전에 기존에 있던 option 제거
    for (let j = select_object.options.length - 1; j >= 0; j--) {
      select_object.removeChild(select_object.options[j]);
    }
    // 선택한 option을 불러와서 저장
    const selected_option = document.createElement("option");
    selected_option.selected = true;
    selected_option.value = selected_label_key[i];
    selected_option.innerHTML = getKeyByValue(
      origin_dictionary,
      selected_label_key[i]
    );
    select_object.appendChild(selected_option);
    // hidden에 athlete_id 추가
    // const hidden = action_list[i].parentNode.children[2];
    const hidden = action_list[i].parentNode.parentNode.children[1];
    hidden.value = selected_label_key[i];
    // select 된 option을 제외하고 추가
    for (const [key, value] of Object.entries(label_dictionary)) {
      const option = document.createElement("option");
      option.value = value;
      option.innerHTML = key;
      select_object.appendChild(option);
    }
  }
}
// 조 편성하기 추가 버튼
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
      '<td><div class="copy-value"><select class="select-box select2-hidden-accessible" name="athlete" onchange="select_change_listener()"></select></div></td><input type="hidden" class="hidden-input" id="player_id" name="player_id[]" value="">';
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

  // AD_front_name이 15글자 이상일 경우, 폰트 크기를 10px로 축소하고 카드 크기를 70% 축소합니다.
  if (adFrontName.textContent.length >= 18) {
    adFrontName.style.zoom = "0.5";
    adFrontName.style.lineHeight = "20px";
  }

  if (adBackName.textContent.length >= 38) {
    adBackName.style.zoom = "0.8";
  }
}

// 필드 1개 있을 때 width 100%
const filed_item = document.querySelectorAll(".filed_list_item ");

for (let i = 0; i < filed_item.length; i++) {
  if (filed_item.length == 1) {
    filed_item[i].classList.add("decathlon_container");
  } else {
    filed_item[i].classList.remove("decathlon_container");
  }
}

// 조 편성 수정 > 클릭클릭으로 선수 SWAP 기능
if (
  document.querySelectorAll('.filed2_swap>tbody>tr>td>input[name="name[]"]')
) {
  let clickedInput = null;
  const inputs = document.querySelectorAll(
    '.filed2_swap>tbody>tr>td>input[name="name[]"]'
  );
  const athlete = document.querySelectorAll('input[name="athlete_id[]"]');
  for (let i = 0; i < inputs.length; i++) {
    inputs[i].addEventListener("click", () => {
      if (!clickedInput) {
        clickedInput = inputs[i];
        athleteInput = athlete[i];
      } else {
        const tempValue = clickedInput.value;
        const athleteValue = athleteInput.value;
        clickedInput.value = inputs[i].value;
        athleteInput.value = athlete[i].value;
        inputs[i].value = tempValue;
        athlete[i].value = athleteValue;
        clickedInput.setAttribute("value", clickedInput.value);
        inputs[i].setAttribute("value", inputs[i].value);
        athleteInput.setAttribute("value", athleteInput.value);
        athlete[i].setAttribute("value", athlete[i].value);
        clickedInput = null;
        athleteInput = null;
      }
    });
  }
  document.addEventListener("click", (event) => {
    // 이전에 클릭된 input 요소가 없는 경우에는 실행하지 않음
    // 현재 클릭된 요소가 input 요소가 아닌 경우에 clickedInput 초기화
    if (event.target.name !== "name[]") {
      clickedInput = null;
      athleteInput = null;
    }
  });
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

/**
 * @author 임지훈
 * 조 삭제 여부 확인 함수
 * @returns true/false
 */
function confirmDeleteGroup() {
  return confirm("해당 조를 삭제하시겠습니까?");
}

//@정진호사장님
const buttons = document.querySelectorAll(".menu_button");
buttons.forEach(function (button, index) {
  button.addEventListener("click", function (e) {
    e.preventDefault();
    this.parentNode.classList.toggle("active");
    this.childNodes[1].classList.toggle("fa-angle-right");
    this.childNodes[1].classList.toggle("fa-angle-down");
    buttons.forEach(function (button2, index2) {
      if (index !== index2) {
        button2.parentNode.classList.remove("active");
        button2.childNodes[1].classList.remove("fa-angle-down");
        button2.childNodes[1].classList.add("fa-angle-right");
      }
    });
  });
});

// 전체 선수를 선택 함수
// @vanillacake369
// @box : 전체 선택을 위한 checkbox 입력
function toggle(box) {
  var checkboxes = document.querySelectorAll("input.entry_check");
  for (var i = 0, n = checkboxes.length; i < n; i++) {
    checkboxes[i].checked = box.checked;
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
  pdfPop(entry_who, checkedId, pageURL);
}

function pdfPop(entry_who, checkedId, openURL) {
  // 값 집어 넣기
  let form = createHiddenIdForm("post", "", entry_who, checkedId);
  document.body.appendChild(form);

  //팝업 만들기
  var pop_title = "ADcard_Print";
  createPopupWin("", pop_title, 1100, 900);
  var forms = form;
  forms.target = pop_title;
  forms.action = openURL;
  forms.submit();
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

// 참가자 삭제 함수
// @junwon
// @id_num : 삭제하고자하는 참가자 id
function coach_Delete(id_num) {
  let form = createHiddenIdForm("post", "", "coach_delete", id_num);
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

//심판 삭제 함수
function judge_Delete(id_num) {
  let form = createHiddenIdForm("post", "", "judge_delete", id_num);
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
function schedule_Delete(sports, gender, round) {
  var deleteConfirm = confirm("정말로 삭제하시겠습니까?");
  if (deleteConfirm) {
    let form = document.createElement("form");
    form.setAttribute("method", "post");
    form.setAttribute("action", "");
    let id = document.createElement("input");
    id.setAttribute("type", "hidden");
    id.setAttribute("name", "sports");
    id.setAttribute("value", sports);
    form.appendChild(id);
    let id1 = document.createElement("input");
    id1.setAttribute("type", "hidden");
    id1.setAttribute("name", "gender");
    id1.setAttribute("value", gender);
    form.appendChild(id1);
    let id2 = document.createElement("input");
    id2.setAttribute("type", "hidden");
    id2.setAttribute("name", "round");
    id2.setAttribute("value", round);
    form.appendChild(id2);
    document.body.appendChild(form);
    form.submit();
  }
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

// 계정 생성 또는 수정할 때 권한 읽기 설정 함수
// @Potatoeunbi
function readCheck() {
  const EnRead = document.getElementsByName("authEntrysRead")[0];
  const EnUpdate = document.getElementsByName("authEntrysUpdate")[0];
  const EnDelete = document.getElementsByName("authEntrysDelete")[0];
  const EnCreate = document.getElementsByName("authEntrysCreate")[0];

  const ScRead = document.getElementsByName("authSchedulesRead")[0];
  const ScUpdate = document.getElementsByName("authSchedulesUpdate")[0];
  const ScDelete = document.getElementsByName("authSchedulesDelete")[0];
  const ScCreate = document.getElementsByName("authSchedulesCreate")[0];

  const ReRead = document.getElementsByName("authRecordsRead")[0];
  const ReUpdate = document.getElementsByName("authRecordsUpdate")[0];
  const ReDelete = document.getElementsByName("authRecordsDelete")[0];
  const ReCreate = document.getElementsByName("authRecordsCreate")[0];

  const StRead = document.getElementsByName("authStaticsRead")[0];
  const StUpdate = document.getElementsByName("authStaticsUpdate")[0];
  const StDelete = document.getElementsByName("authStaticsDelete")[0];
  const StCreate = document.getElementsByName("authStaticsCreate")[0];

  const AcRead = document.getElementsByName("authAccountsRead")[0];
  const AcUpdate = document.getElementsByName("authAccountsUpdate")[0];
  const AcDelete = document.getElementsByName("authAccountsDelete")[0];
  const AcCreate = document.getElementsByName("authAccountsCreate")[0];
  if (
    !EnRead.checked &&
    (EnUpdate.checked || EnDelete.checked || EnCreate.checked)
  ) {
    alert("참가자 관리 페이지의 읽기 권한을 체크해야 합니다.");
  } else if (
    !ScRead.checked &&
    (ScUpdate.checked || ScDelete.checked || ScCreate.checked)
  ) {
    alert("경기 관리 페이지의 읽기 권한을 체크해야 합니다.");
  } else if (
    !ReRead.checked &&
    (ReUpdate.checked || ReDelete.checked || ReCreate.checked)
  ) {
    alert("기록 관리 페이지의 읽기 권한을 체크해야 합니다.");
  } else if (
    !StRead.checked &&
    (StUpdate.checked || StDelete.checked || StCreate.checked)
  ) {
    alert("통계 관리 페이지의 읽기 권한을 체크해야 합니다.");
  } else if (
    !AcRead.checked &&
    (AcUpdate.checked || AcDelete.checked || AcCreate.checked)
  ) {
    alert("계정 관리 페이지의 읽기 권한을 체크해야 합니다.");
  } else {
    document.getElementById("authForm").submit();
  }
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

//정말로 삭제할 것인지 물어봄
//@potatoeunbi
//심판이나 관리자는 account로 부탁드립니다요.
function confirmDelete(account, table) {
  var deleteConfirm = confirm("정말로 삭제하시겠습니까?");
  if (deleteConfirm) {
    if (table == "admin") admin_Delete(account);
    else if (table == "schedule") schedule_Delete(account);
    else if (table == "country") country_Delete(account);
    else if (table == "athlete") athlete_Delete(account);
    else if (table == "sports") sport_Delete(account);
    else if (table == "coach") coach_Delete(account);
    else if (table == "director") director_Delete(account);
    else if (table == "judge") judge_Delete(account);
  }
}

if (
  document.querySelectorAll('.filed2_swap>tbody>tr>td>input[name="name[]"]')
) {
  let clickedInput = null;
  const inputs = document.querySelectorAll(
    '.filed2_swap>tbody>tr>td>input[name="name[]"]'
  );
  const athlete = document.querySelectorAll('input[name="athlete_id[]"]');
  for (let i = 0; i < inputs.length; i++) {
    inputs[i].addEventListener("click", () => {
      if (!clickedInput) {
        clickedInput = inputs[i];
        athleteInput = athlete[i];
        console.log(clickedInput);
        console.log(athleteInput);
      } else {
        const tempValue = clickedInput.value;
        const tempId = clickedInput.id;
        const athleteValue = athleteInput.value;
        clickedInput.value = inputs[i].value;
        clickedInput.id = inputs[i].id;
        athleteInput.value = athlete[i].value;
        inputs[i].value = tempValue;
        inputs[i].id = tempId;
        athlete[i].value = athleteValue;
        athlete[i].id = athletepId;
        clickedInput.setAttribute("value", clickedInput.value);
        clickedInput.setAttribute("id", clickedInput.id);
        inputs[i].setAttribute("value", inputs[i].value);
        inputs[i].setAttribute("id", inputs[i].id);
        athleteInput.setAttribute("value", athleteInput.value);
        athlete[i].setAttribute("value", athlete[i].value);
        clickedInput = null;
        athleteInput = null;
      }
    });
  }
  document.addEventListener("click", (event) => {
    // 이전에 클릭된 input 요소가 없는 경우에는 실행하지 않음
    // 현재 클릭된 요소가 input 요소가 아닌 경우에 clickedInput 초기화
    if (event.target.name !== "name[]") {
      clickedInput = null;
      athleteInput = null;
    }
  });
}

//일정 상세정보 접근할 수 없게 하는 함수
//@potatoeunbi
function resultCheck() {
  alert("접근할 수 없습니다.");
}

/**
 * @author 임지훈
 * 조 삭제 여부 확인 함수
 * @returns true/false
 */
function confirmDeleteGroup() {
  return confirm("해당 조를 삭제하시겠습니까?");
}

/**
 * @author 임지훈
 * cursor가 잡혀있는 active browser에 대한 auto refresh 함수
 */
function reloadWhenVisibilityChange() {
  document.addEventListener("visibilitychange", function () {
    if (document.hidden) {
      console.log("Browser tab is hidden");
    } else {
      console.log("Browser tab is visible");
      location.reload();
    }
  });
}

if (document.querySelector(".ID_Print ")) {
  const ID_Print = document.querySelector(".ID_Print ");
  const checklist = document.getElementsByName("checked[]");

  ID_Print.addEventListener("click", () => {
    let checkedCount = 0;
    for (let i = 0; i < checklist.length; i++) {
      if (checklist[i].checked === true) {
        checkedCount++;
      }
    }
    if (checkedCount === 0) {
      alert("하나를 선택해주세요");
    }
  });
}
