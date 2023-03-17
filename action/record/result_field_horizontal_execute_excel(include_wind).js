function get_match_information() {
  const result = document.querySelectorAll(".input_row");
  const match_information = [];
  for (let i = 0; i < result.length; i++) {
    match_information[i] = result[i].children[0].value;
  }
  return match_information;
}

function get_match_result() {
  const match_data = document.getElementsByTagName("tbody")[0].children;
  const match_result = [];
  // 경기 결과
  for (let i = 0; i < match_data.length; i++) {
    const result = [];
    if (i % 2 === 0) {
      for (let j = 0; j < match_data[i].children.length - 2; j++) {
        // 1차 ~ 기록 저장
        const data = match_data[i].children[j].children[0].value;
        result.push(data);
      }
      result.push(
        match_data[i].children[match_data[i].children.length - 1].children[0]
          .value
      ); // 비고 저장
    } else {
      // 풍속 저장
      for (let j = 0; j < match_data[i].children.length; j++) {
        if (match_data[i] === undefined) result.push(" ");
        else if (match_data[i].children[j] === undefined) result.push(" ");
        else {
          const data = match_data[i].children[j].children[0].value;
          result.push(data);
        }
      }
    }
    match_result.push(result);
  }
  return match_result;
}

function add_table(html, match_result) {
  let col_count = 0;
  match_result.forEach(function (result) {
    html += "<tr>";
    let row_count = 0;
    result.forEach(function (data) {
      if (row_count < 3 && col_count % 2 === 0) {
        html += "<td rowspan='2'>" + data + "</td>";
      } else {
        html += "<td>" + data + "</td>";
      }
      row_count += 1;
    });
    col_count += 1;
    html += "</tr>";
  });
  return html;
}

function add_match_information(html, sport_name, round, judge_name, memo) {
  const title = ["Event", "Round", "Judge", "Memo"];
  html += "<tr>";
  title.forEach(function (title) {
    html += "<th>" + title + "</th>";
  });
  html += "</tr><tr>";
  html += "<td>" + sport_name + "</td>";
  html += "<td>" + round + "</td>";
  html += "<td>" + judge_name + "</td>";
  html += "<td>" + memo + "</td>";
  html += "</tr>";
  return html;
}

function create_header(html, sport_name) {
  if (sport_name === "Decathlon" || sport_name === "Heptathlon") {
    return (
      html +
      "<thead>" +
      "<tr>" +
      '<th style="background: none" rowSpan="2">Rank</th>' +
      '<th style="background: none" rowSpan="2">Order</th>' +
      '<th style="background: none" rowSpan="2">Name</th>' +
      '<th style="background: none">Attempt 1</th>' +
      '<th style="background: none">Attempt 2</th>' +
      '<th style="background: none">Attempt 3</th>' +
      '<th style="background: none">Result</th>' +
      '<th style="background: none">Memo</th>' +
      "</tr>" +
      "<tr>" +
      '<th style="background: none" colSpan="4">Wind</th>' +
      '<th style="background: none">Record</th>' +
      "</tr>" +
      "</thead>"
    );
  } else {
    return (
      html +
      "<thead>" +
      "<tr>" +
      '<th style="background: none" rowSpan="2">Rank</th>' +
      '<th style="background: none" rowSpan="2">Order</th>' +
      '<th style="background: none" rowSpan="2">Name</th>' +
      '<th style="background: none">Attempt 1</th>' +
      '<th style="background: none">Attempt 2</th>' +
      '<th style="background: none">Attempt 3</th>' +
      '<th style="background: none">Attempt 4</th>' +
      '<th style="background: none">Attempt 5</th>' +
      '<th style="background: none">Attempt 6</th>' +
      '<th style="background: none">Result</th>' +
      '<th style="background: none">Memo</th>' +
      "</tr>" +
      "<tr>" +
      '<th style="background: none" colSpan="7">Wind</th>' +
      '<th style="background: none">Record</th>' +
      "</tr>" +
      "</thead>"
    );
  }
}

function execute_excel() {
  let sport_name, judge_name, round, memo;
  const match_result = get_match_result();
  [sport_name, round, judge_name, memo] = get_match_information();

  let html = '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
  html =
    html +
    '<head><meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8">';
  html = html + "<xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>";
  html = html + "<x:Name>Test Sheet</x:Name>";
  html =
    html +
    "<x:WorksheetOptions><x:Panes></x:Panes></x:WorksheetOptions></x:ExcelWorksheet>";
  html = html + "</x:ExcelWorksheets></x:ExcelWorkbook></xml></head><body>";
  html =
    html +
    "<style> table, th, td { border: 1px solid black; border-collapse: collapse; text-align: center; }</style>";
  html = html + "<table>";
  html = add_match_information(html, sport_name, round, judge_name, memo);
  html = html + "</table><br><table>";
  html = create_header(html, sport_name);
  html = add_table(html, match_result);
  html = html + "</table></body></html>";

  const blob = new Blob([html], { type: "application/csv;charset=utf-8;" });
  const elem = window.document.createElement("a");
  elem.href = window.URL.createObjectURL(blob);
  elem.download = "execute_excel.xls";
  document.body.appendChild(elem);
  elem.click();
  document.body.removeChild(elem);
}
