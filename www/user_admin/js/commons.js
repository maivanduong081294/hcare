// JavaScript Document
$(document).ready(function(){
	$('.eventList li').click(function(e){
			showWindow('<div>'+$(this).find('div.content').html()+'</div>');
	});
});

function showWindow(data)
{
	/* Each event contains a set of hidden divs that hold
	   additional information about the event: */
	   
	var title = $('.title',data).text();
	var date = $('.date',data).text();
	var body = $('.body',data).html();
	
	$('<div id="overlay">').css({
								
		width:$(document).width(),
		height:$(document).height(),
		opacity:0.6
		
	}).appendTo('body').click(function(){
		
		$(this).remove();
		$('#windowBox').remove();
		
	});
	
	$('body').append('<div id="windowBox"><div id="titleDiv">'+title+'</div>'+body+'<div id="date">'+date+'</div></div>');

	$('#windowBox').css({
		width:500,
		height:350,
		left: ($(window).width() - 500)/2,
		top: ($(window).height() - 350)/2
	});
	
}
function FormatNumber(str){
    var strTemp = GetNumber(str);
    if(strTemp.length <= 3)
        return strTemp;
    strResult = "";
    for(var i =0; i< strTemp.length; i++)
        strTemp = strTemp.replace(",", "");
    for(var i = strTemp.length; i>=0; i--)
    {
        if(strResult.length >0 && (strTemp.length - i -1) % 3 == 0)
            strResult = "," + strResult;
        strResult = strTemp.substring(i, i + 1) + strResult;
    }    
    return strResult;
}
//=================================================================================================
function GetNumber(str)
{
    for(var i = 0; i < str.length; i++)
    {    
        var temp = str.substring(i, i + 1);        
        if(!(temp == "," || (temp >= 0 && temp <=9)))
        {
            return str.substring(0, i);
        }
        if(temp == " ")
            return str.substring(0, i);
    }
    return str;
}
//=================================================================================================
