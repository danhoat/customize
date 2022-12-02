<?php
function cs_custom_email_template(){

    $logo_url = get_template_directory_uri() . "/img/logo-de.png";
    $options = AE_Options::get_instance();

    // save this setting to theme options
    $site_logo = $options->site_logo;
    if (!empty($site_logo)) {
        $logo_url = $site_logo['large'][0];
    }

    $logo_url = apply_filters('ae_mail_logo_url', $logo_url);

    $customize = et_get_customization();

    $mail_header = '<html>
                    <head>
                    </head>
                    <body style="font-family: Arial, sans-serif;font-size: 0.9em;margin: 0; padding: 0; color: #222222;">
                    <div style="margin: 0px auto; width:600px; border: 1px solid ' . $customize['background'] . '">
                        <table width="100%" cellspacing="0" cellpadding="0">

                        <tr>
                            <td colspan="2" style="background: #ffffff; color: #222222; line-height: 18px; padding: 10px 20px;">';
    return $mail_header;
}
add_filter('ae_get_mail_header','cs_custom_email_template');