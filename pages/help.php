<!DOCTYPE HTML>
<html lang="zh_CN">
	<?php get_head(); ?>
	<body>

		<?php 
			get_header();
		?>	
		<style type="text/css">
			.widget-to-hidden{
				padding:15px 20px;
			}
		</style>
	    <div id="bodier" class="">
	        <div class="container">
	        	<section class="widget widget-click widget-hidden">
					<h3><span>1.如何添加新设备</span><i class="fa fa-chevron-down pull-right" aria-hidden="true"></i></h3>
					<div class="widget-to-hidden">
						<p>(1)点击页面最上处的导航条中的设备管理</p>
						<p>(2)点击页面左上角的新增设备</p>
						<p>(3)如需在添加设备过程中添加杆塔或线路，点击杆塔或线路选择框下相应的按钮</p>
						<p>(4)也可选中某杆塔或线路进行编辑</p>
						<p>(5)填入完成后提交，如有错误会在提交按钮和相应输入框下方提示</p>
					<div>
				</section> 
				<section class="widget widget-click widget-hidden">
					<h3><span>2.如何添加新杆塔</span><i class="fa fa-chevron-down pull-right" aria-hidden="true"></i></h3>
					<div class="widget-to-hidden">
						<p>(1)点击页面最上处的导航条中的杆塔管理</p>
						<p>(2)点击页面左上角的新增杆塔</p>
						<p>(3)杆塔添加过程中可以点击线路选择框下方的新线路和编辑线路来添加或编辑线路</p>
						<p>(4)坐标点击坐标输入框下的坐标查询，然后在打开网页的地图上点击目标点，右上角会有点击的目标点信息</p>
						<p>(5)填入完成后提交，如有错误会在提交按钮和相应输入框下方提示</p>
					<div>
				</section>
				<section class="widget widget-click widget-hidden">
					<h3><span>3.如何添加新线路</span><i class="fa fa-chevron-down pull-right" aria-hidden="true"></i></h3>
					<div class="widget-to-hidden">
						<p>(1)点击页面最上处的导航条中的线路管理</p>
						<p>(2)点击页面左上角的新增线路，弹出添加线路对话框</p>
					<div>
				</section>
				<section class="widget widget-click widget-hidden">
					<h3><span>4.设备、杆塔与线路的关系与操作</span><i class="fa fa-chevron-down pull-right" aria-hidden="true"></i></h3>
					<div class="widget-to-hidden">
						<p>(1)根据实际需求，每个杆塔上最多绑定<b>两条</b>线路，每条线路存在A、B、C<b>三个</b>相位，可添加三个设备</p>
						<p>(2)理论上应先添加线路，再添加杆塔并绑定线路，最后添加设备；这些操作都可在添加设备一个页面完成</p>
						<p>(3)新添加完的线路和杆塔及杆塔与线路的绑定关系会保存在数据库中，无需重复添加</p>
					<div>
				</section>

	        </div>
		</div>
	
		<?php get_footer();?>
    
	</body>
	<script type="text/javascript">
		$('.widget-click').click(function(){
			$(this).toggleClass('widget-hidden');
		})
	</script>
</html>