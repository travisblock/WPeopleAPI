<?php

namespace Bims\Http;
use Bims\Core\WPeopleAPI;
use Bims\Http\Response;

class WPeopleAPIRest extends \WP_REST_Controller
{
    protected $my_namespace = 'wpeopleapi/v';
    protected $my_version = '1';

    /**
     * Setting your own key
     * @todo add key dinamicly via wordpress setting
     */
    private $key = '123reverse321';

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
        $header = getallheaders();
        if (isset($header['x-WPeopleAPI-key']) && $header['x-WPeopleAPI-key'] == $this->key) {
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
        $people = new WPeopleAPI();
        $name   = $request['name'];
        $phone  = $request['phone'];
        $email  = $request['email'];
        $photo  = $request['photo'];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->message = 'Email not valid';
            return Response::set('Email not valid');
        }

        if (isset($name, $phone, $email) && !empty($photo)) {
            $store =  $people->store($name, $phone, $email);
            $people->updateContactPhoto($store->resourceName, $photo);
            return $store;
        }

        if (isset($name, $phone, $email)) {
            return $people->store($name, $phone, $email);
        }

       return Response::set('Form tidak valid');
    }

    public function listContact(WP_REST_Request $request)
    {
        $people = new WPeopleAPI();
        return $people->lists();
    }

}