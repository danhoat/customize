<?php
if (isset($_REQUEST['id'])) {
    $post = get_post($_REQUEST['id']);
    if ($post) {
        global $ae_post_factory;
        $post_object = $ae_post_factory->get($post->post_type);
        echo '<script type="data/json"  id="edit_postdata">' . json_encode($post_object->convert($post)) . '</script>';
    }

}
if (isset($_GET['return_url'])) {
    $return = $_GET['return_url'];
} else {
    $return = home_url();
}
$post_title = $auto_price = $time_delivery = $post_content = '';
if(DEVELOP_MODE){
    $post_title = 'I can do generator title at '. date('l jS \of F Y h:i:s A');
    $auto_price = 10;
    $time_delivery = 3;
    $post_content = 'Generator random post content at '.date('l jS \of F Y h:i:s A');
}
?>
<div class="step-wrapper step-post" id="step-post">
    <form class="post-job post et-form" id="" method="post">
        <div class="form-group clearfix">
            <div class="input-group">
                <label for="post_title" class="input-label"><?php _e('Job name', 'enginethemes-child');?></label>
                <input type="text" class="input-item input-full" name="post_title" value="<?php echo $post_title;?>" required>
            </div>
        </div>

        <div class="form-group row clearfix <?php echo ('1' == ae_get_option('custom_price_mode') || is_super_admin($user_ID)) ? 'has-price-field' : ''; ?>">
            <?php if ('1' == ae_get_option('custom_price_mode') || is_super_admin($user_ID)): ?>
            <?php
            $min_price = ae_get_option('mjob_min_price') ? absint(ae_get_option('mjob_min_price')) : 5;
            $max_price = ae_get_option('mjob_max_price') ? absint(ae_get_option('mjob_max_price')) : 15;
            $currency_code = ae_currency_code(false); ?>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 clearfix">
                    <div class="input-group">
                        <?php if (is_super_admin($user_ID) && '1' != ae_get_option('custom_price_mode')) { ?>
                            <label for="et_budget"><?php printf(__('Price (%s)', 'enginethemes-child'), $currency_code);?></label>
                            <input type="number" name="et_budget" class="input-item et_budget field-positive-int time-delivery" min="1" pattern="[-+]?[0-9]*[.,]?[0-9]+" value="<?php echo $auto_price;?>" required>
                            <?php
                        } else { ?>
                            <label for="et_budget"><?php printf(__('Price (%s)', 'enginethemes-child'), $currency_code);?></label>
                            <input type="number" name="et_budget" placeholder="<?php printf(__('%s - %s', 'enginethemes-child'), mje_format_price($min_price, "", true, false), mje_format_price($max_price, "", true, false));?>" class="input-item et_budget field-positive-int time-delivery" min="<?php echo $min_price ?>" max="<?php echo $max_price ?>" pattern="[-+]?[0-9]*[.,]?[0-9]+" required>
                        <?php } ?>
                    </div>
                </div>
            <?php endif?>

            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 delivery-area">
                <div class="input-group delivery-time">
                    <label for="time_delivery"><?php _e('Time of delivery (Day)', 'enginethemes-child');?></label>
                    <input type="number" name="time_delivery" value="<?php echo $time_delivery;?>" class="input-item time-delivery" min="0">
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 category-area">
                <div class="input-group">
                    <label for="mjob_category"><?php _e('Category', 'enginethemes-child');?></label>
                    <?php ae_tax_dropdown('mjob_category',
                        array('attr' => 'data-chosen-width="100%" data-chosen-disable-search=""  data-placeholder="' . __("Choose categories", 'enginethemes-child') . '"',
                            'class' => 'chosen chosen-single tax-item required',
                            'hide_empty' => false,
                            'hierarchical' => true,
                            'id' => 'mjob_category',
                            'show_option_all' => false,
                        )
                    );?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="input-group">
                <label class="mb-20"><?php _e('Description', 'enginethemes-child')?></label>
                <?php wp_editor($post_content, 'post_content', ae_editor_settings());?>
            </div>
        </div>

        <div class="form-group group-attachment gallery_container" id="gallery_container">
            <div class="input-group">
                <label class="mb-20"><?php _e('Gallery', 'enginethemes-child');?></label>
                <div class="outer-carousel-gallery">
                    <div class="img-avatar carousel-gallery">
                        <img width="100%" src="<?php echo TEMPLATEURL ?>/assets/img/image-avatar.jpg" alt="">
                        <div class="upload-description">
                            <i class="fa fa-picture-o"></i>
                            <p><?php _e(' Up to 5 pictures whose each minimum size is 768 x 435px', 'enginethemes-child');?></p>
                            <p><?php _e('Select one picture for your featured image', 'enginethemes-child');?></p>
                        </div>
                        <input type="hidden" class="input-item show" name="et_carousels" value="" />
                    </div>
                </div>
                <div class="attachment-image clearfix">
                    <ul class="carousel-image-list image-list" id="image-list">

                    </ul>
                    <span class="image-upload carousel_container" id="carousel_container">
                        <span for="file-input" class="carousel_browse_button" id="carousel_browse_button">
                            <a class="add-img"><img src="<?php echo get_template_directory_uri() ?>/assets/img/icon-plus.png" alt=""></a>
                        </span>
                    </span>

                    <span class="et_ajaxnonce" id="<?php echo wp_create_nonce('ad_carousels_et_uploader'); ?>"></span>
                </div>
            </div>
        </div>
        <?php do_action('input_address_field');?>

        <div class="form-group clearfix">
            <div class="input-group audio-field">
                <label class="mb-20"><?php _e('Audio', 'enginethemes-child');?></label>
                <div class="aud-wrapper">
                    <?php
                    $i = 0;
                    if( isset($_COOKIE['audio_0']) ){
                    $data0 = explode(',', stripslashes($_COOKIE['audio_0']));
                    if ( count($data0) == 4 && isset($data0[0], $data0[1], $data0[2], $data0[3]) ) { $i++;
                        $idata = new stdClass();
                        $idata->src = $data0[2];
                        $idata->host = $data0[3]; ?>
                    <div class="audio-placeholer" id="up_0">
                        <p class="audio-text"><span><?php echo $data0[0]; ?></span> <span><?php echo $data0[1]; ?></span></p>
                        <?php if ( $data0[3] == "host" ) { ?>
                            <div class="audio-progess">
                                <div class="audio-progess-bar" style="width: 100%;">100%</div>
                            </div>
                        <?php } ?>
                        <input type="hidden" class="input-item" name="audio_clip_0" value="<?php echo encodeURIComponent(json_encode($idata)); ?>">
                        <a href="#" class="remove-items aud-remove-extra-item"><i class="fa fa-times"></i></a>
                    </div>
                    <?php } }
                    if( isset($_COOKIE['audio_1']) ){
                    $data1 = explode(',', stripslashes($_COOKIE['audio_1']));
                    if ( count($data1) == 4 && isset($data1[0], $data1[1], $data1[2], $data1[3]) ) { $i++;
                        $idata = new stdClass();
                        $idata->src = $data1[2];
                        $idata->host = $data1[3]; ?>
                    <div class="audio-placeholer" id="up_1">
                        <p class="audio-text"><span><?php echo $data1[0]; ?></span> <span><?php echo $data1[1]; ?></span></p>
                        <?php if ( $data1[3] == "host" ) { ?>
                            <div class="audio-progess">
                                <div class="audio-progess-bar" style="width: 100%;">100%</div>
                            </div>
                        <?php } ?>
                        <input type="hidden" class="input-item" name="audio_clip_1" value="<?php echo encodeURIComponent(json_encode($idata)); ?>">
                        <a href="#" class="remove-items aud-remove-extra-item"><i class="fa fa-times"></i></a>
                    </div>
                    <?php } }
                    if( isset($_COOKIE['audio_2']) ){
                    $data2 = explode(',', stripslashes($_COOKIE['audio_2']));
                    if ( count($data2) == 4 && isset($data2[0], $data2[1], $data2[2], $data2[3]) ) { $i++;
                        $idata = new stdClass();
                        $idata->src = $data2[2];
                        $idata->host = $data2[3]; ?>
                    <div class="audio-placeholer" id="up_2">
                        <p class="audio-text"><span><?php echo $data2[0]; ?></span> <span><?php echo $data2[1]; ?></span></p>
                        <?php if ( $data2[3] == "host" ) { ?>
                            <div class="audio-progess">
                                <div class="audio-progess-bar" style="width: 100%;">100%</div>
                            </div>
                        <?php } ?>
                        <input type="hidden" class="input-item" name="audio_clip_2" value="<?php echo encodeURIComponent(json_encode($idata)); ?>">
                        <a href="#" class="remove-items aud-remove-extra-item"><i class="fa fa-times"></i></a>
                    </div>
                    <?php } } ?>
                </div>
                <div class="aud-grp"></div>
                <div class="new_audio" style="<?php echo ($i == 3) ? 'display:none' : ''; ?>"><?php _e('Add audio clip', 'enginethemes-child'); ?><span class="icon-plus"><i class="fa fa-plus"></i></span></div>
            </div>
        </div>

        <div class="form-group clearfix">
            <label><?php _e('Video', 'enginethemes-child');?></label>
            <input type="text" class="input-item form-control text-field" id="video_meta" placeholder="<?php _e("Add link from Youtube, Vimeo or .MP4 ", 'enginethemes-child');?>" name="video_meta"  autocomplete="off" spellcheck="false" >
            <ul class="skills-list" id="skills_list"></ul>
        </div>

        <div class="form-group clearfix">
            <label class="mb-20"><?php _e('Extra services', 'enginethemes-child');?></label>
            <div class="mjob-extras-wrapper">
            </div>
            <div class="add-more">
                <a href="#" class="mjob-add-extra-btn"><?php _e('Add extra', 'enginethemes-child');?><span class="icon-plus"><i class="fa fa-plus"></i></span></a>
            </div>
        </div>

        <div class="form-group skill-control">
            <label><?php _e('Tags', 'enginethemes-child');?></label>
            <input type="text" class="form-control text-field skill" id="skill" placeholder="<?php _e("Enter job tags", 'enginethemes-child');?>" name=""  autocomplete="off" spellcheck="false" >
            <ul class="skills-list" id="skills_list"></ul>
        </div>
        <div class="form-group skill-control">
            <label for="opening_message"><?php _e('Opening message', 'enginethemes-child')?> <i class="fa fa-question-circle popover-opening-message" aria-hidden="true"></i></label>
            <p class="note-message">
                <?php _e('Opening message is automatically displayed as your first message in the order detail page.', 'enginethemes-child');?>
            </p>
            <textarea name="opening_message" class="input-item"></textarea>
        </div>
        <?php  //do_action( 'list_featured_package'  ); // @since 1.8.3 ?>
        <div class="form-group">
            <button class="<?php mje_button_classes(array('btn-save', 'waves-effect', 'waves-light'))?>" type="submit"><?php _e('SAVE', 'enginethemes-child');?></button>
            <a href="<?php echo $return; ?>" class="btn-discard"><?php _e('DISCARD', 'enginethemes-child');?></a>
            <input type="hidden" class="input-item post-service_nonce" name="_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync'); ?>" />
        </div>
    </form>
</div>