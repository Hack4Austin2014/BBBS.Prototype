BBBS.Prototype
==============

Functional prototype of the BBBS Events app for demonstration purposes


##API Spec:

Firebase RESTful API URL: `https://[firebase].firebaseIO.com/[path/to/json/object].json`

With the firebase RESTful API, `GET` returns the json data, `PUT` *replaces* the json data, and `POST` appends to the json data.

Our project is using the firebase JavaScript library. 

Our data is stored in the firebase as described below. `events` is an object containing event objects, where each key is the string assigned by firebase when it is stored, and each value is the event object associated with that string.

`events` represents a collection of single, well-defined events with a beginning date and/or time.

`standingevents` represents a collection of standing events, such as a discount partnership that is valid during the hours of operation of a business, or an event that repeats often enough that it would be annoying to manually enter it into the system each time. These standing events will be presented seperately from the scheduled events, unless they are 'promoted', in which case the event will be promoted to the top of the event search results.

```
data : {
	events : {
		event_id : {	//event_id is a string assigned by firebase
			address 	: 	{
				street1 : string 	// ie: "201 Colorado"
				street2 : string	// ie: "Apt 5"
				city 	: string	// ie: "Austin"
				state	: string	// ie: "TX"
				zip		: string	// ie: "78759"
			}

			agerange 	:	string	// one of ageranges (see below)

			category 	:	[string]	// zero or more of categories (see below)

			datebegin	:	string	// milliseconds. ie: "1405043349696"; see JS Date object

			description	:	string

			picture		:	string	// full URL of the picture to include

			pricerange	:	int		// one of priceranges (see below)

			promoted	:	boolean

			rating		:	int		// one of ratings (see below)

			title		:	string

			url 		:	string	// full URL; ie: "http://www.myurl.com"
		}
	}


	standingevents : {
		event_id : {	//event_id is a string assigned by firebase
			address : 	{
				street1 : string 	// ie: "201 Colorado"
				street2 : string	// ie: "Apt 5"
				city 	: string	// ie: "Austin"
				state	: string	// ie: "TX"
				zip		: string	// ie: "78759"
			}

			agerange 	:	string	// one of ageranges (see below)

			category 	:	[string]	// zero or more of categories (see below)

			description	:	string

			picture		:	string	// full URL of the picture to include

			pricerange	:	int		// one of priceranges (see below)

			promoted	:	boolean

			rating		:	int		// one of ratings (see below)

			schedule	:	string	// human-readable, ie: "M-F 9am-5pm". Not programmatically parsed.

			title		:	string

			url 		:	string	// full URL; ie: "http://www.myurl.com"
		}
	}

	categories : [string]

	ratings : [int]

	ageranges : [string]

	priceranges : [int]
}
```