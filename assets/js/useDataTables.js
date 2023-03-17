$(document).ready(function () {
    $("#nation_rank").DataTable({
        language: {
            emptyTable: "데이터가 없어요.",
            lengthMenu: "페이지당 _MENU_ 개씩 보기",
            info: "현재 _START_ - _END_ / _TOTAL_건",
            infoEmpty: "데이터 없음",
            infoFiltered: "( _MAX_건의 데이터에서 필터링됨 )",
            search: "검색: ",
            zeroRecords: "일치하는 데이터가 없어요.",
            loadingRecords: "로딩중...",
            processing: "잠시만 기다려 주세요...",
            paginate: {
                next: "다음",
                previous: "이전",
            },
        },
    });
});
$(document).ready(function () {
    $("#player_rank").DataTable({
        language: {
            emptyTable: "데이터가 없어요.",
            lengthMenu: "페이지당 _MENU_ 개씩 보기",
            info: "현재 _START_ - _END_ / _TOTAL_건",
            infoEmpty: "데이터 없음",
            infoFiltered: "( _MAX_건의 데이터에서 필터링됨 )",
            search: "검색: ",
            zeroRecords: "일치하는 데이터가 없어요.",
            loadingRecords: "로딩중...",
            processing: "잠시만 기다려 주세요...",
            paginate: {
                next: "다음",
                previous: "이전",
            },
        },
    });
});
$(document).ready(function () {
    $("#judge_table").DataTable({
        language: {
            emptyTable: "데이터가 없어요.",
            lengthMenu: "페이지당 _MENU_ 개씩 보기",
            info: "현재 _START_ - _END_ / _TOTAL_건",
            infoEmpty: "데이터 없음",
            infoFiltered: "( _MAX_건의 데이터에서 필터링됨 )",
            search: "검색: ",
            zeroRecords: "일치하는 데이터가 없어요.",
            loadingRecords: "로딩중...",
            processing: "잠시만 기다려 주세요...",
            paginate: {
                next: "다음",
                previous: "이전",
            },
        },

        columnDefs: [
            {
                orderable: false,
                targets: 9,
            },
            {
                orderable: false,
                targets: 10,
            },
            {
                orderable: false,
                targets: 11,
            },
        ],
    });
});

$(document).ready(function () {
    $("#director_table").DataTable({
        language: {
            emptyTable: "데이터가 없어요.",
            lengthMenu: "페이지당 _MENU_ 개씩 보기",
            info: "현재 _START_ - _END_ / _TOTAL_건",
            infoEmpty: "데이터 없음",
            infoFiltered: "( _MAX_건의 데이터에서 필터링됨 )",
            search: "검색: ",
            zeroRecords: "일치하는 데이터가 없어요.",
            loadingRecords: "로딩중...",
            processing: "잠시만 기다려 주세요...",
            paginate: {
                next: "다음",
                previous: "이전",
            },
        },

        columnDefs: [
            {
                orderable: false,
                targets: 9,
            },
            {
                orderable: false,
                targets: 10,
            },
            {
                orderable: false,
                targets: 11,
            },
        ],
    });
});

$(document).ready(function () {
    $("#coach_table").DataTable({
        language: {
            emptyTable: "데이터가 없어요.",
            lengthMenu: "페이지당 _MENU_ 개씩 보기",
            info: "현재 _START_ - _END_ / _TOTAL_건",
            infoEmpty: "데이터 없음",
            infoFiltered: "( _MAX_건의 데이터에서 필터링됨 )",
            search: "검색: ",
            zeroRecords: "일치하는 데이터가 없어요.",
            loadingRecords: "로딩중...",
            processing: "잠시만 기다려 주세요...",
            paginate: {
                next: "다음",
                previous: "이전",
            },
        },

        columnDefs: [
            {
                orderable: false,
                targets: 10,
            },
            {
                orderable: false,
                targets: 11,
            },
            {
                orderable: false,
                targets: 12,
            },
        ],
    });
});
