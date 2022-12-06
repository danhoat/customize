<?php

$video=get_post_meta($mjob_post->ID,'video_meta',true);

$clip1=get_post_meta($mjob_post->ID,'audio_clip_0',true);

$clip2=get_post_meta($mjob_post->ID,'audio_clip_1',true);

$clip3=get_post_meta($mjob_post->ID,'audio_clip_2',true);

$photo_default=get_template_directory_uri().'/assets/img/mjob.png';

if(ae_get_option('default_mjob')){

	$default=ae_get_option('default_mjob');

	$defautl_thumb=$default['mjob_detail_slider'];

	$photo_default=$defautl_thumb[0];

}

$is_audio = (!empty($clip1) || !empty($clip2) || !empty($clip3)) ? true : false ;



if ($is_audio) {

	mje_get_template( 'template/mjob-post/audio.php', array( 'mjob_post' => $mjob_post ) );

} else if($video && !$is_audio && !has_post_thumbnail() ){

	echo mje_get_video_single($video,'100%','100%');

} else {

	if(has_post_thumbnail($mjob_post->ID)){

		mje_get_template( 'template/mjob-post/gallery.php', array( 'mjob_post' => $mjob_post ) );

	} else{ ?>

		<img src="<?php echo $photo_default ; ?>" alt="">

	<?php

	}

}

?>