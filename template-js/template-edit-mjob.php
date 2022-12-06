<?php global $post, $user_ID; ?>
<form  class="post-job step-post post et-form edit-mjob-form" style="display: none">
    <p class="mjob-title"><?php _e('Edit your job', 'enginethemes-child');?></p>

    <div class="loading">
        <div class="loading-img"></div>
    </div>

    <div class="form-group clearfix">
        <div class="input-group">
            <label for="post_title" class="input-label"><?php _e('Job name', 'enginethemes-child');?></label>
            <input type="text" class="input-item input-full" name="post_title" value="" required>
        </div>
    </div>
    <div class="form-group row clearfix <?php echo ('1' == ae_get_option('custom_price_mode') || is_super_admin($user_ID)) ? 'has-price-field' : ''; ?>">
        <?php if ('1' == ae_get_option('custom_price_mode') || is_super_admin($user_ID)): ?>
            <?php
$min_price = ae_get_option('mjob_min_price') ? absint(ae_get_option('mjob_min_price')) : 5;
$max_price = ae_get_option('mjob_max_price') ? absint(ae_get_option('mjob_max_price')) : 15;
$currency_code = ae_currency_code(false);
?>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 clearfix">
                <div class="input-group">
                    <?php
if (is_super_admin($user_ID) && '1' != ae_get_option('custom_price_mode')) {
    ?>
                        <label for="et_budget"><?php printf(__('Price (%s)', 'enginethemes-child'), $currency_code);?></label>
                        <input type="number" name="et_budget" class="input-item et_budget field-positive-int time-delivery" min="1" pattern="[-+]?[0-9]*[.,]?[0-9]+" required>
                        <?php
} else {
    ?>
                        <label for="et_budget"><?php printf(__('Price (%s)', 'enginethemes-child'), $currency_code);?></label>
                        <input type="number" name="et_budget" placeholder="<?php printf(__('%s - %s', 'enginethemes-child'), mje_format_price($min_price, "", true, false), mje_format_price($max_price, "", true, false));?>" class="input-item et_budget field-positive-int time-delivery" min="<?php echo $min_price ?>" max="<?php echo $max_price ?>" pattern="[-+]?[0-9]*[.,]?[0-9]+" required>
                        <?php
}
?>
                </div>
            </div>
        <?php endif?>

        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 delivery-area">
            <div class="input-group delivery-time">
                <label for="time_delivery"><?php _e('Time of delivery (Day)', 'enginethemes-child');?></label>
                <input type="number" name="time_delivery" value="" class="input-item time-delivery" min="0">
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
        <label class="mb-20"><?php _e('Description', 'enginethemes-child')?></label>
        <?php wp_editor('', 'post_content', ae_editor_settings());?>
    </div>

    <div class="form-group group-attachment gallery_container" id="gallery_container">
        <label class="mb-20"><?php _e('Gallery', 'enginethemes-child');?></label>
        <div class="outer-carousel-gallery">
            <div class="img-avatar carousel-gallery">
                <img width="100%" src="<?php echo TEMPLATEURL ?>/assets/img/image-avatar.jpg" alt="">
                <div class="upload-description">
                    <i class="fa fa-picture-o"></i>
                    <p><?php _e(' Up to 5 pictures whose each minimum size is 768 x 435px', 'enginethemes-child');?></p>
                    <p><?php _e('Select one picture for your featured image', 'enginethemes-child');?></p>
                </div>
                <input type="hidden" class="input-item" name="et_carousels" value="" />
            </div>
        </div>
        <div class="attachment-image has-image clearfix">
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
    <?php do_action('edit_input_address_field');?>

    <div class="form-group clearfix">
        <div class="input-group audio-field">
            <label class="mb-20"><?php _e('Audio', 'enginethemes-child');?></label>
            <div class="aud-wrapper">
                <?php
                $clip1_no = $clip2_no = $clip3_no = false;
                $clip1 = get_post_meta( $post->ID, 'audio_clip_0', true);
                $clip2 = get_post_meta( $post->ID, 'audio_clip_1', true);
                $clip3 = get_post_meta( $post->ID, 'audio_clip_2', true);
                $i = 0;

                $dclip1 = @json_decode(decodeURIComponent($clip1));
                if ( is_object($dclip1) && isset($dclip1->src, $dclip1->host) ) {
                    $i++;
                    echo '<div class="audio-placeholer" id="up_0">';
                    if ( isset(get_post($dclip1->src)->ID) ) {
                        $filename = !empty(basename(get_attached_file($dclip1->src))) ? basename(get_attached_file($dclip1->src)) : 'NA' ;
                        $filesize = isset(wp_get_attachment_metadata($dclip1->src)['filesize']) ? size_format(wp_get_attachment_metadata($dclip1->src)['filesize'], 2) : 'NA' ; ?>
                        <p class="audio-text"><span><?php echo $filename; ?></span> <span>(<?php echo $filesize; ?>)</span>
                        <a href="#" class="remove-items aud-remove-extra-item"><i class="fa fa-times"></i></a></p>
                        <div class="audio-progess">
                            <div class="audio-progess-bar" style="width: 100%;">100%</div>
                        </div>
                        <input type="hidden" class="input-item" name="audio_clip_0" value="<?php echo $clip1; ?>">

                    <?php } else { ?>
                        <p class="audio-text"><span><?php echo $dclip1->src; ?></span><span></span></p>
                        <input type="hidden" class="input-item" name="audio_clip_0" value="<?php echo $clip1; ?>">

                    <?php }
                    echo '</div>';
                } else {
                    echo '<input type="hidden" class="input-item" name="audio_clip_0" value="">';
                }

                $dclip2 = @json_decode(decodeURIComponent($clip2));
                if ( is_object($dclip2) && isset($dclip2->src, $dclip2->host) ) {
                    $i++;
                    echo '<div class="audio-placeholer" id="up_1">';
                    if ( isset(get_post($dclip2->src)->ID) ) {
                        $filename = !empty(basename(get_attached_file($dclip2->src))) ? basename(get_attached_file($dclip2->src)) : 'NA' ;
                        $filesize = isset(wp_get_attachment_metadata($dclip2->src)['filesize']) ? size_format(wp_get_attachment_metadata($dclip2->src)['filesize'], 2) : 'NA' ; ?>
                        <p class="audio-text"><span><?php echo $filename; ?></span> <span>(<?php echo $filesize; ?>)</span>

                        <a href="#" class="remove-items aud-remove-extra-item"><i class="fa fa-times"></i></a>
                    </p>
                        <div class="audio-progess">
                            <div class="audio-progess-bar" style="width: 100%;">100%</div>
                        </div>
                        <input type="hidden" class="input-item" name="audio_clip_1" value="<?php echo $clip2; ?>">

                        </p>
                    <?php } else { ?>
                        <p class="audio-text"><span><?php echo $dclip2->src; ?></span><span></span>
                        <input type="hidden" class="input-item" name="audio_clip_1" value="<?php echo $clip2; ?>">

                        </p>
                    <?php }
                    echo '</div>';
                } else {
                    echo '<input type="hidden" class="input-item" name="audio_clip_1" value="">';
                }

                $dclip3 = @json_decode(decodeURIComponent($clip3));
                if ( is_object($dclip3) && isset($dclip3->src, $dclip3->host) ) {
                    $i++;
                    echo '<div class="audio-placeholer" id="up_2">';
                    if ( isset(get_post($dclip3->src)->ID) ) {
                        $filename = !empty(basename(get_attached_file($dclip3->src))) ? basename(get_attached_file($dclip3->src)) : 'NA' ;
                        $filesize = isset(wp_get_attachment_metadata($dclip3->src)['filesize']) ? size_format(wp_get_attachment_metadata($dclip3->src)['filesize'], 2) : 'NA' ; ?>
                        <p class="audio-text"><span><?php echo $filename; ?></span> <span>(<?php echo $filesize; ?>)</span>
                         <a href="#" class="remove-items aud-remove-extra-item"><i class="fa fa-times"></i></a>
                     </p>
                        <div class="audio-progess">
                            <div class="audio-progess-bar" style="width: 100%;">100%</div>
                        </div>
                        <input type="hidden" class="input-item" name="audio_clip_2" value="<?php echo $clip3; ?>">

                    <?php } else { ?>
                        <p class="audio-text"><span><?php echo $dclip3->src; ?></span><span></span></p>
                        <input type="hidden" class="input-item" name="audio_clip_2" value="<?php echo $clip3; ?>">

                    <?php }
                    echo '</div>';
                } else {
                    echo '<input type="hidden" class="input-item" name="audio_clip_2" value="">';
                } ?>
            </div>
            <div class="aud-grp"></div>
            <input class="input-item" name="is_edit" type="hidden" value="true">
            <div class="new_audio" style="<?php echo ($i>=3) ? 'display:none' : ''; ?>"><?php _e('Add audio clip', 'enginethemes-child'); ?><span class="icon-plus"><i class="fa fa-plus"></i></span></div>
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
    <div class="form-group clearfix skill-control">
        <label><?php _e('Tags', 'enginethemes-child');?></label>
        <?php
$switch_skill = ae_get_option('switch_skill');
if (!$switch_skill) {
    ?>
            <input type="text" class="form-control text-field skill" id="skill" placeholder="<?php _e("Enter job tags", 'enginethemes-child');?>" name=""  autocomplete="off" spellcheck="false" >
            <ul class="skills-list" id="skills_list"></ul>
            <?php
} else {
    ae_tax_dropdown('skill', array('attr' => 'data-chosen-width="100%" data-chosen-disable-search="" multiple data-placeholder="' . __(" Skills (max is 5)", 'enginethemes-child') . '"',
        'class' => 'sw_skill chosen multi-tax-item tax-item required',
        'hide_empty' => false,
        'hierarchical' => true,
        'id' => 'skill',
        'show_option_all' => false,
    )
    );
}

?>
    </div>
    <script type="text/template" id="openingMessageTemplate">
        <div class="box-shadow opening-message">
            <div class="aside-title">
                <?php _e('Opening Message', 'enginethemes-child')?> <i class="fa fa-question-circle popover-opening-message" style="cursor: pointer" aria-hidden="true"></i>
            </div>
            <div class="content">
                <div class="content-opening-message">

                </div>
                <a class="show-opening-message"></a>
            </div>
        </div>
    </script>
    <div class="form-group skill-control">
        <label for="opening_message"><?php _e('Opening message', 'enginethemes-child')?> <i class="fa fa-question-circle popover-opening-message" aria-hidden="true"></i></label>
            <p class="note-message">
                <?php _e('Opening message is automatically displayed as your first message in the order detail page.', 'enginethemes-child');?>
            </p>
        <textarea name="opening_message" class="input-item"></textarea>
    </div>
    <div class="form-group">
        <button class="<?php mje_button_classes(array('btn-save'));?>" type="submit"><?php _e('SAVE', 'enginethemes-child');?></button>
        <a href="#" class="btn-discard mjob-discard-action"><?php _e('DISCARD', 'enginethemes-child');?></a>
        <input type="hidden" class="input-item post-service_nonce" name="_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync'); ?>" />
    </div>
</form>