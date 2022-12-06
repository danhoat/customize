<?php
$video=get_post_meta($mjob_post->ID,'video_meta',true);
$clip1=get_post_meta($mjob_post->ID,'audio_clip_0',true);
$clip2=get_post_meta($mjob_post->ID,'audio_clip_1',true);
$clip3=get_post_meta($mjob_post->ID,'audio_clip_2',true);
$data = []; $total=     $podcast = 0;
$au_pos = $vid_pos = $fist_gallery = -1;
$slider_html = $gallery_html = '';
$total_audio = 0;

$vid_pos = -1;
if ( !empty($video) ) {
    $vid_pos = 0;
    $data[] = ['type'=>'video', 'src'=>$video];
}
$dclip1 = @json_decode(decodeURIComponent($clip1));
if ( is_object($dclip1) && isset($dclip1->src, $dclip1->host) ) {
    if($dclip1->src){
        if ( $dclip1->host == "host" ) {
            $clip1src = wp_get_attachment_url($dclip1->src);
            $clip1title = get_the_title($dclip1->src);
            $data[] = ['type'=>'audio', 'src'=>$clip1src, 'title'=>substr($clip1title, 0, 30), 'host'=>$dclip1->host];
        } else {
            $data[] = ['type'=>'audio', 'src'=>$dclip1->src, 'title'=>__('Audio Clip', 'enginethemes-child'), 'host'=>$dclip1->host];
            if ($dclip1->host == "podcast") {
                $podcast++;
            }
        }
        $total_audio++;
    }
}

$dclip2 = @json_decode(decodeURIComponent($clip2));
if ( is_object($dclip2) && isset($dclip2->src, $dclip2->host) ) {
    if($dclip2->src){
        if ( $dclip2->host == "host" ) {
            $clip2src = wp_get_attachment_url($dclip2->src);
            $clip2title = get_the_title($dclip2->src);
            $data[] = ['type'=>'audio', 'src'=>$clip2src, 'title'=>substr($clip2title, 0, 30), 'host'=>$dclip2->host];
        } else {
            $data[] = ['type'=>'audio', 'src'=>$dclip2->src, 'title'=>__('Audio Clip', 'enginethemes-child'), 'host'=>$dclip2->host];
            if ($dclip2->host == "podcast") {
                $podcast++;
            }
        }
        $total_audio++;
    }
}

$dclip3 = @json_decode(decodeURIComponent($clip3));
if ( is_object($dclip3) && isset($dclip3->src, $dclip3->host) ) {
    if($dclip3->src){
        if ( $dclip3->host == "host" ) {
            $clip3src = wp_get_attachment_url($dclip3->src);
            $clip3title = get_the_title($dclip3->src);
            $data[] = ['type'=>'audio', 'src'=>$clip3src, 'title'=>substr($clip3title, 0, 30), 'host'=>$dclip3->host];
        } else {
            $data[] = ['type'=>'audio', 'src'=>$dclip3->src, 'title'=>__('Audio Clip', 'enginethemes-child'), 'host'=>$dclip3->host];
            if ($dclip3->host == "podcast") {
                $podcast++;
            }
        }
        $total_audio++;
    }
}
if($total_audio > 0){
    $au_pos = $vid_pos + 1;
}
if ( has_post_thumbnail($mjob_post->ID) ) {
    $fist_gallery = $au_pos + 1;
    foreach($mjob_post->et_carousel_urls as $key=>$value){
        $slide = wp_get_attachment_image_src($value->ID, "mjob_detail_slider");
        if ( isset($slide[0]) ) {
            $data[] = ['type'=>'gallery', 'src'=>$slide[0]];
            $class = '';
            $gallery_html.="<div class='item' mjob-post\audio.php line-gallery>
                <img src='{$slide[0]}' alt=''>
            </div>";
        }
    }
}



if ( !empty($data) ) { ?>
    <div class="gallery audiogal"><div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
        <?php
        $listhtml = $thumbnail_html = '';
        $hasaudio = $is_first = $has_aud = $first_i = false;
        $no_thumbnail = true;
        echo '<ol class="carousel-indicators mjob-carousel-indicators">';

        if($video){ ?>
            <li data-target="#carousel-example-generic" data-slide-to="<?php echo $vid_pos;?>" class="active" video></li>
            <?php
        }
        if ( $has_aud ) {  ?>
            <li data-target="#carousel-example-generic" data-slide-to="<?php echo $au_pos; ?>" class="<?php echo ($au_pos == 0) ? 'active' : ''; ?>" au_pos></li>
        <?php }

        echo '</ol><div class="carousel-inner mjob-single-carousels" role="listbox">';



        foreach ($data as $key => $info) {
            if ($info['type'] == 'video') { ?>
                <div class="item <?php echo ($key ==0 ) ? 'active' : ''; ?> mjob-post\audio.php line-video">
                    <div class="vid_player">
                        <?php echo mje_get_video_single($info['src'],'100%','100%'); ?>
                    </div>
                </div>
            <?php } else if ($info['type'] == 'audio') {
                if(isset($info['src']) && !empty($info['src'])){
                    if ( isset($info['host']) && ($info['host'] == "host" || $info['host'] == "external" )) {
                        $listhtml.= '<li class="hap-playlist-item" data-type="audio" data-mp3="'.$info['src'].'" data-thumb="'.get_stylesheet_directory_uri().'/assets/img/poster.png" data-title="'.$info['title'].'"></li>';
                        $hasaudio = true;
                        if ( $key ==0 ) {
                            $is_first = true;
                        }
                    } else {
                        $needed = (($total-$podcast) < 3) ? (3-($total-$podcast)) : 1;
                        $item = (int)($needed/$podcast);
                        $listhtml.= '<li class="hap-playlist-item" data-type="podcast" data-path="'.$info['src'].'" data-limit="'.$item.'"></li>';
                        $hasaudio = true;
                        if ( $key ==0 ) {
                            $is_first = true;
                        }
                    }
                }
            }
        }
        if ( $hasaudio ) {
            list_audio_show($listhtml);
        }

        echo $gallery_html;


        echo '</div>'; ?>

        <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
            <span class="fa fa-angle-left" aria-hidden="true"></span>
            <span class="sr-only"><?php _e('Previous', 'enginethemes'); ?></span>
        </a>
        <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
            <span class="fa fa-angle-right" aria-hidden="true"></span>
            <span class="sr-only"><?php _e('Next', 'enginethemes'); ?></span>
        </a>

    </div></div>
<?php } else {
    $photo_default=get_template_directory_uri().'/assets/img/mjob.png';
    if(ae_get_option('default_mjob')){
        $default=ae_get_option('default_mjob');
        $defautl_thumb=$default['mjob_detail_slider'];
        $photo_default=$defautl_thumb[0];
    }
    echo '<img src="'.$photo_default.'" alt="">';
} ?>