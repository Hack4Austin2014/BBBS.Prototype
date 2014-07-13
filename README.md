BBBS.Prototype
==============

Functional prototype of the BBBS Events app for demonstration purposes


#API Spec:

```
data : {
	events : {
		event_id : {	//event_id is a string assigned by firebase when it is stored in the database
			address 	: 	{
				street1 : string 	// ie: "201 Colorado"
				street2 : string	// ie: "Apt 5"
				city 	: string	// ie: "Austin"
				state	: string	// ie: "TX"
				zip		: string	// ie: "78759"
			}

			agerange 	:	string	// ie: "6 to 18"

			category 	:	string	// one of ["Educational", "Arts/Music", "Sports", "Outdoors", "Discount Partner", "Other"]

			datebegin	:	string	// date in milliseconds since Jan 1, 1970 ie: "1405043349696"; see JS Date object

			description	:	string	// ie: "Austin Children’s Museum stays open late every Wednesday night for Community 						//			Night! Come out and enjoy exhibits and a variety of hands-on activities. 						//			Free admission but $1 donation is suggested."

			picture		:	string	// the absolute URL of the picture to include; hosted elsewhere

			pricerange	:	int		// one of [0, 1, 2, 3, 4, 5]

			promoted	:	boolean

			rating		:	int		// one of [0, 1, 2, 3, 4, 5]

			title		:	string	// ie: "Austin's Child Museum Community Night"

			url 		:	string	// ie: "http://www.myurl.com"
		}
	}


	standingevents : {
		event_id : {	//event_id is a string assigned by firebase when it is stored in the database
			address : 	{
				street1 : string 	// ie: "201 Colorado"
				street2 : string	// ie: "Apt 5"
				city 	: string	// ie: "Austin"
				state	: string	// ie: "TX"
				zip		: string	// ie: "78759"
			}

			agerange 	:	string	// ie: "6 to 18"

			category 	:	string	// one of ["Educational", "Arts/Music", "Sports", "Outdoors", "Discount Partner", "Other"]

			description	:	string	// ie: "Austin Children’s Museum stays open late every Wednesday night for Community 						//			Night! Come out and enjoy exhibits and a variety of hands-on activities. 						//			Free admission but $1 donation is suggested."

			picture		:	string	// the absolute URL of the picture to include; hosted elsewhere

			pricerange	:	int		// one of [0, 1, 2, 3, 4, 5]

			promoted	:	boolean

			rating		:	int		// one of [0, 1, 2, 3, 4, 5]

			schedule	:	string	// human-readable, ie: "M-F 9am-5pm", 
														"	M 9am-5pm,
															T 8:30am-5:30pm,
															W ..." 
															etc. (not programmatically parsed)

			title		:	string	// ie: "Austin's Child Museum Community Night"

			url 		:	string	// ie: "http://www.myurl.com"
		}
	}
}
```