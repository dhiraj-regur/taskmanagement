<?php
class LMVC_AjaxResponse
{

	private $responseType;
	private $status;
	private $msg;
	private $data;
	private $errors = array();
	
	public function __construct($responseType)
	{
		$this->errors = array();		
		if(in_array($responseType, array('xml','json')))
		{
			$this->responseType = $responseType;
		}
		else
		{
			trigger_error('Unsupported response type passed',E_USER_ERROR);
		}		
	}
	
	public function output()
	{
		if(trim($this->status) == '')
		{
			if(empty($this->errors))
			{
				$this->status =1;
			}
			else
			{
				$this->status =0;
			}
		}
		
		$response = array(
				'status' => $this->status,
				'errors' => $this->getErrors(),
				'msg'=> $this->msg,
				'data'=> $this->data
				);
		
		if("json" == $this->responseType)
		{
			header('Content-type: application/json');
			echo json_encode($response);
		}
		else
		{
			trigger_error('Reponse type '. $this->responseType .' has not been implemented yet. Please feel free to implement this response type.',E_USER_ERROR);
		}
	}
	
	final public function setStatus($status)
	{
		$this->status = $status;
	}
	
	final public function setData($data)
	{
		$this->data = $data;
	}
	
	final public function setMessage($message)
	{
		$this->msg = $message;
	}
	
	final public function addError($err_msg)
	{
		if(is_array($err_msg))
		{
			foreach($err_msg as $msg)
			{
				array_push($this->errors, $msg);
			}
	
		}
		else
		{
			array_push($this->errors,$err_msg);
		}
	
	}
	
	final public function hasErrors()
	{
		if(count($this->errors)>0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	final public function getErrors($index = null) {
	
		if (empty($index)) {
			return $this->errors;
		} else {
			if (!isset($this->errors[$index])) {
				trigger_error("No error message exists at the specified $index", E_USER_WARNING);
			} else {
				return $this->errors[$index];
			}
		}
	}
	
	
}