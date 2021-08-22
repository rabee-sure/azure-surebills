<?php
namespace App\Exceptions;
use Exception;

class ValidationException extends Exception
{
	protected $error_message;
	
	protected $exception_message;

	public function __construct($message = 'Exception has no message', $error_message = 451)
	{
		$this->exception_message = $message;
		$this->error_message = $error_message;
	}

	public function apiHandler()
	{
		return response()->json([
			'success' => false,
			'message' => $this->exception_message,
			'error_code' => $this->error_message,
		], $this->error_message);
	}

}
