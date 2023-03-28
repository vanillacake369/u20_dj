//트랙 포맷 :: xx.xx / xx.xx(xxx)
function trackResultForm(obj) {
  obj.value = regTrackResult(uncomma(obj.value));
  rankcal1();
}

function regTrackResult(str) {
  str = String(str);
  if (str.length <= 4) {
    return str.replace(/(\d{2})(\d{2})/g, "$1.$2");
  } else{
    return str.replace(/(\d{2})(\d{2})(\d{2,})/g, "$1:$2.$3");
  } 
//   else if (str.length > 6) {
//     return str.replace(/(\d{2})\.?(\d{2})\(?(\d{3})\)?/, "$1.$2($3)");
//   }
}

// 트랙 Reaction Time 포맷 :: 0.xxx
function trackReactionTimeForm(obj) {
  obj.value = regTrackTime(uncomma(obj.value));
  rankcal1();
}

function regTrackTime(str) {
  str = String(str);
  return str.replace(/^0*(\d{1,6})$/, "0.$1");
}

// 경기 결과 숫자만 입력 및 자동 양식 스크립트
//트랙경기 포맷
function trackFinal(obj) {
  obj.value = comma(uncomma(obj.value));
  rankcal1();
}

//필드경기 포맷
function field1Format(obj) {
  if (
    (obj.value == "-" || obj.value == "X" || obj.value == "x") &&
    obj.value.length == 1
  ) {
    // 실패한 경우
    obj.value = String(obj.value);
    obj.value = obj.value.toUpperCase();
    rankcal();
  } else {
    obj.value = comma(uncomma(obj.value));
    fieldFinal(obj);
    rankcal();
  }
}

//멀리뛰기,삼단뛰기 전용 포맷
function field2Format(obj) {
  if (
    (obj.value == "-" || obj.value == "X" || obj.value == "x") &&
    obj.value.length == 1
  ) {
    // 실패한 경우
    obj.value = String(obj.value);
    obj.value = obj.value.toUpperCase();
    rankcal4();
  } else {
    obj.value = comma(uncomma(obj.value));
    fieldFinal2(obj);
    rankcal4();
  }
}

//필드 바람
function windFormat(obj) {
  obj.value = comma1(uncomma1(obj.value));
  fieldFinal3(obj);
  rankcal4();
}

//높이뛰기 높이 전용
function heightFormat(obj) {
  obj.value = comma(uncomma(obj.value));
}

function highFormat(obj) {
  obj.value = tftf(obj.value);
  const rain = obj.parentElement.parentElement.className.split("_")[1];
  //성공시 처리 부분
  let high = document.querySelectorAll('[name="trial[]"]'); // 높이 배열 가져오기
  let index = document.querySelectorAll("#result");
  let calcal = 0.0;
  for (i = 1; i <= 24; i++) {
    let k = '[name="gameresult' + i + '[]"]';
    let temp = document.querySelectorAll(k)[rain - 1].value;
    if (temp.search("O") != -1) {
      if (
        calcal < parseFloat(high[i - 1].value) ||
        isNaN(parseFloat(index[rain - 1].value))
      ) {
        calcal = parseFloat(high[i - 1].value);
      }
    }
  }
  index[rain - 1].value = calcal; // 기존 값과 비교 후 성공 시 기록이 크면 바꾸기
  rankcal2();
}

function comma(str) {
  str = String(str);
  if (str.length <= 5) {
    return str.replace(/(\B)(?=(?:\d{3})+(?!\d))/g, "$1.");
  } else {
    return str.replace(/(\d+)(\d{2})(\d{3})/g, "$1:$2.$3");
  }
}

function uncomma(str) {
  str = String(str);
  return str.replace(/[^\d]+/g, "");
}

function tftf(str) {
  str = String(str);
  str = str.toUpperCase();
  if (str.indexOf("-") >= 0) {
    if (str.indexOf("-") > str.indexOf("O") && str.indexOf("O") >= 0) {
      str = str.substring(0, str.indexOf("O") + 1);
    } else {
      str = str.substring(0, str.indexOf("-") + 1);
    }
  } else if (str.indexOf("O") >= 0) {
    str = str.substring(0, str.indexOf("O") + 1);
  }
  return str.replace(/[^-OX]+/g, "");
}

//바람전용
function comma1(str) {
  str = String(str);
  return str.replace(/(\B)(?=(?:\d{1})+(?!\d))/g, "$1.");
}

function uncomma1(str) {
  str = String(str);
  return str.replace(/[^-\d]+/g, "");
}

//실격횟수 카운트
function counting(str) {
  str = String(str);
  return (str.match(/X/g) || []).length;
}

//필드경기 최고결과
function fieldFinal(obj) {
  let top = "0";
  let a = obj.parentElement.parentElement;
  for (i = 3; i < a.children.length - 3; i++) {
    if (parseFloat(top) < parseFloat(a.children[i].firstElementChild.value)) {
      top = a.children[i].firstElementChild.value;
    }
  }
  a.children[a.children.length - 3].firstElementChild.value = top;
}

// 멀리뛰기,삼단뛰기 경기에서 최고 기록 선정
function fieldFinal2(obj) {
  let top = "0";
  let wind = "";
  let a = obj.parentElement.parentElement;
  for (i = 3; i < a.children.length - 3; i++) {
    if (parseFloat(top) < parseFloat(a.children[i].firstElementChild.value)) {
      top = a.children[i].firstElementChild.value;

      wind = a.nextElementSibling.children[i - 3].firstElementChild.value;
    }
  }
  a.children[a.children.length - 3].firstElementChild.value = top;
  a.nextElementSibling.children[a.children.length - 7].firstElementChild.value =
    wind;
}

//멀리뛰기,삼단뛰기 바람 입력이 최고 기록 선정
function fieldFinal3(obj) {
  let top = "0";
  let wind = "";
  let a = obj.parentElement.parentElement.previousElementSibling;

  for (i = 3; i < a.children.length - 3; i++) {
    if (parseFloat(top) < parseFloat(a.children[i].firstElementChild.value)) {
      top = a.children[i].firstElementChild.value;

      wind = a.nextElementSibling.children[i - 3].firstElementChild.value;
    }
  }
  a.children[a.children.length - 3].firstElementChild.value = top;
  a.nextElementSibling.children[a.children.length - 7].firstElementChild.value =
    wind;
}

//일반 필드 경기
function rankcal() {
  const participants_data =
    get_horizontal_field_exclude_wind_game_information();
  print_horizontal_field_game_rank(participants_data);
}

//멀리뛰기 세단뛰기
function rankcal4() {
  const participants_data =
    get_horizontal_field_include_wind_game_information();
  print_horizontal_field_game_rank(participants_data);
}

//트랙 경기
function rankcal1() {
  let re = document.querySelectorAll("#result"); //결과 요소 가져옴
  let ran = document.querySelectorAll("#rank"); //둥수 요소가져옴
  let arr1 = {};
  for (i = 0; i < re.length; i++) {
    let k = i;
    arr1[k] = re[i].value; //객체에 결과 저장
  }
  let keysSorted = Object.keys(arr1).sort(function (a, b) {
    if (arr1[a] == "" || arr1[a] == "0") {
      return 1;
    }
    if (arr1[b] == "" || arr1[b] == "0") return -1;
    return parseInt(uncomma(arr1[a])) - parseInt(uncomma(arr1[b]));
  }); //올림차순 정렬
  for (i = 0; i < ran.length; i++) {
    ran[keysSorted[i]].value = i + 1; //등수대로 기입
  }
}

function print_horizontal_field_game_rank(participants_data) {
  // 최대 기록 구하기
  for (let i = 0; i < participants_data.length; i++) {
    const high = Math.max(...participants_data[i].record);
    if (high === Number.NEGATIVE_INFINITY) participants_data[i].highest = -1;
    else participants_data[i].highest = high;
  }
  // 참가자별 내림차순
  participants_data.sort(function (participant1, participant2) {
    if (participant1.highest < participant2.highest) return 1;
    else if (participant1.highest === participant2.highest) return 0;
    else return -1;
  });

  // 동순위 발생 시 이전 데이터를 보고 정렬
  for (let i = 0; i < participants_data.length; i) {
    // 동순위 확인
    let duplicate_record_count = 1;
    for (let j = i + 1; j < participants_data.length; j++) {
      if (participants_data[i].highest == participants_data[j].highest)
        duplicate_record_count += 1;
    }
    // 동순위가 있다면 동순위 사람들끼리 모아서 정렬
    if (duplicate_record_count !== 1) {
      const duplicate_record = participants_data.splice(
        i,
        duplicate_record_count
      );
      // 동순위 사람들의 모든 기록을 문자열로 변환
      for (let j = 0; j < duplicate_record.length; j++) {
        for (let k = 0; k < duplicate_record[j].record.length; k++) {
          let sum_string_record = "";
          sum_string_record = duplicate_record[j].record[k].replace(".", "");
          if (sum_string_record.length !== 4)
            sum_string_record += "0".repeat(4 - sum_string_record.length);
          duplicate_record[j].hit += sum_string_record;
        }
      }
      // hit을 기준으로 내림차순
      duplicate_record.sort(function (participants1, participants2) {
        if (participants1.hit < participants2.hit) return 1;
        else if (participants1.hit > participants2.hit) return -1;
        return 0;
      });
      // hit이 동률인 사람이 있으면 same에 삽입
      for (let j = 0; j < duplicate_record.length; j) {
        let duplicate_hit = 1;
        for (let k = j + 1; k < duplicate_record.length; k++) {
          if (duplicate_record[j].hit === duplicate_record[k].hit)
            duplicate_hit += 1;
        }
        if (duplicate_hit !== 1) {
          const same_hit = duplicate_record.splice(j, duplicate_hit);
          for (let k = 0; k < same_hit.length; k++) {
            same_hit[k].same = same_hit;
          }
          duplicate_record.splice(j, 0, ...same_hit);
        }
        j += duplicate_hit;
      }
      participants_data.splice(i, 0, ...duplicate_record);
    }
    i += duplicate_record_count;
  }

  // 등수 기록
  let rank = 0;
  for (let i = 0; i < participants_data.length; i) {
    rank += 1;
    if (participants_data[i].same.length === 0) {
      const rank_field = search_participant(
        participants_data,
        participants_data[i].name
      );
      rank_field.value = rank;
    } else {
      participants_data[i].same.forEach(function (same_participant_data) {
        const rank_field = search_participant(
          participants_data,
          same_participant_data.name
        );
        rank_field.value = rank;
      });
      rank += participants_data[i].same.length - 1;
    }
    i = rank;
  }
}

function get_horizontal_field_exclude_wind_game_information() {
  const participants_data = document.getElementsByTagName("tbody")[0].children;
  const participants_information = [];
  for (let i = 0; i < participants_data.length; i++) {
    const participant_information = {};
    const participant = participants_data[i];
    const participant_record = [];
    participant_information["name"] = participant.children[3].children[0].value;
    for (let j = 4; j < participant.children.length - 3; j++) {
      const record = participant.children[j].children[0].value;
      if (record !== "" && record !== "X" && record != "-")
        participant_record.push(record);
      else if (record === "X" || record === "-") participant_record.push("0");
    }
    participant_information["record"] = participant_record.sort(function (
      record1,
      record2
    ) {
      if (record1 > record2) return -1;
      else if (record1 < record2) return 1;
      else return 0;
    });
    participant_information["same"] = [];
    participant_information["hit"] = "";
    participants_information.push(participant_information);
  }
  return participants_information;
}

function get_horizontal_field_include_wind_game_information() {
  const participants_data = document.getElementsByTagName("tbody")[0].children;
  const participants_information = [];
  for (let i = 0; i < participants_data.length; i += 2) {
    const participant_information = {};
    const participant = participants_data[i];
    const participant_record = [];
    participant_information["name"] = participant.children[3].children[0].value;
    for (let j = 4; j < participant.children.length - 3; j++) {
      const record = participant.children[j].children[0].value;
      if (record !== "" && record !== "X" && record != "-")
        participant_record.push(record);
      else if (record === "X" || record === "-") participant_record.push("0");
    }
    participant_information["record"] = participant_record.sort(function (
      record1,
      record2
    ) {
      if (record1 > record2) return -1;
      else if (record1 < record2) return 1;
      else return 0;
    });
    participant_information["same"] = [];
    participant_information["hit"] = "";
    participants_information.push(participant_information);
  }
  return participants_information;
}

//등수 자동 배정 올림차순
function rankcal1() {
  let re = document.querySelectorAll("#result"); //결과 요소 가져옴
  let ran = document.querySelectorAll("#rank"); //둥수 요소가져옴
  let arr1 = {};
  for (i = 0; i < re.length; i++) {
    let k = i;
    arr1[k] = re[i].value; //객체에 결과 저장
  }
  let keysSorted = Object.keys(arr1).sort(function (a, b) {
    if (arr1[a] == "" || arr1[a] == "0") {
      return 1;
    }
    if (arr1[b] == "" || arr1[b] == "0") return -1;
    return parseInt(uncomma(arr1[a])) - parseInt(uncomma(arr1[b]));
  }); //올림차순 정렬
  for (i = 0; i < ran.length; i++) {
    ran[keysSorted[i]].value = i + 1; //등수대로 기입
  }
}

//높이뛰기 전용 등수계산
function rankcal2() {
  // 참가자들의 정보가 들어 있는 배열
  let participants = [];
  participants = get_participants_information();
  // 참가자별 최대 높이를 구함
  for (let i = 0; i < participants.length; i++) {
    const height_index = participants[i].success_attempts.lastIndexOf(1);
    if (height_index !== -1)
      participants[i].highest = participants[i].height[height_index];
    else participants[i].highest = "0";
  }
  // 참가자별 높이별 내림차순
  participants.sort(function (participant1, participant2) {
    if (parseFloat(participant1.highest) < parseFloat(participant2.highest))
      return 1;
    else if (
      parseFloat(participant1.highest) === parseFloat(participant2.highest)
    )
      return 0;
    else return -1;
  });

  // 동순위 발생 시 성공한 경기 무효 시기를 보고 결정 (1회 시기부터 적용)
  for (let i = 0; i < participants.length; i) {
    let highest_duplicate_count = 1;
    // 동순위가 있는지 확인
    for (let j = i + 1; j < participants.length; j++) {
      if (participants[i].highest == participants[j].highest)
        highest_duplicate_count += 1;
    }
    // 만약 동순위가 있으면 해당 길이 만큼 배열을 자르고 시기 순으로 정렬
    if (highest_duplicate_count !== 1) {
      const highest_duplicate = participants.splice(i, highest_duplicate_count);
      // 시기 순으로 오름차순
      highest_duplicate.sort(function (participant1, participant2) {
        const height_index1 = participant1.height.lastIndexOf(
          participant1.highest
        );
        const height_index2 = participant2.height.lastIndexOf(
          participant2.highest
        );

        if (
          participant1.failed_attempts[height_index1] <
          participant2.failed_attempts[height_index2]
        )
          return -1;
        else if (
          participant1.failed_attempts[height_index1] ===
          participant2.failed_attempts[height_index2]
        )
          return 0;
        else return 1;
      });
      // 시기도 같은지 확인
      for (let j = 0; j < highest_duplicate.length; j) {
        let trial_duplicate_count = 1;
        // 동시기가 있는지 확인
        for (let k = j + 1; k < highest_duplicate.length; k++) {
          const height_index1 = highest_duplicate[j].height.lastIndexOf(
            highest_duplicate[j].highest
          );
          const height_index2 = highest_duplicate[k].height.lastIndexOf(
            highest_duplicate[k].highest
          );
          if (
            highest_duplicate[j].failed_attempts[height_index1] ===
            highest_duplicate[k].failed_attempts[height_index2]
          )
            trial_duplicate_count += 1;
        }
        // 만약 동시기가 있으면 해당 길이 만큼 배열을 자르고 전체 시도 순으로 정렬
        if (trial_duplicate_count !== 1) {
          const trial_duplicate = highest_duplicate.splice(
            j,
            trial_duplicate_count
          );
          // 전체 시기 순으로 오름차순
          trial_duplicate.sort(function (participant1, participant2) {
            const participant1_total_failed =
              participant1.failed_attempts.reduce(
                (sum, value) => sum + value,
                0
              );
            const participant2_total_failed =
              participant2.failed_attempts.reduce(
                (sum, value) => sum + value,
                0
              );

            if (participant1_total_failed < participant2_total_failed)
              return -1;
            else if (participant1_total_failed === participant2_total_failed)
              return 0;
            else return 1;
          });
          // 그래도 같은 동시기가 있다면 동점 처리
          for (let k = 0; k < trial_duplicate.length; k) {
            let again_duplicate_count = 1;
            for (let l = k + 1; l < trial_duplicate.length; l++) {
              const participant1_total_failed = trial_duplicate[
                k
              ].failed_attempts.reduce((sum, value) => sum + value, 0);
              const participant2_total_failed = trial_duplicate[
                l
              ].failed_attempts.reduce((sum, value) => sum + value, 0);
              if (participant1_total_failed === participant2_total_failed)
                again_duplicate_count += 1;
            }
            // 전체 무효시기도 같다면 무효 마킹 처리
            if (again_duplicate_count !== 1) {
              const same_marked = trial_duplicate.splice(
                k,
                again_duplicate_count
              );
              for (let l = 0; l < same_marked.length; l++) {
                same_marked[l].same = same_marked;
              }
              trial_duplicate.splice(k, 0, ...same_marked);
            }
            k += again_duplicate_count;
          }
          highest_duplicate.splice(j, 0, ...trial_duplicate);
        }
        j += trial_duplicate_count;
      }
      participants.splice(i, 0, ...highest_duplicate);
    }
    i += highest_duplicate_count;
  }

  // 랭크 부여
  let rank = 0;
  for (let i = 0; i < participants.length; i) {
    rank += 1;
    if (participants[i].same.length === 0) {
      const rank_field = search_participant(participants, participants[i].name);
      rank_field.value = rank;
    } else {
      participants[i].same.forEach(function (same_participant_data) {
        const rank_field = search_participant(
          participants,
          same_participant_data.name
        );
        rank_field.value = rank;
      });
      rank += participants[i].same.length - 1;
    }
    i = rank;
  }
}

function get_participants_information() {
  const participant_name_data = document.querySelectorAll("#name");
  const height_data = document.querySelectorAll("#trial");
  const participant_name = [];
  const height = [];
  const participants_information = [];
  const fail_trial = [];
  const success_trial = [];
  // 참가자 이름 배열 생성
  participant_name_data.forEach(function (name) {
    participant_name.push(name.value);
  });
  // 생성된 높이 배열 생성
  height_data.forEach(function (height_data) {
    if (height_data.value != "") height.push(height_data.value);
  });
  const limit = height.length;
  // 참가자 별 시도 횟수 배열 생성
  for (let i = 1; i <= participant_name.length; i++) {
    const failed_trials = [];
    const success_trials = [];
    const trial1 = document.querySelectorAll(".col1_" + i)[0].children;
    const trial2 = document.querySelectorAll(".col2_" + i)[0].children;
    let count = 0;
    // 참가자 위 시도 컬럼
    for (let j = 3; j < trial1.length - 3; j++) {
      let trial_result = trial1[j].children[0].value;
      const failed_regx = new RegExp("X", "g");
      const success_regx = new RegExp("O", "g");
      trial_result = trial_result.toUpperCase();
      failed_trials.push((trial_result.match(failed_regx) || []).length);
      success_trials.push((trial_result.match(success_regx) || []).length);
      count += 1;
      if (count == limit) {
        fail_trial.push(failed_trials);
        success_trial.push(success_trials);
        break;
      }
    }
    if (count == limit) continue;
    // 참가자 아래 시도 컬럼
    for (let j = 0; j < trial2.length - 1 && j != limit - 12; j++) {
      let trial_result = trial2[j].children[0].value;
      const failed_regx = new RegExp("X", "g");
      const success_regx = new RegExp("O", "g");
      trial_result = trial_result.toUpperCase();
      failed_trials.push((trial_result.match(failed_regx) || []).length);
      success_trials.push((trial_result.match(success_regx) || []).length);
      count += 1;
      if (count == limit) {
        fail_trial.push(failed_trials);
        success_trial.push(success_trials);
        break;
      }
    }
  }
  // 참가자 시도 횟수 딕셔너리 생성
  for (let i = 0; i < participant_name.length; i++) {
    const participant_dictionary = {};
    // 이름
    participant_dictionary["name"] = participant_name[i];
    // 높이
    participant_dictionary["height"] = height;
    // 높이당 실패
    participant_dictionary["failed_attempts"] = fail_trial[i];
    // 높이당 성공
    participant_dictionary["success_attempts"] = success_trial[i];
    // 동기록 동시기 해결 필드
    participant_dictionary["same"] = [];
    // 참가자 정보 배열에 선수 정보 추가
    participants_information.push(participant_dictionary);
  }
  return participants_information;
}

function search_participant(participants, name) {
  const rank_field = document.querySelectorAll("#rank");
  const name_field = document.querySelectorAll("#name");
  for (let i = 0; i < participants.length; i++) {
    if (name_field[i].value == name) {
      return rank_field[i];
    }
  }
  return undefined;
}
