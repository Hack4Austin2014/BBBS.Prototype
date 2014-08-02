<?php
/*
debug.php
Monday July 14, 2014 7:47pm Stefan S.

debugging utilities
*/

/* ---------- constants */

// set to TRUE to enable various debugging functionality

define("DEBUG",								TRUE);

define("DEBUG_LOGGING",						DEBUG);

define("ERROR_INCLUDES_CALLSTACK",			TRUE);
define("WARNING_INCLUDES_CALLSTACK",		FALSE);
define("LOG_INCLUDES_CALLSTACK",			FALSE);

define("ERROR_MESSAGE_TO_SYSLOG",			TRUE);
define("WARNING_MESSAGE_TO_SYSLOG",			TRUE);
define("LOG_MESSAGE_TO_SYSLOG",				FALSE);

define("ERROR_MESSAGE_TO_STDOUT",			FALSE);
define("WARNING_MESSAGE_TO_STDOUT",			FALSE);
define("LOG_MESSAGE_TO_STDOUT",				FALSE);

define("ERROR_PREFIX",						"###ERROR: ");
define("WARNING_PREFIX",					"###WARNING: ");
define("LOG_PREFIX",						"###LOG: ");

define("ERROR_POSTFIX",						"");
define("WARNING_POSTFIX",					"");
define("LOG_POSTFIX",						"");

define("NEWLINE",							"\r\n");


// the following will cause output XML-style messages
/*
define("ERROR_PREFIX",						"<ERROR>");
define("WARNING_PREFIX",					"<WARNING>");
define("LOG_PREFIX",						"<LOG>");

define("ERROR_POSTFIX",						"</ERROR>");
define("WARNING_POSTFIX",					"</WARNING>");
define("LOG_POSTFIX",						"</LOG>");

define("NEWLINE",							"<br/>");
*/

// the following will cause output JSON-style messages
/*
define("ERROR_PREFIX",						"{\"ERROR\":{\"");
define("WARNING_PREFIX",					"{\"WARNING\":{\"");
define("LOG_PREFIX",						"{\"LOG\":{\"");

define("ERROR_POSTFIX",						"\"},");
define("WARNING_POSTFIX",					"\"},");
define("LOG_POSTFIX",						"\"},");

define("NEWLINE",							"\r\n");
*/

// debug class
class debug
{
	/* ---------- methods */

	// generate an error message
	// error messages should indicate something potentially damaging / unrecoverable has occurred
	static function error($message)
	{
		if (TRUE == DEBUG_LOGGING)
		{
			$error_message= ERROR_PREFIX . $message;
			if (TRUE == ERROR_INCLUDES_CALLSTACK)
			{
				$skip_frames= 1;
				$error_message= $error_message . NEWLINE . debug::generate_backtrace($skip_frames);
			}
			$error_message= $error_message . ERROR_POSTFIX;
			
			if (TRUE == ERROR_MESSAGE_TO_SYSLOG)
			{
				error_log($error_message, 0);
			}
			
			if (TRUE == ERROR_MESSAGE_TO_STDOUT)
			{
				echo($error_message);
			}
		}
		
		return;
	}

	// generate an warning message
	// warning messages should indicate something problematic, but recoverable, has occurred
	static function warning($message)
	{
		if (TRUE == DEBUG_LOGGING)
		{
			$warning_message= WARNING_PREFIX . $message;
			if (TRUE == WARNING_INCLUDES_CALLSTACK)
			{
				$skip_frames= 1;
				$warning_message= $warning_message . NEWLINE . debug::generate_backtrace($skip_frames);
			}
			$warning_message= $warning_message . $WARNING_POSTFIX;
			
			if (TRUE == WARNING_MESSAGE_TO_SYSLOG)
			{
				error_log($warning_message, 0);
			}
			
			if (TRUE == WARNING_MESSAGE_TO_STDOUT)
			{
				echo($warning_message);
			}
		}
		
		return;
	}

	// generate an log message
	// log messages are intended for general informational reporting
	static function log($message)
	{
		if (TRUE == DEBUG_LOGGING)
		{
			$log_message= LOG_PREFIX . $message;
			if (TRUE == LOG_INCLUDES_CALLSTACK)
			{
				$skip_frames= 1;
				$log_message= $log_message . NEWLINE . debug::generate_backtrace($skip_frames);
			}
			$log_message= $log_message . LOG_POSTFIX;
			
			if (TRUE == LOG_MESSAGE_TO_SYSLOG)
			{
				error_log($log_message, 0);
			}
			
			if (TRUE == LOG_MESSAGE_TO_STDOUT)
			{
				echo($log_message);
			}
		}
		
		return;
	}

	// generates a text string representation of a backtrace
	// $skip_frame_count: number of stack frames to ignore
	static function generate_backtrace($skip_frame_count)
	{
		$backtrace_string= "";
		$backtrace= debug_backtrace();
		
		$skip_frame_count= max(0, $skip_frame_count);
		$frame_count= count($backtrace);
		
		for ($frame_index= $skip_frame_count; $frame_index<$frame_count; $frame_index++)
		{
			$file_name= $backtrace[$frame_index]["file"];
			$line_number= $backtrace[$frame_index]["line"];
			$method_name= $backtrace[$frame_index]["function"];
			$arguments= $backtrace[$frame_index]["args"];
			$arguments_string= "";
			
			foreach ($arguments as $arg)
			{
				$separator= empty($arguments_string) ? "" : ", ";
				$arguments_string= $arguments_string . $arg . $separator;
			}
			
			$frame_details= $method_name . "(" . $arguments_string . ") called at " . $file_name . ":" . $line_number;
			$backtrace_string= $backtrace_string . $frame_details . NEWLINE;
		}
		
		return $backtrace_string;
	}
}

?>
