<?php

namespace Bims;

use Bims\Encryptor;

class WPeopleAPISetting
{
    private $wpeopleapi_setting_options,
        $key = 'BWpeople',
        $authorizer = false,
        $tokenInfo = false;

    public function __construct($authorizer = false)
    {
        if ($authorizer) {
            $this->authorizer = $authorizer;
        }

        add_action('admin_menu', array($this, 'wpeopleapi_setting_add_plugin_page'));
        add_action('admin_init', array($this, 'wpeopleapi_setting_page_init'));
    }

    public function wpeopleapi_setting_add_plugin_page()
    {
        add_options_page(
            'WPeopleAPI Setting', // page_title
            'WPeopleAPI Setting', // menu_title
            'manage_options', // capability
            'wpeopleapi-setting', // menu_slug
            array($this, 'wpeopleapi_setting_create_admin_page') // function
        );
    }

    public function wpeopleapi_setting_create_admin_page()
    {
        $this->wpeopleapi_setting_options = get_option('wpeopleapi_setting_option_name'); ?>

        <div class="wrap">
            <h2>WPeopleAPI Setting</h2>
            <p>Here to setting your WPeopleAPI</p>
            <?php settings_errors(); ?>

            <form method="post" action="options-general.php?page=wpeopleapi-setting">
                <?php
                settings_fields('wpeopleapi_setting_option_group');
                do_settings_sections('wpeopleapi-setting-admin');
                submit_button();
                ?>
            </form>
        </div>
<?php }

    public function wpeopleapi_setting_page_init()
    {
        register_setting(
            'wpeopleapi_setting_option_group', // option_group
            'wpeopleapi_setting_option_name', // option_name
            array($this, 'wpeopleapi_setting_sanitize') // sanitize_callback
        );

        add_settings_section(
            'wpeopleapi_setting_setting_section', // id
            'Settings', // title
            array($this, 'wpeopleapi_setting_section_info'), // callback
            'wpeopleapi-setting-admin' // page
        );

        add_settings_field(
            'the_client_id', // id
            'Client Id', // title
            array($this, 'the_client_id_callback'), // callback
            'wpeopleapi-setting-admin', // page
            'wpeopleapi_setting_setting_section' // section
        );

        add_settings_field(
            'the_client_secret', // id
            'Client Secret', // title
            array($this, 'the_client_secret_callback'), // callback
            'wpeopleapi-setting-admin', // page
            'wpeopleapi_setting_setting_section' // section
        );
    }

    public function wpeopleapi_setting_sanitize($input)
    {
        $sanitary_values = array();
        if (isset($input['the_client_id'])) {
            $sanitary_values['the_client_id'] = Encryptor::encrypt($input['the_client_id'], $this->key);
        }

        if (isset($input['the_client_secret'])) {
            $sanitary_values['the_client_secret'] = Encryptor::encrypt($input['the_client_secret'], $this->key);
        }

        if (isset($input['authorize_2'])) {
            $sanitary_values['authorize_2'] = sanitize_text_field($input['authorize_2']);
        }

        return $sanitary_values;
    }

    public function wpeopleapi_setting_section_info()
    {
    }

    public function the_client_id_callback()
    {
        printf(
            '<input class="regular-text" type="text" name="wpeopleapi_setting_option_name[the_client_id]" id="the_client_id" value="%s">',
            isset($this->wpeopleapi_setting_options['the_client_id']) ? esc_attr($this->wpeopleapi_setting_options['the_client_id']) : ''
        );
    }

    public function the_client_secret_callback()
    {
        printf(
            '<input class="regular-text" type="password" name="wpeopleapi_setting_option_name[the_client_secret]" id="the_client_secret" value="%s">',
            isset($this->wpeopleapi_setting_options['the_client_secret']) ? esc_attr($this->wpeopleapi_setting_options['the_client_secret']) : ''
        );
    }

    public function setAuthorizer($authorizer)
    {
        return $this->authorizer = $authorizer;
    }

    public function setTokenInfo($tokenInfo)
    {
        return $this->tokenInfo = $tokenInfo;
    }

    public function authorizer()
    {
        $that = &$this;
        add_action('admin_init', function () use ($that) {
            add_settings_field(
                'authorize_2', // id
                'Authorization', // title
                array($that, 'authorizer_callback'), // callback
                'wpeopleapi-setting-admin', // page
                'wpeopleapi_setting_setting_section' // section
            );
        });
    }

    public function authorizer_callback()
    {
        if ($this->authorizer) {
            if ($this->tokenInfo) {
                echo '<div>Authorized as ' . $this->tokenInfo->email . '<a class="button" style="vertical-align: middle;margin-left: 10px;background: #DC3232;border:none;color:#fff" href="?page=wpeopleapi-setting&removeAuthWPeopleAPI=true">Remove</a></div>';
                return;
            }
            echo '<a target="_blank" class="button button-primary" href="' . $this->authorizer . '">Authorize</a>';
            return;
        } else {
            echo "Not available";
            return false;
        }
    }
}
