<?php

 	define('Include_HeaderScript', 'TRUE');
	class HeaderScript
	{	
		public $conf_Script;
		public $conf_PageTitle;
		public $conf_BannerText;
		public $conf_BannerLink;

		protected  static $_instance;

		public static function getInstance()
		{
			if(is_null(self::$_instance))
			{
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		public function script_reset()
		{
			$this->conf_Script = "";
			$this->conf_PageTitle = "";
			$this->conf_BannerText = "";
			$this->conf_BannerLink = "";
		}

		public function script_alert($text)
		{
			$this->conf_Script = "alert(\"".$text."\"); ";
		}

		public function script_navigate($text)
		{
			$this->conf_Script .= "window.location.href = \"".$text."\"; ";
		}

		public function script_PageTitle($text)
		{
			$this->conf_PageTitle = $text;
		}

		public function script_BannerText($text)
		{
			$this->conf_BannerText = $text;
		}

		public function script_BannerLink($text)
		{
			$this->conf_BannerLink = $text;
		}
	}

?>