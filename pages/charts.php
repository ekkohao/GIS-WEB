<!DOCTYPE html>

<html lang="zh-CN">
<head >
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.css" rel="stylesheet" />
    <link href="css/main.css" rel="stylesheet" />
    <link href="css/datetimepick.css" rel="stylesheet" />
    <link rel="stylesheet" id="fontawesome-css" href="http://apps.bdimg.com/libs/fontawesome/4.4.0/css/font-awesome.min.css" type="text/css" media="all">
 

    <title>图表数据分析</title>
</head>
<body>

        <?php get_header();?>

    <div id="bodier" class="">
        <div class="container ">
            <div class="widget">
                <h3><i class="icon-screenshot"></i>设备信息</h3>
                <dl class="devinfo dl-horizontal info">
                   <dd> &nbsp;&nbsp;&nbsp;&nbsp;请选择设备</dd>
                </dl>
            </div>  
            <div class="widget">
                <h3><i class="icon-signer"></i>
                    曲线分析
                    <span class="pull-right line-info"><i class="icon-info-sign"></i></span>
                    <span class="info-box">
                        提示：<br />
                        1.按住&lt;shift&gt;点击可以平移<br />
                        2.单击下方的图例可以显示或取消相应曲线<br />
                    </span>
                </h3>
                <div id="line-chart" class="chart">&nbsp;&nbsp;&nbsp;&nbsp;请选择设备</div>
            </div>  
            <div class="widget">
                <h3><i class="icon-list-of"></i>数据表格</h3>
                <table class="table table-striped table-hover ac-table">
                    <thead>
                        <tr>
                          <th>动作时间</th>
                          <th>动作次数</th>
                          <th>电流（uA）</th>
                          <th>温度（℃）</th>
                          <th>湿度（%）</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;请选择设备</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <footer id="footer">
        <div class="container">
            <div class="row footer-bottom">
                <div class="text-center" style="margin-bottom:10px">&copy; 2016 合肥科鼎电气有限公司</div>
                <ul class="list-inline text-center">
                    <li>官网：<a href="http://www.hfkeding.cn/" target="_blank">http://www.hfkeding.cn/</a></li>
                    <li>电话：0551-65336059</li>
                    <li>邮箱：<a href="mailto:hfkeding@sina.com">hfkeding@sina.com</a></li>
                </ul>
          
            </div>
        </div>
    </footer>
    <aside class="aside-fix">
        <div class="pick-show"><i class="fa fa-play"></i></div>
        <form class="f-main-pick">
            <div class="main-pick">
                <div class="form-group">
                    <label>起始日期：</label>
                    <input id='inputdatefrom' class='input form-control' readOnly="true"  />
                </div>
                <div class="form-group">
                    <label>截至日期：</label>
                    <input id='inputdateto' class='input form-control' readOnly="true"  />
                </div>
                <div class="form-group dev-select">
                    <label>设备选择：</label>
                    <select class="form-control">
                        <?php 
                            global $mydb;
                            $devs=$mydb->get_all_devs();


                        foreach($devs as $dev){
                            echo '<option value="'.$dev['dev_number'].'">'.$dev['dev_number'].'</option>';
                        }
          
                         ?>
                    </select>
                </div>
                <div class="form-group mode-select">
                    <label>模式选择：
                        <span class="pull-right line-info"><i class="icon-info-sign"></i></span>
                        <span class="info-box">
                            在模式二下，X轴将对应实际的时间，而不是模式一下固定的间隔
                        </span>
                    </label><br />
                    <select class="form-control">
                        <option value="1">模式一</option>
                        <option value="2">模式二</option>                   
                    </select>
                </div>
                <button id="sc" type="submit" class="btn btn-primary">查询</button>
            </div>
        </form>
    </aside>

</body>
    <script type="text/javascript" src="js/jquery.js"></script>   
    <script type="text/javascript" src="js/highcharts.js" ></script>
    <script type="text/javascript" src="js/charts.js"></script>
    <script type="text/javascript" src="js/datetimepick.js"></script>
    <script type="text/javascript">
        rome(inputdatefrom);
        rome(inputdateto);
    </script>
</html>
