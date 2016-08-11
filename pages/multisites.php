<?php
	if(!is_multisite_on())
		hao_redirect('404.php',302);
	current_user_role_identify(1);
?>
<!DOCTYPE HTML>
<html lang="zh_CN">
	<?php get_head(); ?>
	<body>

		<?php get_header();?>	
	
	    <div id="bodier" class="">
	        <div class="container">
				<?php 
				$actions=array("siteslist","newsite","editsite");
				hao_load_in('action',$actions,'pages/multisites');
				?>
			</div>
	    </div>
		
		<?php get_footer();?>
    
	</body>
</html>