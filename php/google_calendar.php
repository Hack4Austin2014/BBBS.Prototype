<?php
/*
google_calendar.php
Monday July 14, 2014 7:47pm Stefan S.

Google Calendar integration utilities for BBBS Events app

NOTES:
* uses pecl/http v1.7.6

*/

/* ---------- includes */

require_once "universal/universal.php";

require_once "event_source.php";
require_once "bbbs_event.php";

/* ---------- classes */

// Google Calendar
class c_google_calendar extends c_event_source
{
	/* ---------- constants */
	
	// URI for Google Calendar API
	const k_google_calendar_uri= 'https://www.googleapis.com/calendar/v3/calendars/';
	// Google Developer API key for Google Calendar API access
	//currently using Stefan's personal API key
	const k_google_calendar_api_key= 'AIzaSyCFmZJ-R0o4IyhXVzY3A6EerjzfH4Kprfo';
	
	const k_google_calendar_query_time_start= 'timeMin';
	const k_google_calendar_query_time_end= 'timeMax';
	
	const k_key_location= 'location'; // string
	const k_key_start_time= 'start'; // string
	const k_key_start_date_time= 'dateTime'; // DateTime
	const k_key_time_zone= 'timeZone';
	const k_key_description= 'description'; // string
	const k_key_title= 'summary'; // string
	const k_key_url= 'htmlLink'; // string
	
	/* ---------- members */
	
	private $m_calendar_id= "";
	
	/* ---------- c_event_source methods */
	
	// get_events()
	// $start_date_time: optional input start date / time, or NULL
	// $end_date_time: optional inout end date / time, or NULL
	// returns: array of bbbs_event objects, or NULL
	public function get_events($start_date_time, $end_date_time)
	{
		$bbbs_events_array= NULL;
		
		$google_events_list_json= $this->events_list_json($this->m_calendar_id, $start_date_time, $end_date_time);
		//debug::log($google_events_list_json);
		
		if (NULL!=$google_events_list_json)
		{
			$google_events_array= $this->parse_google_calendar_events_list_json($google_events_list_json);
			if (NULL!=$google_events_array)
			{
				$bbbs_events_array= $this->google_calendar_events_to_bbbs_events($google_events_array);
				if (NULL!=$bbbs_events_array)
				{
					$google_events_count= count($google_events_array);
					$bbbs_events_count= count($bbbs_events_array);
					debug::log("Converted " . $bbbs_events_count . " of " . $google_events_count . " Google calendar events into BBBS events");
				}
				else
				{
					debug::warning("failed to convert Google calendar events to BBBS events");
				}
			}
			else
			{
				debug::warning("failed to parse Google events list JSON");
			}
		}
		else
		{
			debug::warning("failed to retrieve google calendar events for " . $bbbs_calendar_id);
		}
		
		return $bbbs_events_array;
	}
	
	/* ---------- public methods */
	
	// constructor
	function __construct($calendar_id)
	{
		$this->m_calendar_id= $calendar_id;
		
		return;
	}
	
	/* ---------- private methods */
	
	// returns calendar events query results in JSON format (or NULL on failure)
	// for the input Google Calendar ID
	// ref: https://developers.google.com/google-apps/calendar/v3/reference/events/list
	private function events_list_json($calendar_id, $start_time, $end_time)
	{
		$body_result= NULL;
		$date_time_zone= new DateTimeZone("UTC");
		if (!isset($start_time))
		{
			$start_time= "now";
			//$end_time= "+1 day"; // look ahead to tomorrow
			$end_time= "+1 week"; // look ahead 1 week
		}
		else if (!isset($end_time))
		{
			//$end_time= "+1 day"; // look ahead to tomorrow
			$end_time= "+1 week"; // look ahead 1 week
		}
		
		$start_date_time= new DateTime($start_time, $date_time_zone);
		$end_date_time= new DateTime($end_time, $date_time_zone);
		
		$data= array(
			'key' => self::k_google_calendar_api_key,
			self::k_google_calendar_query_time_start => $start_date_time->format(DateTime::RFC3339),
			self::k_google_calendar_query_time_end => $end_date_time->format(DateTime::RFC3339)
			);
		$query= http_build_query($data);
		$url= self::k_google_calendar_uri . urlencode($calendar_id) . '/events?' . $query;
		$response= http_get($url, NULL, $info);
		if (FALSE !== $response)
		{
			//print_r($info);
			$body_result= http_parse_message($response)->body;
		}
		
		return $body_result;
	}
	
	// parse a Google calendar events list JSON blob into a php associative array
	// returns an associative array representation of the Google events list, or an empty array on failure
	private function parse_google_calendar_events_list_json($google_events_list_json)
	{
		$result= array();
		$json_object= json_decode($google_events_list_json, true);
		if (NULL!=$json_object)
		{
			// Google calendar events list is the "items"[] array
			$result= $json_object['items'];
		}
		
		return $result;
	}
	
	// takes a *decoded* $google_event JSON object as input (ie, the $google_event is itself already an associative array)
	// returns an associative array representation of the BBBS event
	private function google_event_to_bbbs_event($google_event)
	{
		$bbbs_event= new c_bbbs_event();
		
		$bbbs_event->initialize_from_google_calendar_event($google_event);
		
		return $bbbs_event->get_event_data();
	}
	
	// convert an array of Google calendar events to an array of BBBS events
	private function google_calendar_events_to_bbbs_events($google_events_array)
	{
		$bbbs_events= array();
		
		if (NULL!=$google_events_array)
		{
			foreach ($google_events_array as $google_event)
			{
				// convert to BBBS event and append to the array
				$bbbs_event= $this->google_event_to_bbbs_event($google_event);
				$bbbs_events[]= $bbbs_event;
			}
			// break the reference with the last element
			unset($bbbs_event);
		}
		
		return $bbbs_events;
	}
}

/* ---------- main */

// test code

Header('Content-type: application/json');
echo("{\"results\":");

$bbbs_calendar_id= 'n01082cnj5ujivj2t6v98if3ek@group.calendar.google.com'; //<-- cloned from 'bbbsctxcalendar@gmail.com';
$gcal= new c_google_calendar($bbbs_calendar_id);
$bbbs_events= $gcal->get_events(NULL, NULL);

if (isset($bbbs_events))
{
	echo(json_encode($bbbs_events, JSON_UNESCAPED_SLASHES));
}
else
{
	debug::error("failed to get calendar events!");
}

echo("}");

?>
