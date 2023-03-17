//    function onDisplay(){
//        const btn1 = document.getElementById('home');

//        if(btn1.style.display !== 'none') {
//        btn1.style.display = 'none';
//    }
//        else {
//    btn1.style.display = 'block';
//    }
//    }
//    function offDisplay(){
//        $('#home').hide()''
//    }
//    function onDisplay2(){
//        const btn2 = document.getElementById('house');
//
//        if(btn2.style.display !== 'none') {
//        btn2.style.display = 'none';
//        else {
//    btn1.style.display = 'block';
//    }
//    }
//}
//    function offDisplay2(){
//        $('#house').hide()''
//}

$(document).ready(function () {
  $("#house").hide(); // 초깃값 설정

  $("input[name='contact']").change(function () {
    console.log("input[name='contact']:checked".val);
    if ($("input[name='contact']:checked").val() == "선수") {
      $("#home").hide();
      $("#house").show();
    } else if ($("input[name='contact']:checked").val() == "국가") {
      $("#home").show();
      $("#house").hide();
    }
  });
});
