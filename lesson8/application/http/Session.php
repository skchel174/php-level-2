<?php
namespace application\http;

use \application\traits\Singleton;

class Session {
    use Singleton;

    private $session = [];
    
    private function __construct() {
        session_start();
        $this->session = $_SESSION;
    }

	public function get($index = null) {
        if (is_null($index)) {
            return $this->session;
        }
        return $this->session[$index] ?? null;
	}

	public function set($index, $value) {
		$_SESSION[$index] = $value;
    }	
    
    public function unset($index) {
        unset($_SESSION[$index]);
    }

	public function destroy() {
		session_destroy();
	}
}
