// JavaScript Document
//usage
/*filter_array = new Array(
			'rfullname:Vui lòng nhập họ tên',
			'remail:Vui lòng nhập địa chỉ email',
			'cremail:Địa chỉ email không đúng',			
			'rpassword',						
			'crpassword:Mật khẩu xác nhận không đúng',			
			'rphone',			
			'rcode:Vui lòng nhập mã an toàn');
		valid = new Array(
			'required',
			'email',
			'confirm',
			'password',
			'confirm',
			'alert-number',
			'required'
			);
		result = formValid('fregister', filter_array, valid, 'inline');
*/
    var vtimer;
    function fsubmit(frmID, filter_array, valid){
			//alert_type: inline, alertbox			
            min_pass = 6;
            spec_chars = "? | / > < \ : * \" \'";
            //filter_array = new Array('password','cpassword');
            //valid = new Array('pass','confirm');
            inform = false;
            $('.alert_box').remove();
            for(i=0;i<filter_array.length;i++)
            {
                name = filter_array[i];
                if (name.indexOf(":")>0){ //has custom alert
                    __name = name.split(":");
                    name = __name[0];
                    __alert = __name[1];
                }else{
                    __alert = '';
                }

//                value = $('#'+frmID).find('#'+name).val();
//                alert(name+"---"+value);
                switch (valid[i])
                {
                    case 'email':
                        if (!isValidEmailAddress($('#'+frmID).find('#'+name).val()))
                        {
                            inform = true;
                            _focus = name;
                            if (__alert == '')
                                msg = "Vui lòng nhập email hợp lệ";
                            else
                                msg = __alert;
                        }
                        break;
                    case 'number':
                        if(isNaN($('#'+frmID).find('#'+name).val()) || $('#'+frmID).find('#'+name).val() == '' )
                        {
                            inform = true;
                            _focus = name;
                            if (__alert == '')
                                msg = "Vui lòng nhập số điện thoại";
                            else
                                msg = __alert;
                        }
                        break;
                    case 'required':
                        if ($('#'+frmID).find('#'+name).val() == '')
                        {
                            _focus = name;
                            if (__alert == '')
                                msg = "Bạn chưa điền đủ thông tin bắt buộc";
                            else
                                msg = __alert;

                            inform = true;
                        }
                        break;                    
                    case 'selected':
                        if ($('#'+frmID).find('#'+name).val() == '' || $('#'+frmID).find('#'+name).val() == 0)
                        {
                            _focus = name;
                            if (__alert == '')
                                msg = "Bạn chưa điền đủ thông tin bắt buộc";
                            else
                                msg = __alert;

                            inform = true;
                        }
                        break;
                    case 'alert-email':
                        if (!isValidEmailAddress($('#'+frmID).find('#'+name).val()) && $('#'+frmID).find('#'+name).val() != '')
                        {
                            inform = true;
                            _focus = name;
                            if (__alert == '')
                                msg = "Vui lòng nhập email hợp lệ";
                            else
                                msg = __alert;
                        }
                        break;
					case 'alert-number':
							if ($('#'+frmID).find('#'+name).val() != ''){
								if (isNaN($('#'+frmID).find('#'+name).val())){
									inform = true;
									_focus = name;
									if (__alert == '')
										msg = "Vui lòng nhập số";
									else
										msg = __alert;	
								}
							}
						break;
                    case 'confirm':
                            original = name.substr(1,name.length);
                            value = $('#'+name).val();
                            if (value != $('#'+frmID).find('#'+original).val()){
                                _focus = name;
                                if (__alert == '')
                                    msg = "Xác nhận không đúng.";
                                else
                                    msg = __alert;

                                inform = true;
                            }
                    break;
                    case 'password':
                            value = $('#'+name).val();
                            isPass = validup(value);

                            if ( value.length < min_pass || !isPass ){
                                    _focus = name;
                                    if (__alert == '')
                                        msg = "Vui lòng nhập mật khẩu chiều dài tối thiểu "+min_pass+" ký tự và không bao gồm các ký tự đặc biệt " + spec_chars;
                                    else
                                        msg = __alert;

                                    inform = true;
                            }
                    break;
                    case 'captcha':
                        if ($(frmID).find('#'+name).val() == '')
                        {
                            _focus = name;
                                if (__alert == '')
                                    msg = "Vui lòng nhập mã an toàn";
                                else
                                    msg = __alert;

                            inform = true;
                        }
                    break;
                }
                if (inform)
                {
                    break;
                }
            }
            if (inform)
            {
//				if (alert_type == 'inline'){
//					$('#'+frmID).find('#'+_focus).select();
//					/*alert inline*/
//					var _width = $('#'+_focus).width();
//					
//					if (_width < 400){
//						var _left = $('#'+_focus).offset().left + 20;
//					}else{
//						var _left = 790;
//					}
//					
//					$('#'+_focus).parent().append('<span class="alert_box" style="position: absolute; background: RED; color:#ffffff; font-size:12px; padding:3px; top:0; width:auto; margin:0 0 0 10px;">'+msg+'</span>');
//
//				}
//				if (alert_type == 'alertbox'){
                /*alert box*/
	                alert(msg);
                        $('#'+frmID).find('#'+_focus).select();
//				}
                return false;
            }
            else
            {
                return true;
            }
    }

    function isValidEmailAddress(emailAddress) {
        var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
        return pattern.test(emailAddress);
    }
    // valid user name password with none illealCharacters
    function validup(myvalue){
            // ? | / > < \ : * " '
            var illegalChars = /(\?|\/|>|<|\\|\:|\*|\"|\'|\s)/g;
            if (illegalChars.test(myvalue))
                    return false;
            return true;
    }
    function hint(id, title) {

        $('#'+id).val(title);
        $('#'+id).focus(function(){
            _value = $(this).val();
            if (_value == title) {
                    $(this).val('');
            }
        });

        $('#'+id).focusout(function(){
            _value = $(this).val();
            if (_value == '') {
                    $(this).val(title);
            }
        });
    }