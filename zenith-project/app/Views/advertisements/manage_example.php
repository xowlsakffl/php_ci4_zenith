<script>
getTestCampaigns01(args);
function setDataTable(tableId, columns, args){
    $(tableId).DataTable({
        "processing" : true,
        "searching": false,
        "ordering": true,
        "bLengthChange" : false, 
        "bDestroy": true,
        "paging": false,
        "info": false,
        "ajax": {
            "url": "<?=base_url()?>/advertisements/data",
            "data": args,
            "type": "GET",
            "contentType": "application/json",
            "dataType": "json",
        },
        "columns": columns,
        "language": {
            "emptyTable": "데이터가 존재하지 않습니다.",
            "lengthMenu": "페이지당 _MENU_ 개씩 보기",
            "infoEmpty": "데이터 없음",
            "infoFiltered": "( _MAX_건의 데이터에서 필터링됨 )",
            "search": "에서 검색: ",
            "zeroRecords": "일치하는 데이터가 없어요.",
            "loadingRecords": "로딩중...",
        },
    });
}

function getTestCampaigns02(args) {
    setDataTable('#campaigns-table', [
            { "title" : "캠페인명", "data": "name" },
            { "title" : "상태", "data": "status" },
            { 
                "title" : "예산", 
                "data": "budget", 
                "render": function (data, type, row) {
                    if (data !== null) {
                        budget = '\u20A9'+parseInt(data).toLocaleString('ko-KR');  
                    }else{
                        budget = "";
                    }
                    return budget;
                }
            }
        ], args);
}

function getTestCampaigns02(args) {
    setDataTable('#campaigns-table', [
            { "title" : "캠페인명", "data": "name" },
            { "title" : "상태", "data": "status" },
            { "title" : "목표Ai", "data": "autoBudget"},
            { 
                "title" : "예산", 
                "data": "budget", 
                "render": function (data, type, row) {
                    if (data !== null) {
                        budget = '\u20A9'+parseInt(data).toLocaleString('ko-KR');  
                    }else{
                        budget = "";
                    }
                    return budget;
                }
            }
        ], args);
}
</script>