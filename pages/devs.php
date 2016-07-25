<!DOCTYPE HTML>
<html lang="zh_CN">
	<?php get_head(); ?>
	<body>

		<?php get_header();?>	
	
	    <div id="bodier" class="">
	        <div class="container">
				<?php 
				$actions=array("devslist","newdev","editdev");
				hao_load_in('action',$actions,'pages/devs');
				?>
			</div>
	    </div>
		
		<?php get_footer();?>
    
	</body>
</html>
