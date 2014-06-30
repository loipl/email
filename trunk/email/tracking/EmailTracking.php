<?php
class EmailTracking
{
	
	protected $parameters;
	
	public function __construct($parameterHolders = NULL, $parameterNames = array())
	{
		$this->parameters = array();

		if (empty($parameterHolders)) {
			$parameterHolders = $_GET;
		}
		
		foreach($parameterNames as $pName) {
			if (isset($_GET[$pName])) {
				$this->parameters[$pName] = $_GET[$pName];
			}
			else {
				$this->parameters[$pName] = '';
			}
		}
	}
    //--------------------------------------------------------------------------


	public function getParameters() {
		return $this->parameters;
	}
    //--------------------------------------------------------------------------
}