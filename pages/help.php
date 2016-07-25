<!DOCTYPE HTML>
<html lang="zh_CN">
	<?php get_head(); ?>
	<body>

		<?php 
			get_header();
		?>	
	
	    <div id="bodier" class="">
	        <div class="container">
	        	<section class="widget widget-click widget-hidden">
					<h3><span>1.如何添加新设备</span><i class="fa fa-chevron-down pull-right" aria-hidden="true"></i></h3>
					<div class="widget-to-hidden"><div>
				</section> 
				<section class="widget widget-click widget-hidden">
					<h3><span>2.如何添加新杆塔</span><i class="fa fa-chevron-down pull-right" aria-hidden="true"></i></h3>
					<div class="widget-to-hidden"><div>
				</section>
				<section class="widget widget-click widget-hidden">
					<h3><span>3.如何添加新线路</span><i class="fa fa-chevron-down pull-right" aria-hidden="true"></i></h3>
					<div class="widget-to-hidden"><div>
				</section>
				<section class="widget widget-click widget-hidden">
					<h3><span>4.设备、杆塔与线路的关系拓扑图</span><i class="fa fa-chevron-down pull-right" aria-hidden="true"></i></h3>
					<div class="widget-to-hidden"><div>
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