<?php
namespace application\core;

use \application\traits\Singleton;

class Session {
    use Singleton;

    public function start() {
		session_start();
	}

	public function get($index) {
		return $_SESSION[$index] ?? null;
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
