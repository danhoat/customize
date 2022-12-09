<?php
// Remove powered by Microjob Themes in the footer page

require_once("custom_mail.php");

function custom_copyright($copyright){
	$copyright = get_theme_mod('site_copyright');
	return $copyright;
}
add_filter('ae_attribution_footer','custom_copyright');

// Remove powered by Microjob Themes in the footer mail and get footer of the site
function custom_footer_mail_template(){
	$copyright = get_theme_mod('site_copyright');
    $customize = et_get_customization();
	$info = apply_filters('ae_mail_footer_contact_info', get_option('blogname') . ' <br>
                        ' . get_option('admin_email') . ' <br>');
	$mail_footer = '</td>
		</tr>
		<tr>
			<td colspan="2" style="background: ' . $customize['background'] . '; padding: 10px 20px; color: #666;">
				<table width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td style="vertical-align: top; text-align: left; width: 50%;">' . $copyright . '</td>
						<td style="text-align: right; width: 50%;">' . $info . '</td>
					</tr>
				</table>
			</td>
		</tr>
		</table>
		</div>
		</body>
		</html>';
    return $mail_footer;
}
add_filter('ae_get_mail_footer','custom_footer_mail_template');

/**
* @author Abubakar Wazih Tushar<tushar.abubakar@gmail.com>
* @version 1.0
* Install child theme textdomain
*/
function child_theme_slug_setup() {
    load_child_theme_textdomain( 'enginethemes-child', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'child_theme_slug_setup' );

/**
* @author Abubakar Wazih Tushar<tushar.abubakar@gmail.com>
* @version 1.0
* Add audio option in post job page
*/
function encodeURIComponent($str) {
    $revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
    return strtr(rawurlencode($str), $revert);
}

/**
 * Decode URI
 *
 * @access  public
 * @since   1.0
 */
function decodeURIComponent($str) {
    $str = preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($str));
    return html_entity_decode($str,null,'UTF-8');
}

add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_style( 'style-main', get_stylesheet_directory_uri() . '/assets/css/style.css', array(), rand(), 'all' );
	wp_deregister_script( 'js-cookie' );
	wp_dequeue_script( 'js-cookie' );
	wp_enqueue_script( 'js-cookie', get_stylesheet_directory_uri() . '/assets/js/js_cookie.min.js', array(), '1.1', true );

    if( is_singular('mjob_post') || is_page_template('page-process-payment.php')) {
        wp_enqueue_style( 'cssmCustomScrollbar', get_stylesheet_directory_uri() . '/assets/css/jquery.mCustomScrollbar.css', array(), '1.1', 'all' );
        wp_enqueue_style( 'player', get_stylesheet_directory_uri() . '/assets/css/player.css', array(), '1.1', 'all' );
        wp_enqueue_script( 'jsmCustomScrollbar', get_stylesheet_directory_uri() . '/assets/js/jquery.mCustomScrollbar.concat.min.js', array('jquery'), '1.1', true );
        wp_enqueue_script( 'jsmediatags', get_stylesheet_directory_uri() . '/assets/js/jsmediatags.min.js', array('jquery'), '1.1', true );
        wp_enqueue_script( 'radio', get_stylesheet_directory_uri() . '/assets/js/radio.js', array('jquery'), '1.1', true );
        wp_enqueue_script( 'player', get_stylesheet_directory_uri() . '/assets/js/player.js', array('jquery'), '1.1', true );
        wp_deregister_script( 'single-mjob' );
        wp_dequeue_script( 'single-mjob' );
        wp_enqueue_script( 'single-mjob', get_stylesheet_directory_uri() . '/assets/js/single-mjob.js', array(), '1.1', true );
    }

    if( is_mje_submit_page()) {
        wp_deregister_script( 'post-service' );
        wp_dequeue_script( 'post-service' );
        wp_enqueue_script( 'post-service', get_stylesheet_directory_uri() . '/assets/js/post-service.js', array(), '1.1', true );
    }

    if(is_page_template('page-payment-method.php')) {
        wp_deregister_script( 'payment-method' );
        wp_dequeue_script( 'payment-method' );
        wp_enqueue_script( 'payment-method', get_stylesheet_directory_uri() . '/assets/js/payment-method.js', array(), '1.1', true );
    }

    wp_enqueue_script( 'microjob-child-script', get_stylesheet_directory_uri() . '/assets/js/script.js', array('jquery', 'wp-i18n'), '1.1', true );

	wp_set_script_translations( 'microjob-child-script', 'enginethemes-child', get_stylesheet_directory_uri() . 'languages' );

	$child_js_vars = array(
		"home_url" => home_url(),
		"upload_url" => admin_url('async-upload.php'),
		"ajax_url" => admin_url( 'admin-ajax.php' ),
		"nonce" => wp_create_nonce('media-form'),
        "childdir" => get_stylesheet_directory_uri()
	);
	wp_localize_script( 'microjob-child-script', 'microjob', $child_js_vars );
});

add_filter('mjob_post_meta_fields', function($meta_fields) {
    array_push($meta_fields, "is_edit", "audio_clip_0", "audio_clip_1", "audio_clip_2");
    return $meta_fields;
}, 20, 1);

function microjob_check_link() {
    if ( isset($_POST['url']) ) {
        $content = file_get_contents($_POST['url']);
        try {
            $rss = new SimpleXmlElement($content);
            if ( isset($rss->channel->item) && $rss->channel->item->count() > 0 ) {
                echo "podcast";
            } else {
                echo "error";
            }
        }catch(Exception $e){
            $validext = ['mp3', 'wav', 'acc'];
            $info = pathinfo($_POST['url']);
            if ( isset($info['extension']) && in_array($info['extension'], $validext) ) {
                echo "external";
            } else {
                echo "error";
            }
        }
    } else {
        echo "error";
    }
    wp_die();
}
add_action( 'wp_ajax_nopriv_microjob_check_link', 'microjob_check_link' );
add_action( 'wp_ajax_microjob_check_link', 'microjob_check_link' );

function microjob_upload_audio() {
    if ( isset($_FILES['audio_clip'], $_POST['name']) ) {
        $getit = false;
        $sizes = isset( $_POST['filesize'] ) ? $_POST['filesize'] : 0;
        $name = preg_replace('/\s+/', '-', trim($_POST['name']));
        $title = !empty($name) ? pathinfo($name, PATHINFO_FILENAME) : '';
        $atch_id = get_page_by_title( $title, OBJECT, 'attachment');
        if ( isset($atch_id->ID) && !empty($sizes) && !empty($title) ) {
            $filesize = isset(wp_get_attachment_metadata($atch_id->ID)['filesize']) ? wp_get_attachment_metadata($atch_id->ID)['filesize'] : 0 ;
            $max = $filesize+100;
            $min = $filesize-100;
            if ( ($sizes == $filesize) || (($min <= $sizes) && ($sizes <= $max)) ) {
                $getit = true;
                echo $atch_id->ID;
            }
        }
        if ( !$getit ) {
            require_once(ABSPATH . "wp-admin/includes/admin.php");
            $file_return = wp_handle_upload($_FILES['audio_clip'], array('test_form' => false));
            if( isset( $file_return['error'] ) || isset( $file_return['upload_error_handler'] ) ) {
                echo "error";
            } else {
                $filename = $file_return['file'];
                $attachment = array(
                    'post_mime_type' => $file_return['type'],
                    'post_title' => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
                    'post_content' => '',
                    'post_status' => 'inherit',
                    'guid' => $file_return['url']
                );

                $attachment_id = wp_insert_attachment( $attachment, $file_return['url'] );
                require_once(ABSPATH . "wp-admin/includes/file.php");
                require_once(ABSPATH . "wp-admin/includes/media.php");
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                $attachment_data = wp_generate_attachment_metadata( $attachment_id, $filename );
                wp_update_attachment_metadata( $attachment_id, $attachment_data );
                if( 0 < intval( $attachment_id ) ) {
                    echo $attachment_id;
                }
            }
        }
    } else {
        echo "error";
    }
    wp_die();
}
add_action( 'wp_ajax_nopriv_microjob_upload_audio', 'microjob_upload_audio' );
add_action( 'wp_ajax_microjob_upload_audio', 'microjob_upload_audio' );

function microjob_delete_audio() {
    if ( isset($_POST['id']) && $_POST['id'] > 0 ) {
        $pid = (int) $_POST['id'];
        $post_data = get_post($pid, ARRAY_A);
        $author_id = $post_data['post_author'];
        if( get_current_user_id() == $author_id ) {
            $args = array(
                'post_type'  => 'mjob_post',
                'meta_query' => array(
                    'relation' => 'OR',
                    array(
                        'key'     => 'audio_clip_0',
                        'value'   => $pid,
                    ),
                    array(
                        'key'     => 'audio_clip_1',
                        'value'   => $pid,
                    ),
                    array(
                        'key'     => 'audio_clip_2',
                        'value'   => $pid,
                    )
                ),
            );
            $query = new WP_Query( $args );
            if ( !($query->have_posts()) ) {
                wp_delete_attachment($pid, true);
            }
        }
    }
    wp_die();
}
add_action( 'wp_ajax_nopriv_microjob_delete_audio', 'microjob_delete_audio' );
add_action( 'wp_ajax_microjob_delete_audio', 'microjob_delete_audio' );

/**
* Zelle withdraw method
* @author Abubakar Wazih Tushar<tushar.abubakar@gmail.com>
**/
add_filter('wp_privacy_personal_data_exporters', function( $exporters ) {
    $exporters[] = array(
        'exporter_friendly_name' =>  'MicrojobEngine Data',
        'callback'               => 'fre_personal_data_exporter_child',
    );
    $exporters[] = array(
        'exporter_friendly_name' =>  'Your Mjob Posted',
        'callback'               => 'mje_mjob_posted_exporter',
    );
    $exporters[] = array(
        'exporter_friendly_name' =>  'Your Order',
        'callback'               => 'mje_mjob_order_exporter',
    );
    $exporters[] = array(
        'exporter_friendly_name' =>  'Your Recruits',
        'callback'               => 'mje_recruit_exporter',
    );

    return $exporters;
}, 20, 1);

function fre_personal_data_exporter_child( $email_address, $page = 1 ) {
    $export_items = array();
    $user = get_user_by( 'email', $email_address );
    if ( $user && $user->ID ) {

        // Plugins can add as many items in the item data array as they want
        $data = array();
        $role = ae_user_role($user->ID);

        global $wp_query, $ae_post_factory, $post;
        $profile_obj = $ae_post_factory->get('mjob_profile');
        $profile_id = get_user_meta( $user->ID, 'user_profile_id', true );
        $profile = get_post($profile_id);
        $convert    = $profile_obj->convert( $profile );

        $description = !empty($convert->profile_description) ? $convert->profile_description : "";
        $display_name = isset($user_data->display_name) ? $user_data->display_name : '';
        $country_name = isset($convert->tax_input['country'][0]) ? $convert->tax_input['country'][0]->name : '';
        $languages = isset($convert->tax_input['language']) ? $convert->tax_input['language'] : '';

        if ( ! empty( $country_name ) ) {
            $data[] = array(
                'name'  => __( 'Country:', 'enginethemes' ),
                'value' => $country_name
            );
        }
        if ( ! empty( $languages) ) {
            $langs = array();
            if( ! empty($languages) ) {
                foreach($languages as $language) {
                    $langs[] = $language->name;
                }
            }
            if( !empty( $langs ) ){
                $lns =  implode(",", $langs);
                $data[] = array(
                    'name'  => __( 'Languages:', 'enginethemes' ),
                    'value' => $lns
                );
            }
        }
        $data[] = array(
            'name'  => __( 'Profile ID', 'enginethemes' ),
            'value' => $profile_id
        );
        $data[] = array(
            'name'  => __( 'URL', 'enginethemes' ),
            'value' =>get_author_posts_url($user->ID)
        );
        $data[] = array(
            'name'  => __( 'Payment info:', 'enginethemes' ),
            'value' => $convert->payment_info
        );
        $data[] = array(
            'name'  => __( 'Billing full name:', 'enginethemes' ),
            'value' => $convert->billing_full_name
        );
        $data[] = array(
            'name'  => __( 'Billing Full Address :', 'enginethemes' ),
            'value' => $convert->billing_full_address
        );
        $data[] = array(
            'name'  => __( 'Billing Country:', 'enginethemes' ),
            'value' => $convert->billing_country
        );
        $data[] = array(
            'name'  => __( 'Billing VAT:', 'enginethemes' ),
            'value' => $convert->billing_vat
        );

        $data[] = array(
            'name'  => __( 'Overview:', 'enginethemes' ),
            'value' => $description
        );
        $user_obj = mJobUser::getInstance();
        $user_data = $user_obj->convert($user);

        // Bank account data
        $bank_first_name = isset($user_data->payment_info['bank']) ? $user_data->payment_info['bank']['first_name'] : '';
        $bank_middle_name = isset($user_data->payment_info['bank']) ? $user_data->payment_info['bank']['middle_name'] : '';
        $bank_last_name = isset($user_data->payment_info['bank']) ? $user_data->payment_info['bank']['last_name'] : '';
        $bank_name = isset($user_data->payment_info['bank']) ? $user_data->payment_info['bank']['name'] : '';
        $bank_swift_code = isset($user_data->payment_info['bank']) ? $user_data->payment_info['bank']['swift_code'] : '';
        $bank_account_no = isset($user_data->payment_info['bank']) ? $user_data->payment_info['bank']['account_no'] : '';

        // Zelle account data
        $zelle_email = isset($user_data->payment_info['zelle']) ? $user_data->payment_info['zelle'] : '';
        // Paypal account data
        $paypal_email = isset($user_data->payment_info['paypal']) ? $user_data->payment_info['paypal'] : '';
        $data[] = array(
            'name'  => __( 'Bank First Name:', 'enginethemes' ),
            'value' => $bank_first_name
        );
        $data[] = array(
            'name'  => __( 'Bank Middle Name:', 'enginethemes' ),
            'value' => $bank_middle_name
        );
        $data[] = array(
            'name'  => __( 'Bank Last Name:', 'enginethemes' ),
            'value' => $bank_last_name
        );
        $data[] = array(
            'name'  => __( 'Bank Name:', 'enginethemes' ),
            'value' => $bank_name
        );
        $data[] = array(
            'name'  => __( 'Bank Swift Name:', 'enginethemes' ),
            'value' => $bank_swift_code
        );
        $data[] = array(
            'name'  => __( 'Bank Account No:', 'enginethemes' ),
            'value' => $bank_account_no
        );
        $data[] = array(
            'name'  => __( 'PayPal Email:', 'enginethemes' ),
            'value' => $paypal_email
        );
        $data[] = array(
            'name'  => __( 'Zelle Email:', 'enginethemes' ),
            'value' => $zelle_email
        );



        // Add this group of items to the exporters data array.
        $item_id = "mje-info-{$user->ID}";
        $export_items[] = array(
            'group_id'    => 'fre_data',
            'group_label' => 'MJE Data',
            'item_id'     => $item_id,
            'data'        => $data,
        );


    }
    // Returns an array of exported items for this pass, but also a boolean whether this exporter is finished.
    //If not it will be called again with $page increased by 1.
    return array(
        'data' => $export_items,
        'done' => true,
    );
}

add_action( 'wp_loaded', function(){
    class mJobUserAction_child extends AE_UserAction {
        public static $instance;


        public static function getInstance() {
            if(!self::$instance) {
                self::$instance = new self();
            }

            return self::$instance;
        }


        public function __construct() {
            $user = new mJobUser();
            parent::__construct($user);

            remove_action( 'wp_ajax_nopriv_mjob_sync_user', array( 'mJobUserAction', 'mJobUserSync' ) );
            remove_action( 'wp_ajax_mjob_sync_user', array( 'mJobUserAction', 'mJobUserSync' ) );
            if( is_user_logged_in() ){
                $this->add_ajax('mjob_sync_user', 'mJobUserSync_child', true, true, 9);
            }
        }


        public function mJobUserSync_child() {
            global $current_user;

            if(!mje_is_user_active($current_user->ID)) {
                wp_send_json(array(
                    'success' => false,
                    'msg' => __('Your account is pending. You have to activate your account to continue this step.', 'enginethemes')
                ));
            }

            $request = $_REQUEST;
            $result = array();
            if(isset($request['do_action']) && !empty($request['do_action'])) {
                switch ($request['do_action']) {
                    case 'check_email':
                        // $result = $this->validateEmail($request['user_email']);
                        // wp_send_json($result);
                        break;

                    case 'update_payment_method':
                        $payment_info = get_user_meta($current_user->ID, 'payment_info', true);
                        if(empty($payment_info)) {
                            $payment_info = array();
                        }

                        if(isset($request['zelle_email'])) {
                            $payment_info['zelle'] = strip_tags($request['zelle_email']);
                            update_user_meta($current_user->ID, 'payment_info', $payment_info);
                        } else if(isset($request['paypal_email'])) {
                            $payment_info['paypal'] = strip_tags($request['paypal_email']);
                            update_user_meta($current_user->ID, 'payment_info', $payment_info);
                        } else if(isset($request['bank_account_no'])) {
                            $payment_info['bank'] = array (
                                'first_name' => strip_tags($request['bank_first_name']),
                                'middle_name' => strip_tags($request['bank_middle_name']),
                                'last_name' => strip_tags($request['bank_last_name']),
                                'name' => strip_tags($request['bank_name']),
                                'swift_code' => strip_tags($request['bank_swift_code']),
                                'account_no' => strip_tags($request['bank_account_no'])
                            );
                            update_user_meta($current_user->ID, 'payment_info', $payment_info);
                        }
                        break;
                }
            }

            parent::sync();
        }
    }
    $new_instance = mJobUserAction_child::getInstance();
}, 11);

add_filter('mje_render_payment_name', function($payment_name){
    $child = get_stylesheet_directory_uri() . '/assets/img';
    $parent = get_template_directory_uri() . '/assets/img';
    $payments = array(
        'paypal' => __('PayPal', 'enginethemes'),
        'zelle' => __('Zelle', 'enginethemes'),
        'cash' => __('Cash', 'enginethemes'),
        'credit' => __('Credit', 'enginethemes'),
        '2checkout' => __('2Checkout', 'enginethemes'),
        'bank' => __('Bank', 'enginethemes'),
    );
    $payment_name = array();
    foreach ($payments as $key => $name) {
        if ( $key == 'zelle' ) {
            $icon_path = "{$child}/card-{$key}.svg";
        } else {
            $icon_path = "{$parent}/card-{$key}.svg";
        }
        $payment_name[$key] = "<p class='payment-name {$key}' title='{$name}'><img src='{$icon_path}'/><span>{$name}</span></p>";
    }

    return $payment_name;
}, 10, 1);

function list_audio_show($listhtml, $au_pos){?>
<div class="item <?php echo ($au_pos==0) ? 'active' : ''; ?> item-audio">
                <div id="audio_player">
                    <div id="hap-wrapper" class="hap-brona-light">
                        <div class="hap-player-outer">
                            <div class="hap-player-holder">
                                <div class="hap-player-thumb-wrapper">
                                    <div class="hap-player-thumb"></div>
                                </div>
                                <div class="hap-player-right">
                                    <div class="hap-center-elements">
                                        <div class="hap-info">
                                            <div class="hap-player-title"></div>
                                            <div class="hap-player-artist"></div>
                                        </div>
                                        <div class="hap-seekbar-wrap">
                                            <div class="hap-media-time">
                                                <div class="hap-media-time-current">0:00</div>
                                                <div class="hap-media-time-total">0:00</div>
                                            </div>
                                            <div class="hap-seekbar">
                                                <div class="hap-progress-bg">
                                                    <div class="hap-load-level">
                                                        <div class="hap-progress-level"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="hap-controls">
                                            <div class="hap-controls-left">
                                                <div class="hap-prev-toggle hap-contr-btn" data-tooltip="Previous">
                                                    <i class="material-icons">skip_previous</i>
                                                </div>
                                                <div class="hap-playback-toggle hap-contr-btn">
                                                    <div class="hap-btn hap-btn-play" data-tooltip="Play">
                                                        <i class="material-icons">play_arrow</i>
                                                    </div>
                                                    <div class="hap-btn hap-btn-pause" data-tooltip="Pause">
                                                        <i class="material-icons">pause</i>
                                                    </div>
                                                </div>
                                                <div class="hap-next-toggle hap-contr-btn" data-tooltip="Next">
                                                    <i class="material-icons">skip_next</i>
                                                </div>
                                                <div class="hap-volume-wrap hap-contr-btn">
                                                    <div class="hap-volume-toggle hap-contr-btn" data-tooltip="Volume">
                                                        <div class="hap-btn hap-btn-volume-up">
                                                            <i class="material-icons">volume_up</i>
                                                        </div>
                                                        <div class="hap-btn hap-btn-volume-down">
                                                            <i class="material-icons">volume_down</i>
                                                        </div>
                                                        <div class="hap-btn hap-btn-volume-off">
                                                            <i class="material-icons">volume_off</i>
                                                        </div>
                                                    </div>
                                                    <div class="hap-volume-seekbar">
                                                        <div class="hap-volume-bg">
                                                            <div class="hap-volume-level"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="hap-controls-right">
                                                <div class="hap-random-toggle hap-contr-btn">
                                                    <div class="hap-btn hap-btn-random-off" data-tooltip="Shuffle off">
                                                        <i class="material-icons">shuffle</i>
                                                    </div>
                                                    <div class="hap-btn hap-btn-random-on hap-contr-btn-hover" data-tooltip="Shuffle on">
                                                        <i class="material-icons">shuffle</i>
                                                    </div>
                                                </div>
                                                <div class="hap-loop-toggle hap-contr-btn">
                                                    <div class="hap-btn hap-btn-loop-playlist hap-contr-btn-hover" data-tooltip="Loop playlist">
                                                        <i class="material-icons">repeat</i>
                                                    </div>
                                                    <div class="hap-btn hap-btn-loop-single hap-contr-btn-hover" data-tooltip="Loop single">
                                                        <i class="material-icons">repeat_one</i>
                                                    </div>
                                                    <div class="hap-btn hap-btn-loop-off" data-tooltip="Loop off">
                                                        <i class="material-icons">repeat</i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="hap-playlist-holder">
                                <div class="hap-playlist-filter-msg"><span><?php _e('NOTHING FOUND!', 'enginethemes-child'); ?></span></div>
                                <div class="hap-playlist-inner">
                                    <div class="hap-playlist-content"></div>
                                </div>
                            </div>
                            <div class="hap-tooltip"></div>
                        </div>
                        <div class="hap-preloader"></div>
                    </div>
                    <div id="hap-playlist-list" style="display: none;">
                        <div id="playlist-mixed">
                            <?php echo $listhtml; ?>
                        </div>
                    </div>
                </div>
            </div>
<?php }?>

<?php 

require_once dirname(__FILE__) . '/firebase_custom.php';