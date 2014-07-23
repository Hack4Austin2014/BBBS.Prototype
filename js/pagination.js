
// this is the main class
function AccordionPaginator(element, currentPage, itemsPerPage, paginatorControl) {
	this.element = element;
	this.currentPage = currentPage;
	this.itemsPerPage = itemsPerPage;
	this.paginatorControl = paginatorControl;
	
	// does the actual pagination (shows/hides items)
	this.paginate = function() {
		var index = this.currentPage * this.itemsPerPage;
		
		element.accordion("activate", index);
		element.children().hide();

		if(index < 0) {
			this.element.children("span:first").show();
			this.element.children("div:first").show();	
		}
		else {
		
			this.element
				.children("span:eq(" + index + ")")
				.show();
				
			this.element
				.children("div:eq(" + index + "),div:gt(" + index + ")")
				.filter(":lt(" + this.itemsPerPage + ")")
				.show();
		}
		
		this.refreshControl();
	};
	
	// increments the currentPage var (if possible) and calls paginate
	this.nextPage = function() {
		if(this.currentPage + 1 >= this.getMaxPageIndex()) {
			return;
		}
		
		this.currentPage++;
		this.paginate();
	};
	
	// decrements the currentPage var (if possible) and calls paginate
	this.previousPage = function() {
		if(this.currentPage - 1 < 0) {
			return;
		}

		this.currentPage--;
		this.paginate();
	};
	
	// sets currentPage var (if possible) and calls paginate
	this.goToPage = function(pageIndex) {
		if((pageIndex < 0) || (pageIndex  >= this.getMaxPageIndex())) {
			return;
		}
		
		this.currentPage = pageIndex;
		this.paginate();
	};
	
	// returns the maximum of pages possible with the current number of items
	this.getMaxPageIndex = function() {
		var items = this.element.children("h3");
		var fullPages = items.length / this.itemsPerPage;
		var restPage = items.length % (this.itemsPerPage > 0 ? 1 : 0);
		return fullPages + restPage;
	};
	
	// fills up the paginator control
	this.refreshControl = function() {
		if(this.paginatorControl == null) {
			return;
		}
		
		var pageList = this.paginatorControl.children(".goToPage");
		pageList.empty();
		for(var i = 0; i < this.getMaxPageIndex(); i++) {
			pageList.append("<option value=\"" + i + "\">" + (i + 1) + "</option>");
		}
		pageList.val(this.currentPage);
	};
}

jQuery.fn.extend({
	paginateAccordion: function(options) {
		var currentPage = options.currentPage ? parseInt(options.currentPage) : 0;
		var itemsPerPage = options.itemsPerPage ? parseInt(options.itemsPerPage) : 5;
		var paginatorControl = options.paginatorControl;
		
		return new AccordionPaginator(this, currentPage, itemsPerPage, paginatorControl);
	}
});
