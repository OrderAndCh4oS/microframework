<?php
namespace functions\csrf;

	class CSRF {

		public static function generate() {
			return $_SESSION['csrf'] = base64_encode(openssl_random_pseudo_bytes(32));
		}

		public static function check($csrf) {
			if(isset($_SESSION['csrf']) && $csrf === $_SESSION['csrf']) {
				unset($_SESSION['csrf']);
				return true;
			}
			return false;
		}

	}