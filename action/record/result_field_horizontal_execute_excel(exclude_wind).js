function get_match_information() {
  const result = document.querySelectorAll(".input_row");
  const match_information = [];
  for (let i = 0; i < result.length; i++) {
    match_information[i] = result[i].children[0].value;
  }
  return match_information;
}

function get_match_result() {
  const result = document.getElementsByTagName("tbody");
  const header_data = result[0].children[0].children;
  const match_data = result[1].children;
  const header = [];
  const match_result = [];
  // 해더
  for (let i = 0; i < header_data.length; i++) {
    header.push(header_data[i].innerText);
  }
  // 경기 결과
  for (let i = 0; i < match_data.length; i++) {
    const athlete_data = [];
    for (let j = 0; j < header.length; j++) {
      const data = match_data[i].children[j].children[0].value;
      athlete_data.push(data);
    }
    match_result.push(athlete_data);
  }
  return [header, match_result];
}

function add_header(html, header) {
  html += "<tr>";
  header.forEach(function (title) {
    html += "<th>" + title + "</th>";
  });
  html += "</tr>";
  return html;
}

function add_table(html, match_result) {
  match_result.forEach(function (result) {
    html += "<tr>";
    result.forEach(function (data) {
      html += "<td>" + data + "</td>";
    });
    html += "</tr>";
  });
  return html;
}

function add_match_information(
  html,
  sport_name,
  round,
  judge_name,
  wind,
  memo
) {
  const title = ["Sport", "Round", "Judge", "Equipment", "Memo"];
  html += "<tr>";
  title.forEach(function (title) {
    html += "<th>" + title + "</th>";
  });
  html += "</tr><tr>";
  html += "<td>" + sport_name + "</td>";
  html += "<td>" + round + "</td>";
  html += "<td>" + judge_name + "</td>";
  html += "<td>" + wind + "</td>";
  html += "<td>" + memo + "</td>";
  html += "</tr>";
  return html;
}

function execute_excel() {
  let sport_name, judge_name, round, wind, memo;
  let header, match_result;
  [sport_name, round, judge_name, wind, memo] = get_match_information();
  [header, match_result] = get_match_result(); // FIXME: 한글 해더 영어로 고치기 (현재 안들어 기록지 에러 떠서 안들어 가짐)

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
  html = add_match_information(html, sport_name, round, judge_name, wind, memo);
  html = html + "</table><br><table>";
  html = add_header(html, header);
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
