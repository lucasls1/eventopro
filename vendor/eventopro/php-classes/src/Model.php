<?php 

namespace EventoPro;

class Model{

	private $values = [];

	
	public function __call($name, $args)
	{

		$method = substr($name, 0, 3);
		$fieldName = substr($name, 3, strlen($name));
		
		switch ($method) {
			case "get":
				# code...
			$this->values[$fieldName];
				break;
				case "set":
				$this->values[$fieldName]=$args;
				break;
			}
		
	}
public function setData($data)
	{

		foreach ($data as $key => $value)
		{

			$this->{"set".$key}($value);

		}

	}
	public function getValues()
	{

		return $this->values;

	}

}


 ?>