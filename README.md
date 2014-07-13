BBBS.Prototype
==============

Functional prototype of the BBBS Events app for demonstration purposes


##API Spec:

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

			agerange 	:	string	// one of ["6-10", "11-14", "15-18"]

			category 	:	string	// one of ["Educational", "Arts/Music", "Sports", "Outdoors", "Discount Partner", "Other"]

			datebegin	:	string	// milliseconds since Jan 1, 1970 ie: "1405043349696"; see JS Date object

			description	:	string

			picture		:	string	// full URL of the picture to include

			pricerange	:	int		// one of [0, 1, 2, 3, 4, 5]

			promoted	:	boolean

			rating		:	int		// one of [0, 1, 2, 3, 4, 5]

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

			agerange 	:	string	// one of ["6-10", "11-14", "15-18"]

			category 	:	string	// one of ["Educational", "Arts/Music", "Sports", "Outdoors", "Discount Partner", "Other"]

			description	:	string

			picture		:	string	// full URL of the picture to include

			pricerange	:	int		// one of [0, 1, 2, 3, 4, 5]

			promoted	:	boolean

			rating		:	int		// one of [0, 1, 2, 3, 4, 5]

			schedule	:	string	// human-readable, ie: "M-F 9am-5pm". Not programmatically parsed.

			title		:	string

			url 		:	string	// full URL; ie: "http://www.myurl.com"
		}
	}
}
```