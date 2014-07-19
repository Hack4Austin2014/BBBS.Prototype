<?php
/*
google_calendar.php
Monday July 14, 2014 7:47pm

Google Calendar integration utilities for BBBS Events app

NOTES:
* uses pecl/http v1.7.6

*/

/* ---------- classes */

// Google Calendar
class google_calendar
{
	/* ---------- constants */
	
	// URI for Google Calendar API
	const k_google_calendar_uri= 'https://www.googleapis.com/calendar/v3/calendars/';
	// Google Developer API key for Google Calendar API access
	//currently using Stefan's personal API key
	const k_google_calendar_api_key= 'AIzaSyCFmZJ-R0o4IyhXVzY3A6EerjzfH4Kprfo';
	
	/* ---------- members */
	
	/* ---------- methods */
	
	// returns an array of BBBS events pulled from the input Google calendar (or NULL on failure)
	
	// returns calendar events query results in JSON format (or NULL on failure)
	// for the input Google Calendar ID
	// ref: https://developers.google.com/google-apps/calendar/v3/reference/events/list
	public function events_list_json($calendar_id)
	{
		$body_result= NULL;
		$data= array('key' => self::k_google_calendar_api_key);
		$query= http_build_query($data);
		$url= self::k_google_calendar_uri . urlencode($calendar_id) . '/events?' . $query;
		$response= http_get($url, NULL, $info);
		if (FALSE!=$response)
		{
			//print_r($info);
			$body_result= http_parse_message($response)->body;
		}
		
		return $body_result;
	}
	
	// parse a Google calendar events list JSON blob into a php associative array
	// returns an associative array representation of the Google events list, or an empty array on failure
	public function parse_google_calendar_events_list_json($google_events_list_json)
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
	public function google_event_to_bbbs_event($google_event)
	{
		/*
		BBBS event JSON:
		
		event
		{
			event_id: string, // firebase id
			address
			{
				street1: string,
				street2: string,
				city: string,
				state: string,
				zip: string,
			},
			agerange: string,
			category: string[],
			datebegin: string, // milliseconds
			description: string,
			picture: string, // url
			pricerange: int,
			promoted: boolean,
			rating: int,
			title: string,
			url: string, // event url
		}
		
		Google Event representation is documented here:
		https://developers.google.com/google-apps/calendar/v3/reference/events
		*/
		
		$bbbs_event= array();
		
		$bbbs_event['event_id']= NULL;
		$bbbs_event['address']= array();
		$bbbs_event['address']['street1']= $google_event['location'];
		$bbbs_event['address']['street2']= '';
		$bbbs_event['address']['city']= '';
		$bbbs_event['address']['state']= '';
		$bbbs_event['address']['zip']= '';
		$bbbs_event['agerange']= '';
		$bbbs_event['category']= '';
		$bbbs_event['datebegin']= strtotime($google_event['start']['dateTime']) * 1000;
		$bbbs_event['description']= $google_event['description'];
		$bbbs_event['picture']= '';
		$bbbs_event['pricerange']= '';
		$bbbs_event['promoted']= FALSE;
		$bbbs_event['rating']= 0;
		$bbbs_event['title']= $google_event['summary'];
		$bbbs_event['url']= $google_event['htmlLink'];
		
		return $bbbs_event;
	}
	
	// convert an array of Google calendar events to an array of BBBS events
	public function google_calendar_events_to_bbbs_events($google_events_array)
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

//echo(phpinfo());
$bbbs_calendar_id= 'bbbsctxcalendar@gmail.com';
$gcal= new google_calendar();
$google_events_list_json= $gcal->events_list_json($bbbs_calendar_id);
//echo($google_events_list_json);
if (NULL!=$google_events_list_json)
{
	$google_events_array= $gcal->parse_google_calendar_events_list_json($google_events_list_json);
	if (NULL!=$google_events_array)
	{
		$bbbs_events_array= $gcal->google_calendar_events_to_bbbs_events($google_events_array);
		if (NULL!=$bbbs_events_array)
		{
			$google_events_count= count($google_events_array);
			$bbbs_events_count= count($bbbs_events_array);
			echo("Converted " . $bbbs_events_count . " of " . $google_events_count . " Google calendar events into BBBS events<br/><br/>");
			foreach ($bbbs_events_array as $bbbs_event)
			{
				echo("<p>" . print_r($bbbs_event, true) . "<br/></p>");
			}
		}
		else
		{
			echo("failed to convert Google calendar events to BBBS events");
		}
	}
	else
	{
		echo("failed to parse Google events list JSON");
	}
}
else
{
	echo("failed to retrieve google calendar events for " . $bbbs_calendar_id);
}

?>
