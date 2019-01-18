$(document).ready(function(){
	$(document).on('click','#search-icon',function(){
		$('#search-form').slideToggle();
		$(this).find('i').toggleClass('fa-search');
		$(this).find('i').toggleClass('fa-close');
	});
});