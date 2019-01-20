$(document).ready(function(){
	$(document).on('click','#search-icon',function(){
		$('#search-form').slideToggle();
		$(this).find('i').toggleClass('fa-search');
		$(this).find('i').toggleClass('fa-close');
	});
	$(document).on('click','#menu-icon',function(){
		$('.service-menu > ul').toggleClass('actived');
		$(this).toggleClass('actived');
	});
	$('.banner-list-image').slick({
		lazyLoad: 'ondemand',
		slidesToShow: 1,
		slidesToScroll: 1,
		arrows: false,
		fade: true,
		asNavFor: '.banner-list-title',
	});
	$('.banner-list-title').slick({
		slidesToShow: 3,
		slidesToScroll: 1,
		asNavFor: '.banner-list-image',
		arrows: false,
		dots: false,
		focusOnSelect: true,
		responsive: [
			{
				breakpoint: 1024,
				settings: {
					slidesToShow: 3,
					}
				},
			{
			breakpoint: 576,
				settings: {
					slidesToShow: 2,
				}
			}
		]
	});
});