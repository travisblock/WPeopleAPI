<?php

namespace Bims\Http;
use Bims\Core\WPeopleAPI;
use Bims\Http\Response;
use Bims\Helpers\Arr;

class WPeopleAPIRest extends \WP_REST_Controller
{
    protected $my_namespace = 'wpeopleapi/v';
    protected $my_version = '1';

    /**
     * Setting your own key
     * @todo add key dinamicly via wordpress setting
     */
    private $key = 'wpeopleapi';

    public function __construct()
    {
        $option = get_option('wpeopleapi_setting_option_name');
        if ($option && isset($option['authorization_token'])) {
            $this->key = $option['authorization_token'];
        }
    }

    public function register_routes()
    {
        $namespace = $this->my_namespace . $this->my_version;
        $base = 'contact';
        register_rest_route($namespace, '/' . $base, array(
            array(
                'methods'               => \WP_REST_Server::CREATABLE,
                'callback'              => array($this, 'createContact'),
                'permission_callback'   => array($this, 'getCreatePermission')
            ),
            array(
                'methods'               => \WP_REST_Server::READABLE,
                'callback'              => array($this, 'listContact'),
                'permission_callback'   => array($this, 'getListPermission')
            ),
        ));
    }

    public function hookRestServer()
    {
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    public function getCreatePermission()
    {
        
        $token = $this->getBearerToken();
        if ($token && $token == $this->key) {
            return true;
        }

        return new \WP_Error('rest_forbidden', esc_html__( 'Anda tidak punya akses kesini', 'my-text-domain' ), array('status' => 403));
    }

    public function getListPermission()
    {
        if (! current_user_can('edit_posts') ) {
            return new \WP_Error('rest_forbidden', esc_html__( 'Anda tidak punya akses kesini', 'my-text-domain' ), array('status' => 403));
        }

        return true;
    }

    public function createContact(\WP_REST_Request $request)
    {   
        $people         = new WPeopleAPI();
        $name           = $request['name'];
        $phone          = $request['phone'];
        $email          = $request['email'];
        $photo          = $request['photo'];
        $group          = ( isset($request['group']) ) ? $request['group'] : null;
        $address        = RestSupport::address($request);
        $birth          = ( isset($request['birthday']) ) ? Arr::dateToArray($request['birthday'])::result() : [];
        $events         = RestSupport::events($request);
        $urls           = ( is_array($request['urls']) ) ? Arr::arrToPipeArray($request['urls'], 'type,value')::result() : Arr::pipeToArray($request['urls'], 'type,value')::result();
        $custom         = ( is_array($request['custom']) ) ? Arr::arrToPipeArray($request['custom'], 'key,value')::result() : Arr::pipeToArray($request['custom'], 'key,value')::result();

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->message = 'Email not valid';
            return Response::set('Email not valid');
        }

        if (!isset($name, $phone, $email)) {
            return Response::set('Missing required parameter. see https://github.com/ajid2/WPeopleAPI');
        }

        if (isset($name, $phone, $email) && !empty($photo)) {
            $store =  $people->store($name, $phone, $email, $group, $address, $birth, $urls, $events, $custom);
            $people->updateContactPhoto($store->resourceName, $photo);
            return $store;
        }

        return $people->store($name, $phone, $email, $group, $address, $birth, $urls, $events, $custom);
    }

    public function listContact(WP_REST_Request $request)
    {
        $people = new WPeopleAPI();
        return $people->lists();
    }


    public function getAuthorizationHeader()
    {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }

    public function getBearerToken()
    {
        $headers = $this->getAuthorizationHeader();
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }
}

class RestSupport
{
    public static function address(\WP_REST_Request $request)
    {
        $address = [];
        if (is_array($request['address'])) {
            $address = $request['address'];
        } else{
            if (isset($request['addresscity'])) {
                $address['city'] = $request['addresscity'];
            }

            if (isset($request['addresscountry'])) {
                $address['country'] = $request['addresscountry'];
            }
        }

        return $address;
    }

    public static function events(\WP_REST_Request $request)
    {
        $events = [];
        if (is_array($request['events'])) {
            $events = $request['events'];
            if (!empty($events) && isset($events['date'])) {
                $events['date'] = Arr::dateToArray($request['events']['date'])::result();
            }
        }else{
            if (isset($request['eventstype'])) {
                $events['type'] = $request['eventstype'];
            }

            if (isset($request['eventsdate'])) {
                $events['date'] = Arr::dateToArray($request['eventsdate'])::result();
            }

        }
        
        return $events;
    }
}