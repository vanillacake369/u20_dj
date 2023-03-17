// function addColumn() {
//     const table = document.getElementsByName("table_name[]");
//     // 새 행(Row) 추가
//     const newRow = table.insertRow();

//     // 새 행(Row)에 Cell 추가
//     const Cell1 = newRow.insertCell(0);
//     const Cell2 = newRow.insertCell(1);

//     // Cell에 텍스트 추가
//     Cell1.innerHTML =
//         '<input type="hidden" name="group[]" value="2"><td <style="background: none;"><input type="text" placeholder="순서" class="input_text" name="lane[]"></td>';
//     Cell2.innerHTML =
//         '<td style="background: none;"><input type="text" placeholder="선수 이름" class="input_text" name="playername[]"></td>';
// }
// 복사코드

function addColumn() {
    for (var i = 0; i < 12; i++) {
        const table = document.getElementsByName("table_name[]")[i];
        var cloneElements = $("#copy-value").clone();
        console.log(cloneElements);

        // 새 행(Row) 추가
        const newRow = table.insertRow();

        // 새 행(Row)에 Cell 추가
        const Cell1 = newRow.insertCell(0);
        const Cell2 = newRow.insertCell(1);

        // Cell에 텍스트 추가
        Cell1.innerHTML =
            '<input type="hidden" name="group[]" value="1"><td <style="background: none;"><input type="text" placeholder="순서" class="input_text" name="lane[]"></td>';
        Cell2.innerHTML =
            '<td><div id="copy-value"></div><input type="hidden" name="playerid[]" value=""></td>';
        //$("#athlete").clone().appendTo("#copy-element");
        cloneElements.appendTo("#copy-element");
        cloneElements.appendTo("#copy-value");
    }
}
// function deleteColumn() {
//    const table = document.getElementById('athlete_table');

// const newRow = table.deleteRow(rownum);

//     for(let i = 0; i < table.rows.length; i++)  {

//       table.deleteRow();
//   }
//}

// function add_row() {
//   var my_tbody = document.getElementById('athlete_table');
//   // var row = my_tbody.insertRow(0); // 상단에 추가
//   var row = my_tbody.insertRow( my_tbody.rows.length ); // 하단에 추가
//   var cell1 = row.insertCell(0);
//   var cell2 = row.insertCell(1);
//   cell1.innerHTML = 'HELLO world';
//   cell2.innerHTML = new Date().toUTCString();
// }

/*
function deleteColumn() {
    for (var i = 0; i < 12; i++) {
        var my_tbody = document.getElementsByName("table_name[]")[i];
        console.log(my_tbody);
        if (my_tbody.rows.length < 1) return;
        // my_tbody.deleteRow(0); // 상단부터 삭제
        my_tbody.deleteRow(my_tbody.rows.length - 1); // 하단부터 삭제
    }
}
*/

function deleteColumn() {
    for (var i = 0; i < 12; i++) {
        var my_tbody = [];
        my_tbody = document.getElementsByClassName("team_table")[i];
        console.log(my_tbody);
        if (my_tbody.rows.length < 1) return;
        console.log(my_tbody);
        // my_tbody.deleteRow(0); // 상단부터 삭제
        my_tbody.deleteRow(my_tbody.rows.length - 1); // 하단부터 삭제
    }
}
