<?php

$video = get_post_meta($mjob_post->ID,'video_meta',true);
?>
<div class="gallery">

    <!-- <img src="<?php /*echo $current->the_post_thumbnail; */?>" width="100%" alt="">-->

    <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">

        <!-- Indicators -->

        <?php
        if( !empty($mjob_post->et_carousel_urls) || !empty($video) ):
            $active ='active'; $i = 0; ?>

            <ol class="carousel-indicators mjob-carousel-indicators">
                <?php if( !empty($video) ){ ?>
                     <li data-target="#carousel-example-generic" data-slide-to="<?php echo $i ?>" class="<?php echo $active; ?>" video-indicatior></li>
                    <?php
                    $i++;
                } ?>

                <?php
                $fist_gallery = $i;
                  if( !empty($mjob_post->et_carousel_urls) ): ?>
                    <?php foreach($mjob_post->et_carousel_urls as $key=>$value){
                        $active  = '';
                    if($key == 0 && $fist_gallery == 0){
                        $active = 'active';
                    } ?>
                        <li data-target="#carousel-example-generic" data-slide-to="<?php echo $i ?>" class="<?php echo $active; ?>" gallery-indicatio></li>
                        <?php
                        $i++;

                    } ?>
                <?php endif; ?>


            </ol>
        <?php endif;?>


        <!-- Wrapper for slides -->

        <?php  if( !empty($mjob_post->et_carousel_urls) ):

            $active ='';

            ?>

            <div class="carousel-inner mjob-single-carousels" role="listbox">

                <?php

                $current_pos = 0;
                foreach($mjob_post->et_carousel_urls as $key=>$value){

                    $slide = wp_get_attachment_image_src($value->ID, "mjob_detail_slider");
                    $slide_url = $slide[0];
                     $active = '';
                    if($current_pos == 0 & $key == 0)
                        $active = 'active';
                    ?>

                    <div class="item <?php echo $active;?> mjob-post\gallery.php-line-57 111">
                        <img src="<?php echo $slide_url; ?>" alt="">
                    </div>
                    <?php

                    $current_pos ++;

                }
                if($video){
                    $active = '';
                    if($current_pos == 0) $active = 'active';
                    ?>
                    <div class="item <?php echo $active;?> mjob-post\gallery.php-line-57 111">
                         <div class="vid_player">
                        <?php

                         echo mje_get_video_single($video,'100%','100%'); ?>

                    ?>
                    </div>
                </div>
                    <?php

                }

                 ?>

            </div>

        <?php endif; ?>



        <!-- Controls -->

        <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">

            <span class="fa fa-angle-left" aria-hidden="true"></span>

            <span class="sr-only"><?php _e('Previous', 'enginethemes'); ?></span>

        </a>

        <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">

            <span class="fa fa-angle-right" aria-hidden="true"></span>

            <span class="sr-only"><?php _e('Next', 'enginethemes'); ?></span>

        </a>

    </div>

</div>