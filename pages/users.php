<?php
	current_user_role_identify(2);
?>
<!DOCTYPE HTML>
<html lang="zh_CN">
	<?php get_head(); ?>
	<body>

		<?php get_header();?>	
	
	    <div id="bodier" class="">
	        <div class="container">
				<?php 
				$actions=array('userslist','usergroups','newuser','edituser','editself');
				hao_load_in('action',$actions,'pages/users');
				?>
			</div>
	    </div>
		
		<?php get_footer();?>
	
    
	</body>
</html>