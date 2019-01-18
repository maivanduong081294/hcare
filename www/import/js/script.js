$(document).ready(function(){
  $('.save').click(function(){
      $('#add_vendor').submit();
  });
  $('.search').click(function(){
      $('#search_imei').submit();
  });
  $('.reset').click(function(){
      $('#add_vendor')[0].reset();
  });
  $('#content_test').slimScroll({
      height: '300px'
  });
});