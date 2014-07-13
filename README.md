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


##Installation
**Note**: This is still in development; once we have a functional prototype, we will move all development to a `dev` branch and leave the `master` branch as the current release. Stay tuned for details on configuring your firebase, defining your firebase URL in the javascript, and any installation/hosting details.

##Road Map
We plan on finishing the prototype such that it will be all static HTML, JavaScript, and CSS files and can be hosted on any static web page server. This, coupled with your [firebase plan](https://www.firebase.com/pricing.html) of choice, should remove the need for any server-side coding. Depending on your database connection needs, you might get by with the free firebase hacker plan. Otherwise, you simply pay for your connection needs. 

We have on the horizon the vision to make a sequel server version, so you can host it all locally on a server you have access to. Also, this was born at a hackathon, so we didn't get a chance to use any JS frameworks; however, future versions may be rewritten in AngularJS and possibly use Google Maps API or the new firebase [Geofire](https://github.com/firebase/geofire/).

##Acknowledgements

Read about our hackathon on the Big Brothers Big Sisters of Central Texas [blog](http://bigmentoring.wordpress.com/2014/06/04/bbbs-hack4austin/). 

Thank-you to [ESRI](http://www.esri.com/) for supporting this non-profit effort with API access. 