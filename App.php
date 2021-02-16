<?php
defined('ABSPATH') or exit('Silence is golden');

/* Plugin Name: WPeopleAPI
 * Plugin URI: https://github.com/ajid2/WPeopleAPI
 * Description: Create contect in google contact using Google PeopleAPI.
 * Version: 1
 * Author: PT. Bimasakti
 * Author URI: https://ptbimasakti.com/
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 **/

require_once dirname(__FILE__) . '/vendor/autoload.php';
use Bims\Core\WPeopleAPI;
use Bims\WPeopleAPISetting;
use Bims\Http\WPeopleAPIRest;


// init rest server
$rest_server = new WPeopleAPIRest();
$rest_server->hookRestServer();

// admin
$people = new WPeopleAPI();
if (is_admin()) {
    $setting = new WPeopleAPISetting();
    if (!empty($_POST['wpeopleapi_setting_option_name']['the_client_id']) && !empty($_POST['wpeopleapi_setting_option_name']['the_client_secret'])) {
        $people->storeClientSecret($_POST['wpeopleapi_setting_option_name']['the_client_id'], $_POST['wpeopleapi_setting_option_name']['the_client_secret']);
    }

    $people->setBaseUrl();
    $setting->setBaseUrl($people->getBaseUrl());
    $setting->showBaseUrl();

    if ($people->isValidFile()) {
        if ($people->getClient()) {
            $setting->setAuthorizer($people->getClient()->createAuthUrl());
        }
        
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

    $setting->authorizer();
}


// guest
if ($people->isValidFile()) {
    if ($people->getToken()) {
        if (!$people->tokenInfo($people->getToken()->access_token)) {
            $refresh = $people->getClient()->refreshToken($people->getToken()->refresh_token);
            $people->storeJsonToken($refresh);
        }
    }
}