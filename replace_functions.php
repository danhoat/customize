<?php

function cs_mje_show_contact_link($to_user){
	$user = get_userdata($to_user);
	?>
	  <li><a href="" class="create-chat " data-touser= "<?php echo $user->user_login; ?>"><?php _e('Contact me', 'enginethemes');?><i class="fa fa-comment"></i></a></li>
	  <div id="message-form"></div>
	  <?php

}