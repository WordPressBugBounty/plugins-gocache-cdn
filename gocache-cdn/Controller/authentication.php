<?php

namespace GoCache;


if ( ! function_exists( 'add_action' ) ) {
	exit(0);
}

class Authentication_Controller
{
    private $request;

    public function __construct()
    {
        $this->request = new Request();
    }

    public function authentication() {

        $response = $this->request->verify_connection();
        $result = json_decode( $response['body'] );

        $message = 'Não foi póssível realizar a autenticação.';

        if ( $result->status_code == 1 ) {
            $message = 'Autenticação realizada com sucesso!';
        }
        $this->update_gocache_options( $result, $result->status_code );

        return [
            'status' => $result->status_code,
            'message' => $message
        ];
    }

    private function update_gocache_options( $response, $status ) {

        if ( $status == 1 ) {
            update_option( 'gocache_option-external_configs', $response );
        }
        update_option('gocache_option-status', $status );
    }

}
