function getInfo1(datefrom,dateto,devid) {
    var arr_actiontime = new Array();
    var arr_actionnum = new Array();
    var arr_inum = new Array();
    var arr_tem = new Array();
    var arr_hum = new Array();
    var arr_cc = ['#9ACD32', '#f7a35c', '#8085e9','#f15c80', '#e4d354'];
    $.ajax({
        type: "post",
        url: "datapost.aspx",
        dataType: "json",
        data: "dateFrom="+ datefrom+"&dateTo="+dateto+"&devId="+devid,
        beforeSend: LoadFunction, //加载执行方法    
        //error: erryFunction,  //错误执行方法    
        success: function (tt) {
            var json = eval(tt); //数组
            var tt = "";
            if (json.devinfo == null)
                $("dl.devinfo").html("&nbsp;&nbsp;&nbsp;&nbsp;没有此设备，请重新选择设备");
            else
                $("dl.devinfo").html("<dt>设备编号</dt><dd>" + json.devinfo.dev_name + "</dd><dt>所属杆塔</dt><dd>" + json.devinfo.group_name + "</dd><dt>相位</dt><dd>" + json.devinfo.dev_phase + "</dd>");
            if (json.data == null) {
                alert("该时段没有数据！");
                return;
            } else {
                var isby = true;
                if (datefrom.substring(0, 4) == dateto.substring(0, 4))
                    isby = false;
                $.each(json.data, function (index) {
                    //循环获取数据
                    if(isby)
                        arr_actiontime[index] = json.data[index].action_time;
                    else
                        arr_actiontime[index] = json.data[index].action_time.substr(5);
                    arr_actionnum[index] = Number(json.data[index].action_num);
                    arr_inum[index] = Number(json.data[index].i_num);
                    arr_tem[index] = Number(json.data[index].tem);
                    arr_hum[index] = Number(json.data[index].hum);
                    $(".ac-table tbody").append("<tr><td>" + json.data[index].action_time + "</td><td>" + arr_actionnum[index] + "次</td><td>" + arr_inum[index] + "uA</td><td>" + arr_tem[index] + "℃</td><td>" + arr_hum[index] + "%</td></tr>");
                });
                //转换数组类型为整形

                $(function () {
                    $('#line-chart').highcharts({                   //图表展示容器，与div的id保持一致
                        chart: {
                            type: 'line',                       //指定图表的类型，默认是折线图（line）
                            zoomType: 'x',
                            pinchType: 'x',
                            panning: true,
                            panKey: 'shift',
                            alignTicks: false
                        },
                        tooltip: {
                            crosshairs: [{ width: 3 }, true],
                            shared: true
                        },
                        credits: {
                            enabled: false     //去掉highcharts网站url  

                        },
                        title: {
                            text: ''      //指定图表标题
                        },
                        colors: arr_cc,
                        xAxis: {
                            type:'datetime',
                            title: { text: '时间' },
                            categories: arr_actiontime,
                            tickmarkPlacement: 'on',
                        //},{
                        //    title: { text: '设备编号' },
                        //    categories: arr_devid,
                        //    tickmarkPlacement: 'on',
                        //    opposite: true
                        },
                        yAxis: [{ // Primary yAxis
                            title: {
                                text: '动作次数',
                                style: { color: arr_cc[0] },
                            },
                            allowDecimals: false,
                            gridLineWidth: 0,
                            //gridLineColor:arr_cc[0],
                            labels: {
                                style: { color: arr_cc[0] },
                                format: '{value}次'
                            },
                        }, { // Secondary yAxis
                            title: {
                                style: { color: arr_cc[1] },
                                text: '电流',
                            },
                            gridLineWidth:0,
                            labels: {
                                style: { color: arr_cc[1] },
                                format: '{value}uA'
                            },
                        }, { // Secondary yAxis
                            title: {
                                text: '温度',
                                style: { color: arr_cc[2] },
                            },
                            gridLineWidth: 0,
                            labels: {
                                style: { color: arr_cc[2] },
                                format: '{value}℃'
                            },
                            opposite: true,
                        }, { // Secondary yAxis
                            title: {
                                text: '湿度',
                                style: { color: arr_cc[3] },
                            },
                            gridLineWidth: 0,
                            min:0,
                            max:100,
                            labels: {
                                style: { color: arr_cc[3] },
                                format: '{value}%'
                            },
                            opposite: true,
                        }],
                        series: [{                                 //指定数据列
                            name: '动作次数',                          //数据列名
                            yAxis: 0,
                            tooltip: {
                                valueSuffix: '次'
                            },
                            data: arr_actionnum                       //数据
                        }, {
                            name: '电流',
                            yAxis: 1,
                            tooltip: {
                                valueSuffix: 'uA'
                            },
                            data: arr_inum
                        }, {
                            name: '温度',
                            yAxis: 2,
                            tooltip: {
                                valueSuffix: '℃'
                            },
                            data: arr_tem
                        }, {
                            name: '湿度',
                            yAxis: 3,
                            tooltip: {
                                valueSuffix: '%'
                            },
                            data: arr_hum
                        }]
                    });
                });
                //console.log(arr_actiontime);
                //console.log(arr_tem);
                //console.log(arr_hum);
            }
        },
    })
    function LoadFunction() {
        $("#line-chart").html("&nbsp;&nbsp;&nbsp;&nbsp;加载中...");
        $(".ac-table tbody").html("");
    }
    function erryFunction() {
        alert("error");
    };
}
function getInfo2(datefrom, dateto, devid) {
    var arr_actiontime = new Array();
    var arr_actionnum = new Array();
    var arr_inum = new Array();
    var arr_tem = new Array();
    var arr_hum = new Array();
    var arr_cc = ['#9ACD32', '#f7a35c', '#8085e9', '#f15c80', '#e4d354'];
    $.ajax({
        type: "post",
        url: "datapost.aspx",
        dataType: "json",
        data: "dateFrom=" + datefrom + "&dateTo=" + dateto + "&devId=" + devid,
        beforeSend: LoadFunction, //加载执行方法    
        //error: erryFunction,  //错误执行方法    
        success: function (tt) {
            var json = eval(tt); //数组
            var tt = "";
            if (json.devinfo == null)
                $("dl.devinfo").html("&nbsp;&nbsp;&nbsp;&nbsp;没有此设备，请重新选择设备");
            else
                $("dl.devinfo").html("<dt>设备编号</dt><dd>" + json.devinfo.dev_name + "</dd><dt>所属杆塔</dt><dd>" + json.devinfo.group_name + "</dd><dt>相位</dt><dd>" + json.devinfo.dev_phase + "</dd>");
            if (json.data == null) {
                alert("该时段没有数据！");
                return;
            } else {
                var dataformat = "%m/%d %H:%M";
                if (datefrom.substring(0, 4) != dateto.substring(0, 4))
                    dataformat = "%Y/%m/%d %H:%M";
                $.each(json.data, function (index) {
                    //循环获取数据    
                    var stringTime=arr_actiontime[index] = json.data[index].action_time;         
                    var timestamp = Date.parse(new Date(stringTime.replace(/\//g, "-")));
                    console.log(timestamp);
                    arr_actionnum[index] = [timestamp, Number(json.data[index].action_num)];
                    console.log(arr_actionnum[index]);
                    arr_inum[index] = [timestamp,Number(json.data[index].i_num)];
                    arr_tem[index] = [timestamp,Number(json.data[index].tem)];
                    arr_hum[index] = [timestamp,Number(json.data[index].hum)];
                    $(".ac-table tbody").append("<tr><td>" + arr_actiontime[index] + "</td><td>" + json.data[index].action_num + "次</td><td>" + json.data[index].i_num + "uA</td><td>" + json.data[index].tem + "℃</td><td>" + json.data[index].hum + "%</td></tr>");
                });

                $(function () {
                    $('#line-chart').highcharts({                   //图表展示容器，与div的id保持一致
                        chart: {
                            type: 'line',                       //指定图表的类型，默认是折线图（line）
                            zoomType: 'x',
                            pinchType: 'x',
                            panning: true,
                            panKey: 'shift',
                            alignTicks: false
                        },
                        tooltip: {
                            crosshairs: [{ width: 3 }, true],
                            shared: true,
                            xDateFormat: '%Y/%m/%d %H:%M:%S',
                        },
                        credits: {
                            enabled: false     //去掉highcharts网站url  

                        },
                        title: {
                            text: ''      //指定图表标题
                        },
                        colors: arr_cc,
                        xAxis: {
                            type: 'datetime',
                            title: { text: '时间' },
                            tickmarkPlacement: 'on', 
                            dateTimeLabelFormats: {
                                second: dataformat,
                                minute: dataformat,
                                hour: dataformat,
                                day: dataformat,
                                week: dataformat,
                                month: dataformat,
                                year: dataformat
                            }
                            //},{
                            //    title: { text: '设备编号' },
                            //    categories: arr_devid,
                            //    tickmarkPlacement: 'on',
                            //    opposite: true
                        },
                        yAxis: [{ // Primary yAxis
                            title: {
                                text: '动作次数',
                                style: { color: arr_cc[0] },
                            },
                            allowDecimals: false,
                            gridLineWidth: 0,
                            //gridLineColor:arr_cc[0],
                            labels: {
                                style: { color: arr_cc[0] },
                                format: '{value}次'
                            },
                        }, { // Secondary yAxis
                            title: {
                                style: { color: arr_cc[1] },
                                text: '电流',
                            },
                            gridLineWidth: 0,
                            labels: {
                                style: { color: arr_cc[1] },
                                format: '{value}uA'
                            },
                        }, { // Secondary yAxis
                            title: {
                                text: '温度',
                                style: { color: arr_cc[2] },
                            },
                            gridLineWidth: 0,
                            labels: {
                                style: { color: arr_cc[2] },
                                format: '{value}℃'
                            },
                            opposite: true,
                        }, { // Secondary yAxis
                            title: {
                                text: '湿度',
                                style: { color: arr_cc[3] },
                            },
                            gridLineWidth: 0,
                            min: 0,
                            max: 100,
                            labels: {
                                style: { color: arr_cc[3] },
                                format: '{value}%'
                            },
                            opposite: true,
                        }],
                        series: [{                                 //指定数据列
                            name: '动作次数',                          //数据列名
                            yAxis: 0,
                            tooltip: {
                                valueSuffix: '次'
                            },
                            data: arr_actionnum                       //数据
                        }, {
                            name: '电流',
                            yAxis: 1,
                            tooltip: {
                                valueSuffix: 'uA'
                            },
                            data: arr_inum
                        }, {
                            name: '温度',
                            yAxis: 2,
                            tooltip: {
                                valueSuffix: '℃'
                            },
                            data: arr_tem
                        }, {
                            name: '湿度',
                            yAxis: 3,
                            tooltip: {
                                valueSuffix: '%'
                            },
                            data: arr_hum
                        }]
                    });
                });
                console.log(arr_actiontime);
                console.log(arr_tem);
                console.log(arr_hum);
            }
        },
    })
    function LoadFunction() {
        $("#line-chart").html("&nbsp;&nbsp;&nbsp;&nbsp;加载中...");
        $(".ac-table tbody").html("");
    }
    function erryFunction() {
        alert("error");
    };
}
function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]); return null;
}
function getNowFormatDate(i) {
    var date = new Date();
    var month = date.getMonth() + 1-i;
    var day = date.getDate();
    var hour = date.getHours();
    var min = date.getMinutes();
    month = (month <= 9) ? ("0" + month) : month;
    day = (day <= 9) ? ("0" + day) : day;
    hour = (hour <= 9) ? ("0" + hour) : hour;
    min = (min <= 9) ? ("0" + min) : min;
    var currentdate = date.getFullYear() + "-" + month + "-" + day + " " + hour + ":" + min;
    return currentdate;
}
function init() {
    var datenow = getNowFormatDate();
    if ($("#inputdatefrom").val() == "")
        $("#inputdateto").val(getNowFormatDate(0));
    if ($("#inputdatefrom").val() == "")
        $("#inputdatefrom").val(getNowFormatDate(1));
    var datefrom = $("#inputdatefrom").val();
    var dateto = $("#inputdateto").val();
    var d = GetQueryString("d");
    if (d != null) {
        $(".main-pick .dev-select select").val(d);
        getInfo1(datefrom, dateto, d);
    }
}

$(document).ready(function () {
    init();
    $(".main-pick button").click(function () {
        var datefrom = $("#inputdatefrom").val();
        var dateto = $("#inputdateto").val();
        var d1 = new Date(datefrom.replace(/\-/g, "\/"));
        var d2 = new Date(dateto.replace(/\-/g, "\/"));
        if (d1 > d2){
            alert("起始时间不能大于截止时间！");
            return false;
        }
        var devid = $(".main-pick select").val();
        if (devid=="") {
            alert("请选择设备！");
            return false;
        }
        if ($(".main-pick .mode-select select").val()==1)
            getInfo1(datefrom, dateto, devid);
        else
            getInfo2(datefrom, dateto, devid);
        return false;
    });
    $(".aside-fix .pick-show").click(function () {
        $(".aside-fix").toggleClass("aside-active");
    });
    $(".line-info").hover(function () {
        $(this).next(".info-box").fadeIn();
    },function () {
        $(this).next(".info-box").fadeOut();
    })
})
