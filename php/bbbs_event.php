<?php
/*
bbbs_event.php
Friday July 18, 2014 8:45pm Stefan S.

Wrapper class for BBBS event object

NOTES:

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
	datebegin: string, // milliseconds [NOTE: this code just uses DateTime objects]
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

/* ---------- includes */

// Google Event class
require_once "google_calendar.php";

/* ---------- constants */

define("PARSE_GCAL_EVENT_DESCRIPTION",		TRUE);

/* ---------- classes */

// BBBS Event
class c_bbbs_event
{
	/* ---------- constants */
	
	const k_key_event_id= 'event_id'; // string
	const k_key_address= 'address'; // string
	const k_key_address_street1= 'street1'; // string
	const k_key_address_street2= 'street2'; // string
	const k_key_address_city= 'city'; // string
	const k_key_address_state= 'state'; // string
	const k_key_address_zip= 'zip'; // string
	const k_key_age_range= 'agerange'; // string
	const k_key_category= 'category'; // string
	const k_key_start_time= 'datebegin'; // string - milliseconds
	const k_key_description= 'description'; // string
	const k_key_picture= 'picture'; // string - url
	const k_key_price_range= 'pricerange'; // int
	const k_key_promoted= 'promoted'; // boolean
	const k_key_rating= 'rating'; // int
	const k_key_title= 'title'; // string
	const k_key_url= 'url'; // string
	
	const k_gcal_desc_key_age= "[age:";
	const k_gcal_desc_key_cost= "[cost:";
	const k_gcal_desc_key_category= "[category:";
	
	/* ---------- members */
	
	private $m_data= array();
	
	/* ---------- methods */
	
	function __construct()
	{
		// initialize $m_data
		$this->m_data[self::k_key_event_id]= NULL;
		$this->m_data[self::k_key_address]= array();
		$this->m_data[self::k_key_address][self::k_key_address_street1]= NULL;
		$this->m_data[self::k_key_address][self::k_key_address_street2]= NULL;
		$this->m_data[self::k_key_address][self::k_key_address_city]= NULL;
		$this->m_data[self::k_key_address][self::k_key_address_state]= NULL;
		$this->m_data[self::k_key_address][self::k_key_address_zip]= NULL;
		$this->m_data[self::k_key_age_range]= "";
		$this->m_data[self::k_key_category]= "";
		$this->m_data[self::k_key_start_time]= 0;
		$this->m_data[self::k_key_description]= "";
		$this->m_data[self::k_key_picture]= "";
		$this->m_data[self::k_key_price_range]= 0;
		$this->m_data[self::k_key_promoted]= FALSE;
		$this->m_data[self::k_key_rating]= 0;
		$this->m_data[self::k_key_title]= "";
		$this->m_data[self::k_key_url]= "";
		
		return;
	}
	
	// return a copy of the event data
	public function get_event_data()
	{
		$copy = array();
		$copy= $this->m_data;
		
		return $copy;
	}
	
	// consumes a decoded Google event JSON object as input (ie, the Google event is itself already an associative array)
	// and initializes the BBBS event
	public function initialize_from_google_calendar_event($google_calendar_event)
	{
		if (isset($google_calendar_event[c_google_calendar::k_key_location]))
		{
			$this->m_data[self::k_key_address][self::k_key_address_street1]= $google_calendar_event[c_google_calendar::k_key_location];
		}
		if (isset($google_calendar_event[c_google_calendar::k_key_start_time]) &&
			isset($google_calendar_event[c_google_calendar::k_key_start_time][c_google_calendar::k_key_start_date_time]))
		{
			//$bbbs_event['datebegin']= strtotime($google_event['start']['dateTime']) * 1000;
			$time_zone= isset($google_calendar_event[c_google_calendar::k_key_start_time][c_google_calendar::k_key_time_zone]) ?
				new DateTimeZone($google_calendar_event[c_google_calendar::k_key_start_time][c_google_calendar::k_key_time_zone]) :
				new DateTimeZone("UTC");
			$this->m_data[self::k_key_start_time]= new DateTime(
				$google_calendar_event[c_google_calendar::k_key_start_time][c_google_calendar::k_key_start_date_time],
				$time_zone);
		}
		if (isset($google_calendar_event[c_google_calendar::k_key_description]))
		{
			$this->m_data[self::k_key_description]= $google_calendar_event[c_google_calendar::k_key_description];
			if (PARSE_GCAL_EVENT_DESCRIPTION)
			{
				// attempt to extract additional event details from the provided event description field
				$this->parse_google_calendar_event_description_field_parameters(
					strtolower($google_calendar_event[c_google_calendar::k_key_description]));
			}
		}
		if (isset($google_calendar_event[c_google_calendar::k_key_title]))
		{
			$this->m_data[self::k_key_title]= $google_calendar_event[c_google_calendar::k_key_title];
		}
		if (isset($google_calendar_event[c_google_calendar::k_key_url]))
		{
			$this->m_data[self::k_key_url]= $google_calendar_event[c_google_calendar::k_key_url];
		}
		
		return;
	}
	
	// attempts to extract additional event details from the provided text
	private function parse_google_calendar_event_description_field_parameters($parse_text)
	{
		$age_text= strstr($parse_text, self::k_gcal_desc_key_age);
		$cost_text= strstr($parse_text, self::k_gcal_desc_key_cost);
		$category_text= strstr($parse_text, self::k_gcal_desc_key_category);
		
		if (!empty($age_text))
		{
			// examples:
			// [age:9+]
			// [age:9-99]
			$age_text= substr($age_text, strlen(self::k_gcal_desc_key_age));
			$tokens= "+-]";
			$age_start= strtok($age_text, $tokens);
			$age_end= 99;
			
			if (!empty($age_start))
			{
				$age_start= min(max(0, $age_start), 99);
				$age_end= strtok($tokens);
				$age_end= empty($age_end) ? 99 : min(max(0, $age_end), 99);
			}
			else
			{
				$age_start= 0;
			}
			
			$this->m_data[self::k_key_age_range]= $age_start . "-" . $age_end;
		}
		
		if (!empty($cost_text))
		{
			// examples:
			// [cost:]
			// [cost:$]
			// [cost:$$]
			$cost_text= substr($cost_text, strlen(self::k_gcal_desc_key_cost));
			$tokens= "]";
			$cost_tokens= strtok($cost_text, $tokens);
			
			if (!empty($cost_tokens))
			{
				$cost_tokens_length= strlen($cost_tokens);
				for ($char_index= 0; $char_index < $cost_tokens_length; $char_index++)
				{
					if ('$' == $cost_tokens[$char_index])
					{
						$this->m_data[self::k_key_price_range]+= 1;
					}
				}
			}
		}
		
		if (!empty($category_text))
		{
			// examples:
			// [category:educational]
			$category_text= substr($category_text, strlen(self::k_gcal_desc_key_category));
			$tokens= "]";
			$category= strtok($category_text, $tokens);
			if (!empty($category))
			{
				$this->m_data[self::k_key_category]= $category;
			}
		}
		
		return;
	}
}

?>
