function get_match_information() {
  const result = document.querySelectorAll(".input_row");
  const match_information = [];
  for (let i = 0; i < result.length; i++) {
    match_information[i] = result[i].children[0].value;
  }
  return match_information;
}

function get_header() {
  const result = document.getElementsByTagName("thead");
  const data = result[0].children[0].children;
  const header = [];
  for (let i = 0; i < data.length; i++) {
    header.push(data[i].innerText);
  }
  return header;
}

function get_match_result() {
  const result = document.getElementsByTagName("tbody");
  const match_data = result[0].children;
  const match_result = [];
  for (let i = 0; i < match_data.length; i++) {
    const data = [];
    for (let j = 0; j < match_data[i].children.length; j++) {
      if (j === 6) continue;
      else if (j === 2) {
        // addTrackResult2 - 선수 이름 (4명)
        const name_by_team = [];
        for (let k = 0; k < match_data[i].children[j].children.length; k++) {
          name_by_team.push(match_data[i].children[j].children[k].value);
        }
        data.push(name_by_team.join("<br>"));
      } else data.push(match_data[i].children[j].children[0].value); // addTrackResult2 - 경기 결과 이외
    }
    match_result.push(data);
  }
  return match_result;
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
  const RECORD_COL = 5;
  match_result.forEach(function (result) {
    html += "<tr>";
    let count = 0;
    result.forEach(function (data) {
      if (count === RECORD_COL) {
        html += "<td>\u00a0" + data + "</td>";
      } else {
        html += "<td>" + data + "</td>";
      }
      count += 1;
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
  const title = ["Event", "Round", "Judge", "Wind", "Memo"];
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
  [sport_name, round, judge_name, wind, memo] = get_match_information();
  const header = [
    "Rank",
    "Lane",
    "Name",
    "Country",
    "Passed",
    "Result",
    "Memo",
    "Record",
  ];
  const match_result = get_match_result();

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
