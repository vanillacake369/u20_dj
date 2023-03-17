function addColumn() {
    const table = document.getElementsByName("table_name[]");
    // 새 행(Row) 추가
    const newRow = table.insertRow();
    const newRow1 = table.insertRow();
    const newRow2 = table.insertRow();
    const newRow3 = table.insertRow();
    // 새 행(Row)에 Cell 추가
    const Cell1 = newRow.insertCell(0);
    const Cell2 = newRow.insertCell(1);
    const Cell3 = newRow.insertCell(2);
    const Cell4 = newRow.insertCell(3);

    const Cell5 = newRow1.insertCell(0);
    const Cell6 = newRow1.insertCell(1);
    const Cell7 = newRow1.insertCell(2);
    const Cell8 = newRow1.insertCell(3);

    const Cell9 = newRow2.insertCell(0);
    const Cell10 = newRow2.insertCell(1);
    const Cell11 = newRow2.insertCell(2);
    const Cell12 = newRow2.insertCell(3);

    const Cell13 = newRow3.insertCell(0);
    const Cell14 = newRow3.insertCell(1);
    const Cell15 = newRow3.insertCell(2);
    const Cell16 = newRow3.insertCell(3);
    // Cell에 텍스트 추가
    Cell1.innerHTML =
        '<input type="hidden" name="group[]" value="2"><td <style="background: none;"><input type="text" placeholder="순서" class="input_text" name="lane[]"></td>';
    Cell2.innerHTML =
        '<td style="background: none;"><input type="text" placeholder="선수 이름" class="input_text" name="playername[]"></td>';
    Cell3.innerHTML =
        '<td style="background: none;"><input type="text" placeholder="국가" class="input_text" name="country[]"></td>';
    Cell4.innerHTML =
        '<td style="background: none;"><input type="text" placeholder="소속" class="input_text" name="division[]"></td>';

    Cell5.innerHTML =
        '<td <style="background: none;"><input type="text" placeholder="순서" class="input_text" name="lane[]"></td>';
    Cell6.innerHTML =
        '<td style="background: none;"><input type="text" placeholder="선수 이름" class="input_text" name="playername[]"></td>';
    Cell7.innerHTML =
        '<td style="background: none;"><input type="text" placeholder="국가" class="input_text" name="country[]"></td>';
    Cell8.innerHTML =
        '<td style="background: none;"><input type="text" placeholder="소속" class="input_text" name="division[]"></td>';

    Cell9.innerHTML =
        '<td <style="background: none;"><input type="text" placeholder="순서" class="input_text" name="lane[]"></td>';
    Cell10.innerHTML =
        '<td style="background: none;"><input type="text" placeholder="선수 이름" class="input_text" name="playername[]"></td>';
    Cell11.innerHTML =
        '<td style="background: none;"><input type="text" placeholder="국가" class="input_text" name="country[]"></td>';
    Cell12.innerHTML =
        '<td style="background: none;"><input type="text" placeholder="소속" class="input_text" name="division[]"></td>';

    Cell13.innerHTML =
        '<td <style="background: none;"><input type="text" placeholder="순서" class="input_text" name="lane[]"></td>';
    Cell14.innerHTML =
        '<td style="background: none;"><input type="text" placeholder="선수 이름" class="input_text" name="playername[]"></td>';
    Cell15.innerHTML =
        '<td style="background: none;"><input type="text" placeholder="국가" class="input_text" name="country[]"></td>';
    Cell16.innerHTML =
        '<td style="background: none;"><input type="text" placeholder="소속" class="input_text" name="division[]"></td>';
}

function addColumn() {
    for (var i = 0; i < 12; i++) {
        const table = document.getElementsByName("table_name[]")[i];
        // 새 행(Row) 추가
        const newRow = table.insertRow();

        // 새 행(Row)에 Cell 추가
        const Cell1 = newRow.insertCell(0);
        const Cell2 = newRow.insertCell(1);
        const Cell3 = newRow.insertCell(2);
        const Cell4 = newRow.insertCell(3);

        // Cell에 텍스트 추가
        Cell1.innerHTML =
            '<input type="hidden" name="group[]" value="2"><td <style="background: none;"><input type="text" placeholder="순서" class="input_text" name="lane[]"></td>';
        Cell2.innerHTML =
            '<td <style="background: none;"><input type="text" placeholder="선수 이름" class="input_text" name="playername[]"></td>';
        Cell3.innerHTML =
            '<td <style="background: none;"><input type="text" placeholder="국가" class="input_text" name="country[]"></td>';
        Cell4.innerHTML =
            '<td <style="background: none;"><input type="text" placeholder="소속" class="input_text" name="division[]"></td>';
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
        my_tbody.deleteRow(my_tbody.rows.length - 2);
        my_tbody.deleteRow(my_tbody.rows.length - 3);
        my_tbody.deleteRow(my_tbody.rows.length - 4);
    }
}
