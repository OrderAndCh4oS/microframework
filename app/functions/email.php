<?php
	namespace functions\email;

	require_once __DIR__ . '/../../../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';

	class Email {

		private $header_colour = '#000000';
		private $body_colour   = '#333333';

		private $h2_style;
		private $p_style;

		private $email_title;
		private $subject;
		private $message;

		public function __construct() {
			$this->setH2Style("font-family:Helvetica,Arial,sans-serif;font-weight:bold;font-size:14px;color:$this->header_colour;padding-bottom:0;margin-bottom:0;");
			$this->setPStyle("font-family:Helvetica,Arial,sans-serif;font-size:12px;color:$this->body_colour;");
		}


		public function check_required_fields($array) {
			$errors = array();
			foreach ( $array as $field_name ) {
				// check that required fields are set
				if ( ! isset( $_POST[ $field_name ] ) || ( empty( $_POST[ $field_name ] ) && $_POST[ $field_name ] != '0' ) ) {
					$errors[] = $field_name;
				}
			}
			return $errors;
		}

		private function clean( $data ) {
			return htmlentities( trim( $data ) );
		}

		/**
		 * @param mixed $h2_style
		 */
		public function setHeaderColour($h2_style) {
			$this->header_colour = $h2_style;
			$this->h2_style = "font-family:Helvetica,Arial,sans-serif;font-weight:bold;font-size:14px;color:$h2_style;padding-bottom:0;margin-bottom:0;";;
		}

		/**
		 * @param mixed $p_style
		 */
		public function setBodyColour($p_style) {
			$this->body_colour = $p_style;
			$this->p_style = "font-family:Helvetica,Arial,sans-serif;font-size:12px;color:$p_style;";
		}

		/**
		 * @param mixed $p_style
		 */
		public function setPStyle($p_style) {
			$this->p_style = $p_style;
		}

		/**
		 * @param mixed $h2_style
		 */
		public function setH2Style($h2_style) {
			$this->h2_style = $h2_style;
		}

		/**
		 * @param string $email_title
		 */
		public function setEmailTitle($email_title) {
			$this->email_title = $email_title;
		}

		public function setSubject($subject) {
			$this->subject = $subject;

		}

		/**
		 * @return mixed
		 */
		public function getEmailTitle() {
			return $this->email_title;
		}

		/**
		 * @return mixed
		 */
		public function getSubject() {
			return $this->subject;
		}

		/**
		 * @return mixed
		 */
		public function getMessage() {
			return $this->message;
		}


		public function setText($array) {
			foreach ($array as $key => $form_field) {
				if ( !empty( $_POST[$form_field] ) ) {
					$this->message[$key] = $this->clean( $_POST[$form_field] );
				} else {
					$this->message[$key] = "Not Provided";
				}
			}
		}

		public function setTextArea($array) {
			foreach ($array as $key => $form_field) {
				if (!empty($_POST[ $form_field ])) {
					$text_area             = nl2br(wordwrap($_POST[ $form_field ], 60));
					$text_area             = $this->clean($text_area);
					$text_area             = preg_replace('#&lt;((?:br) /?)&gt;#', '<\1>', $text_area);
					$this->message[$key] = $text_area;
				} else {
					$this->message[$key] = "Not Provided";
				}
			}
		}

		public function buildMessage() {
			$body = "<html><head><title>{$this->email_title}</title></head>";
			$body .= "<body><table><tbody><tr><td>";
			$body .= "<h1 style=\"font-family:Helvetica,Arial,sans-serif;font-weight:bold;font-size:18px;color:$this->header_colour;\">$this->email_title</h1>";
			foreach ($this->message as $title => $text) {
				$text = stripslashes($text);
				$body .= "<h2 style=\"{$this->h2_style}\">{$title}:</h2>";
				$body .= "<p style=\"{$this->p_style}\"><strong>{$text}</strong></p>";
			}
			$body .= "</td></tr></tbody></table></body>";
			return $body;
		}

	}