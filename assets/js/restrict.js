//특수문자에 대한 자동 필터링 기능
function characterCheck(obj) {
  var regExp = /[ \{\}\[\]\/?.,;:|\)*~`!^\-_+┼<>@\#$%&\'\"\\\(\=]/gi; // 허용할 특수문자 설정

  // 띄어쓰기도 특수문자 처리됨
  if (regExp.test(obj.value)) {
    alert("특수문자는 입력하실수 없습니다."); //안내 문구 출력
    obj.value = obj.value.substring(0, obj.value.length - 1); // 입력한 특수문자 한자리 지움
  }
}

//숫자 입력 2자리로 제한
function maxLengthCheck(object) {
  if (object.value.length > object.maxLength) {
    object.value = object.value.slice(0, object.maxLength);
  }
}

// 경기 결과 숫자만 입력 및 자동 양식 스크립트
function inputNumberFormat(obj) {
  obj.style.textAlign = "right";
  obj.style.width = "35px";
  if (obj.value.length == 0) {
    obj.style.textAlign = "left";
    obj.style.width = "auto";
  }

  if (obj.value.length == obj.maxLength) {
    obj.nextElementSibling.nextElementSibling.focus();
    //   console.log(obj.nextElementSibling)
  } else {
    obj.value = comma(uncomma(obj.value));
  }
}

function inputNumberFormat2(obj) {
  if (obj.value.length > 0) {
    obj.style.textAlign = "right";
    obj.style.width = "35px";
  } else {
    obj.style.textAlign = "left";
    obj.style.width = "auto";
  }
  obj.value = comma(uncomma(obj.value));
}

function comma(str) {
  str = String(str);
  return str.replace(/(\d)(?=(?:\d{3})+(?!\d))/g, "$1.");
}

function uncomma(str) {
  str = String(str);
  return str.replace(/[^\d]+/g, "");
}

function openTextFile() {
  var input = document.createElement("input");
  input.type = "file";
  input.accept = "text/plain"; // 확장자가 xxx, yyy 일때, ".xxx, .yyy"
  input.onchange = function (event) {
    processFile(event.target.files[0]);
  };
  input.click();
}

function processFile(file) {
  var reader = new FileReader();
  reader.onload = function () {
    let ddd = reader.result.split("\r\n");
    for (i = 0; i < ddd.length; i++) {
      let k = ddd[i].split(" ");
      let on = document.querySelector("#" + k[1]).children;
      on[1].firstChild.value = k[0].slice(0, 1);
      on[4].firstChild.value = k[2];
    }
  };
  reader.readAsText(file, /* optional */ "utf-8");
}

// 태블릿 심판 유무 스크립트
function isUsingTablet() {
  // Get the checkbox
  var checkBox = document.getElementById("is_using_tablet");
  // Get the tag
  var judge_id = document.getElementById("user_id");
  var judge_password = document.getElementById("use_pw");
  var cpassword = document.getElementById("use_pw_check");
  var check_message = document.getElementById("message");
  // If the checkbox is checked, display the tag
  if (checkBox.checked == true) {
    if (judge_id) {
      judge_id.style.display = "inherit";
    }
    judge_password.style.display = "inherit";
    cpassword.style.display = "inherit";
    check_message.style.display = "inherit";
  } else {
    if (judge_id) {
      judge_id.style.display = "none";
    }
    judge_password.style.display = "none";
    cpassword.style.display = "none";
    check_message.style.display = "none";
  }
}

// 심판 패스워드 확인 스크립트
const check = function () {
  if (
    document.getElementById("judge_password").value ==
    document.getElementById("cpassword").value
  ) {
    document.getElementById("message").style.color = "green";
    document.getElementById("message").innerHTML = "비밀번호가 같습니다.";
  } else {
    document.getElementById("message").style.color = "red";
    document.getElementById("message").innerHTML = "비밀번호가 다릅니다.";
  }
};
