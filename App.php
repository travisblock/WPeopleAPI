<?php
defined('ABSPATH') or exit('Silence is golden');

/* Plugin Name: WPeopleAPI
 * Plugin URI: http://api.com
 * Description: Create contect in google contact using Google PeopleAPI.
 * Version: 0.1.2
 * Author: KomsBims
 * Author URI: https://api.com
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 **/

require_once 'vendor/autoload.php';

use Bims\WPeopleAPI;
use Bims\WPeopleAPISetting;

class InitClass
{
    public function __construct()
    {
        if (!is_admin()) {
            return false;
        }
    }
}


$people = new WPeopleAPI();
if (is_admin()) {
    $setting = new WPeopleAPISetting();
    if (!empty($_POST['wpeopleapi_setting_option_name']['the_client_id']) && !empty($_POST['wpeopleapi_setting_option_name']['the_client_secret'])) {
        $people->storeClientSecret($_POST['wpeopleapi_setting_option_name']['the_client_id'], $_POST['wpeopleapi_setting_option_name']['the_client_secret']);
    }

    $people->setBaseUrl();
    $setting->setBaseUrl($people->getBaseUrl());
    $setting->showBaseUrl();

    if (is_file(dirname(__FILE__) . '/' . $people->getClientSecret())) {
        $setting->setAuthorizer($people->getClient()->createAuthUrl());


        if ($people->getToken()) {
            if ($people->tokenInfo($people->getToken()->access_token)) {
                $setting->setTokenInfo($people->tokenInfo($people->getToken()->access_token));
            } else {
                $refresh = $people->getClient()->refreshToken($people->getToken()->refresh_token);
                $people->storeJsonToken($refresh);
            }
        }

        if (isset($_GET['code'])) {
            $people->storeToken($_GET['code']);
        }

        if (isset($_GET['removeAuthWPeopleAPI'])) {
            $people->removeAuthorization();
        }
    }
    // $people->setBaseUrl();
    // var_dump($people->getBaseUrl());

    $setting->authorizer();
}

if (is_file(dirname(__FILE__) . '/' . $people->getClientSecret())) {
    if ($people->getToken()) {
        if (!$people->tokenInfo($people->getToken()->access_token)) {
            $refresh = $people->getClient()->refreshToken($people->getToken()->refresh_token);
            $people->storeJsonToken($refresh);
        }

        if (isset($_GET['wpeopleapi']) && $_GET['wpeopleapi'] == 'create') {
            $name   = $_GET['name'];
            $phone  = $_GET['phone'];
            $email  = $_GET['email'];
            if (isset($name) && isset($phone) && isset($email)) {
                $people->store($name, $phone, $email);
            }
            // if ($_SERVER['REQUEST_METHOD'] == 'post') {
            //     $name   = $_POST['name'];
            //     $phone  = $_POST['phone'];
            //     $email  = $_POST['email'];
            //     if (isset($name) && isset($phone) && isset($email)) {
            //         $people->store($name, $phone, $email);
            //     }
            // }
        }
    }
}



// add_action('init', 'wpse26388_rewrites_init');
// function wpse26388_rewrites_init()
// {
//     add_rewrite_rule(
//         'properties/([0-9]+)/?$',
//         'index.php?pagename=properties&property_id=$matches[1]',
//         'top'
//     );
// }

// add_filter('query_vars', 'wpse26388_query_vars');
// function wpse26388_query_vars($query_vars)
// {
//     $query_vars[] = 'property_id';
//     echo "<pre>";
//     var_dump($query_vars);
//     die();
//     return $query_vars;
// }

// add_action('init',  function () {
//     add_rewrite_rule('^wpeopleapi/([0-9]+)/?', 'index.php?page_id=$matches[1]', 'top');
// });

// add_filter('query_vars', function ($query_vars) {
//     $query_vars[] = 'wpeopleapi';
//     return $query_vars;
// });


// add_action('template_include', function ($template) {
//     var_dump(get_query_var('wpeopleapi'));
//     die;
//     if (get_query_var('wpeopleapi') == false || get_query_var('wpeopleapi') == '') {
//         return $template;
//     }
//     echo "A";
//     die;
// });
