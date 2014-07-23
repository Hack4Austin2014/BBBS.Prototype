/*
event_filters.js
Tuesday July 22, 2014 7:27pm Stefan S.

Event filtering for BBBS events

NOTES:

*/

/* ---------- BBBS event filter class */

function bbbs_event_filter()
{
	// filter events by category
	// in_events: array of BBBS event objects
	// in_categories: array of categories (strings)
	// returns BBBS events from in_events whose category matches one of the entries in in_categories
	this.filter_by_category= function(in_events, in_categories)
	{
		var result= null;
		
		if (!(in_events instanceof Array))
		{
			console.log("bbbs_event_filter.filter_by_category(): in_events is not an Array!");
		}
		else if (!(in_categories instanceof Array))
		{
			console.log("bbbs_event_filter.filter_by_category(): in_categories is not an Array!");
		}
		else
		{
			var categories= in_categories.splice(0);
			var i, j;
			
			// sanitize categories
			for (i= 0; i<categories.length; i++)
			{
				if ((null != categories[i]) && ("string" == typeof categories[i]))
				{
					categories[i]= categories[i].toLowerCase();
				}
				else
				{
					// invalid category - remove it
					categories.splice(i, 1);
				}
			}
			
			result= in_events.slice(0);
			
			for (i= 0; i<result.length; i++)
			{
				var remove_event= true;
				
				if ((null != result[i].category) && ("string" == typeof result[i].category))
				{
					var test_category= new String(result[i].category);
					
					test_category= test_category.toLowerCase();
					for (j= 0; j<categories.length; j++)
					{
						if (0==test_category.localeCompare(categories[j]))
						{
							remove_event= false;
							break;
						}
					}
				}
				else
				{
					console.log("bbbs_event_filter.filter_by_category(): in_event[" + i + "] has no 'category' field!");
				}
				
				if (true==remove_event)
				{
					result.splice(i, 1);
					i-= 1;
				}
			}
		}
		
		return result;
	}
}
