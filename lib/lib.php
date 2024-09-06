
<?php 

	/**
	 * 
	 */
	class lib
	{
		
		public $url_tpl;
		public $tpl;

		function __construct($url_tpl)
		{
			$this->url_tpl = $url_tpl;

			if(!file_exists($url_tpl)){
				echo("Error la plantilla solicitada <u>no existe</u>: <b>".$url_tpl."</b>");

				exit();
			}

			$this->tpl = file_get_contents($url_tpl);

			if($this->testVar("URL_WEB")){
				$this->assign("URL_WEB", URL_WEB);
			}

			if($this->testVar("CACHE")){
				if(CACHE==DEBUG){
					$this->assign("CACHE", "?cache=".date("YmdHis"));
				}else{
					$this->assign("CACHE", "");
				}
			}
			
		}

		private function testVar($var){
			return strstr($this->tpl, "{{{$var}}}");			
		}

		public function assign($var, $value){

			if(!$this->testVar($var)){

				echo("La variable <b>$var</b> no existe dentro de la plantilla <b>".$this->url_tpl."</b>");
				exit();
			}

			$this->tpl = str_replace("{{{$var}}}", $value, $this->tpl);

		}

		public function printToScreen(){
			echo $this->tpl;
		}


	}

 ?>