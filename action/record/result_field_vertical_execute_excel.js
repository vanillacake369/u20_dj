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
      if ((row_count < 3 || row_count == 15) && col_count % 2 === 0) {
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

function create_header(html) {
  const thead_data = document.getElementsByTagName("thead")[0].children;
  const top_height_data = thead_data[0].children;
  const bottom_height_data = thead_data[1].children;
  html +=
    '<thead><tr><th rowSpan="2">Rank</th> <th rowSpan="2">Order</th><th rowSpan="2">Name</th>';
  for (let i = 3; i < top_height_data.length - 2; i++) {
    const height = top_height_data[i].children[0].value;
    html += "<th>" + height + "</th>";
  }
  html += '<th rowSpan="2">Result</th> <th>Memo</th> </tr> <tr>';
  for (let i = 0; i < bottom_height_data.length - 1; i++) {
    const height = bottom_height_data[i].children[0].value;
    html +=
      "<th>\u00a0\u00a0\u00a0\u00a0\u00a0" +
      height +
      "\u00a0\u00a0\u00a0\u00a0\u00a0</th>";
  }
  html += "<th>Record</th></tr></thead>";
  return html;
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
    "<style> table, th, td { border: 1px solid black; text-align: center; } td { min-width: 3rem; height: 2rem;}</style>";
  html = html + "<table>";
  html = add_match_information(html, sport_name, round, judge_name, memo);
  html = html + "</table><br><table>";
  html = create_header(html);
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
