<?php
class Hnamcare_SectionController extends Zend_Controller_Action
{

    public function init()
    {

    }

    public function headerAction()
    {
        $this->view->headLink()->appendStylesheet("/hcare/css/bootstrap.css?v=" . Globals::getVersion());
        $this->view->headLink()->appendStylesheet("/hcare/css/font-awesome.min.css?v=" . Globals::getVersion());
    	$this->view->headLink()->appendStylesheet("/hcare/css/slick.css?v=" . Globals::getVersion());
        $this->view->headLink()->appendStylesheet("/hcare/css/main.css?v=" . Globals::getVersion());
        $this->view->headLink(array('rel' => 'shortcut icon', 'href' => Globals::getStaticUrl().'/hcare/images/favicon.png', 'type' => 'image/x-icon'), 'PREPEND');
        $this->view->headScript()->appendFile(Globals::getStaticUrl() . "/hcare/js/jquery.min.js?v=" . Globals::getVersion());

        $main_menu = array(
            'home' => array(
                'title' => 'Trang chủ',
                'link' => Globals::getStaticUrl(),
            ),
            'about' => array(
                'title' => 'Giới thiệu',
                'link' => Globals::getStaticUrl().'/gioi-thieu',
            ),
            'service' => array(
                'title' => 'Dịch vụ sửa chữa',
                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua',
            ),
            'news' => array(
                'title' => 'Tin tức',
                'link' => Globals::getStaticUrl().'/tin-tuc',
            ),
            'guarantee' => array(
                'title' => 'Bảo hành',
                'link' => Globals::getStaticUrl().'/bao-hanh',
            ),
            'contact' => array(
                'title' => 'Liên hệ',
                'link' => Globals::getStaticUrl().'/lien-he',
            ),
            'recruitment' => array(
                'title' => 'Tuyển dụng',
                'link' => Globals::getStaticUrl().'/tuyen-dung',
            ),
        );

        $service_menu = array(
            'iphone' => array(
                'title' => 'iPhone',
                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/iphone',
                'menu' => array(
                    array(
                        'title' => '',
                        'link' => '',
                        'menu' => array(
                            array(
                                'title' => 'iPhone 4',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/iphone-4',
                            ),
                            array(
                                'title' => 'iPhone 4S',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/iphone-4s',
                            ),
                            array(
                                'title' => 'iPhone 5',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/iphone-5',
                            ),
                            array(
                                'title' => 'iPhone 5C',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/iphone-5c',
                            ),
                            array(
                                'title' => 'iPhone 5S',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/iphone-5s',
                            ),
                        ),
                    ),
                    array(
                        'title' => '',
                        'link' => '',
                        'menu' => array(
                            array(
                                'title' => 'iPhone SE',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/iphone-se',
                            ),
                            array(
                                'title' => 'iPhone 6',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/iphone-6',
                            ),
                            array(
                                'title' => 'iPhone 6 Plus',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/iphone-6-plus',
                            ),
                            array(
                                'title' => 'iPhone 6S',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/iphone-6s',
                            ),
                            array(
                                'title' => 'iPhone 6S Plus',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/iphone-6s-plus',
                            ),
                        ),
                    ),
                    array(
                        'title' => '',
                        'link' => '',
                        'menu' => array(
                            array(
                                'title' => 'iPhone 7',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/iphone-7',
                            ),
                            array(
                                'title' => 'iPhone 7 Plus',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/iphone-7-plus',
                            ),
                            array(
                                'title' => 'iPhone 8',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/iphone-8',
                            ),
                            array(
                                'title' => 'iPhone 8 Plus',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/iphone-8-plus',
                            ),
                            array(
                                'title' => 'iPhone X',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/iphone-x',
                            ),
                        ),
                    ),
                ),
            ),
            'ipad' => array(
                'title' => 'iPad',
                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/ipad',
                'menu' => array(
                    array(
                        'title' => 'Thay thế linh kiện',
                        'link' => '',
                        'menu' => array(
                            array(
                                'title' => 'Thay pin máy tính bảng iPad',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/thay-pin-may-tinh-bang-ipad',
                            ),
                            array(
                                'title' => 'Thay kính cảm ứng máy tính bảng iPad',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/thay-kinh-cam-ung-may-tinh-bang-ipad',
                            ),
                            array(
                                'title' => 'Thay màn hình máy tính bảng iPad',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/thay-man-hinh-may-tinh-bang-ipad',
                            ),
                        ),
                    ),
                    array(
                        'title' => 'Sửa chữa phần cứng',
                        'link' => '',
                        'menu' => array(
                            array(
                                'title' => 'Sửa main: wifi máy tính bảng iPad',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/sua-main-wifi-may-tinh-bang-ipad',
                            ),
                            array(
                                'title' => 'Sửa main: usb sạc máy tính bảng iPad',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/sua-main-usb-sac-may-tinh-bang-ipad',
                            ),
                            array(
                                'title' => 'Sửa main: nguồn máy tính bảng iPad',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/sua-main-nguon-may-tinh-bang-ipad',
                            ),
                        ),
                    ),
                    array(
                        'title' => 'Mở iCloud',
                        'link' => '',
                        'menu' => array(
                            array(
                                'title' => 'Mở iCloud iPad Wifi',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/mo-icloud-ipad-wifi',
                            ),
                            array(
                                'title' => 'Mở iCloud 3G thành Wifi',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/mo-icloud-3g-thanh-wifi',
                            ),
                        ),
                    ),
                ),
            ),
            'samsung' => array(
                'title' => 'Samsung',
                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/samsung',
                'menu' => array(
                    array(
                        'title' => '',
                        'link' => '',
                        'menu' => array(
                            array(
                                'title' => 'Samsung Note 8',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/samsung-note-8',
                            ),
                            array(
                                'title' => 'Samsung Note FE',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/samsung-note-fe',
                            ),
                            array(
                                'title' => 'Samsung Note 5',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/samsung-note-5',
                            ),
                            array(
                                'title' => 'Samsung Note 4',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/samsung-note-4',
                            ),
                            array(
                                'title' => 'Samsung Note 3',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/samsung-note-3',
                            ),
                            array(
                                'title' => 'Samsung J7 Prime',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/samsung-j7-prime',
                            ),
                            array(
                                'title' => 'Samsung J7 2016',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/samsung-j7-2016',
                            ),
                            array(
                                'title' => 'Samsung J7 2015',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/samsung-j7-2015',
                            ),
                        ),
                    ),
                    array(
                        'title' => '',
                        'link' => '',
                        'menu' => array(
                            array(
                                'title' => 'Samsung J5 2016',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/samsung-j5-2016',
                            ),
                            array(
                                'title' => 'Samsung J5 2015',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/samsung-j5-2015',
                            ),
                            array(
                                'title' => 'Samsung A9 Pro',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/samsung-a9-pro',
                            ),
                            array(
                                'title' => 'Samsung A9',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/samsung-a9',
                            ),
                            array(
                                'title' => 'Samsung A8',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/samsung-a8',
                            ),
                            array(
                                'title' => 'Samsung A7 2016',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/samsung-a7-2016',
                            ),
                            array(
                                'title' => 'Samsung A7 2015',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/samsung-a7-2015',
                            ),
                        ),
                    ),
                    array(
                        'title' => '',
                        'link' => '',
                        'menu' => array(
                            array(
                                'title' => 'Samsung A5 2016',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/samsung-a5-2016',
                            ),
                            array(
                                'title' => 'Samsung A5 2015',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/samsung-a5-2015',
                            ),
                            array(
                                'title' => 'Samsung A3 2016',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/samsung-a3-2016',
                            ),
                            array(
                                'title' => 'Samsung A3 2015',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/samsung-a3-2015',
                            ),
                            array(
                                'title' => 'Samsung Note Edge',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/samsung-note-egde',
                            ),
                            array(
                                'title' => 'Samsung S4',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/samsung-s4',
                            ),
                            array(
                                'title' => 'Samsung S5',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/samsung-s5',
                            ),
                        ),
                    ),
                    array(
                        'title' => '',
                        'link' => '',
                        'menu' => array(
                            array(
                                'title' => 'Samsung S6',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/samsung-s6',
                            ),
                            array(
                                'title' => 'Samsung S6 Edge',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/samsung-s6-edge',
                            ),
                            array(
                                'title' => 'Samsung S6 Edge Plus',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/samsung-s6-edge-plus',
                            ),
                            array(
                                'title' => 'Samsung S7',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/samsung-s7',
                            ),
                            array(
                                'title' => 'Samsung S7 Edge',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/samsung-s7-edge',
                            ),
                            array(
                                'title' => 'Samsung S8',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/samsung-s8',
                            ),
                            array(
                                'title' => 'Samsung S8 Plus',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/samsung-s8-plus',
                            ),
                        ),
                    ),
                ),
            ),
            'asus' => array(
                'title' => 'Asus',
                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/asus',
                'menu' => array(
                    array(
                        'title' => 'Thay thế linh kiện',
                        'link' => '',
                        'menu' => array(
                            array(
                                'title' => 'Thay pin điện thoại Asus',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/thay-pin-dien-thoai-asus',
                            ),
                            array(
                                'title' => 'Thay màn hình điện thoai Asus',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/thay-man-hinh-dien-thoai-asus',
                            ),
                            array(
                                'title' => 'Thay kính cảm ứng điện thoai Asus',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/thay-kinh-cam-ung-dien-thoai-asus',
                            ),
                        ),
                    ),
                    array(
                        'title' => 'Sửa chữa phần cứng',
                        'link' => '',
                        'menu' => array(
                            array(
                                'title' => 'Sửa main: nguồn điện thoại Asus',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/sua-main-nguon-dien-thoai-asus',
                            ),
                            array(
                                'title' => 'Sửa main: usb sạc điện thoại Asus',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/sua-main-usb-sac-dien-thoai-asus',
                            ),
                            array(
                                'title' => 'Sửa main: wifi điện thoại Asus',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/sua-main-wifi-dien-thoai-asus',
                            ),
                        ),
                    ),
                ),
            ),
            'sony' => array(
                'title' => 'Sony',
                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/sony',
                'menu' => array(
                    array(
                        'title' => 'Thay thế linh kiện',
                        'link' => '',
                        'menu' => array(
                            array(
                                'title' => 'Thay pin điện thoại Sony',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/thay-pin-dien-thoai-sony',
                            ),
                            array(
                                'title' => 'Thay màn hình điện thoai Sony',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/thay-man-hinh-dien-thoai-sony',
                            ),
                            array(
                                'title' => 'Thay kính cảm ứng điện thoai Sony',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/thay-kinh-cam-ung-dien-thoai-sony',
                            ),
                        ),
                    ),
                    array(
                        'title' => 'Sửa chữa phần cứng',
                        'link' => '',
                        'menu' => array(
                            array(
                                'title' => 'Sửa main: nguồn điện thoại Sony',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/sua-main-nguon-dien-thoai-sony',
                            ),
                            array(
                                'title' => 'Sửa main: usb sạc điện thoại Sony',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/sua-main-usb-sac-dien-thoai-sony',
                            ),
                            array(
                                'title' => 'Sửa main: wifi điện thoại Sony',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/sua-main-wifi-dien-thoai-sony',
                            ),
                        ),
                    ),
                ),
            ),
            'xiaomi' => array(
                'title' => 'Xiaomi',
                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/xiaomi',
                'menu' => array(
                    array(
                        'title' => 'Thay thế linh kiện',
                        'link' => '',
                        'menu' => array(
                            array(
                                'title' => 'Thay pin điện thoại Xiaomi',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/thay-pin-dien-thoai-xiaomi',
                            ),
                            array(
                                'title' => 'Thay màn hình điện thoai Xiaomi',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/thay-man-hinh-dien-thoai-xiaomi',
                            ),
                            array(
                                'title' => 'Thay ép kính điện thoai Xiaomi',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/thay-man-hinh-dien-thoai-xiaomi',
                            ),
                            array(
                                'title' => 'Thay kính cảm ứng điện thoai Xiaomi',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/thay-kinh-cam-ung-dien-thoai-xiaomi',
                            ),
                        ),
                    ),
                    array(
                        'title' => 'Sửa chữa phần cứng',
                        'link' => '',
                        'menu' => array(
                            array(
                                'title' => 'Sửa main: nguồn điện thoại Xiaomi',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/sua-main-nguon-dien-thoai-xiaomi',
                            ),
                            array(
                                'title' => 'Sửa main: usb sạc điện thoại Xiaomi',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/sua-main-usb-sac-dien-thoai-xiaomi',
                            ),
                            array(
                                'title' => 'Sửa main: wifi điện thoại Xiaomi',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/sua-main-wifi-dien-thoai-xiaomi',
                            ),
                        ),
                    ),
                    array(
                        'title' => 'Mở MiCloud',
                        'link' => '',
                        'menu' => array(
                            array(
                                'title' => 'Mở MiCloud',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/mo-miCloud',
                            ),
                            array(
                                'title' => 'Sửa main: usb sạc điện thoại Xiaomi',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/sua-main-usb-sac-dien-thoai-xiaomi',
                            ),
                            array(
                                'title' => 'Sửa main: wifi điện thoại Xiaomi',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/sua-main-wifi-dien-thoai-xiaomi',
                            ),
                        ),
                    ),
                ),
            ),
            'lg' => array(
                'title' => 'LG',
                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/lg',
                'menu' => array(
                    array(
                        'title' => 'Thay thế linh kiện',
                        'link' => '',
                        'menu' => array(
                            array(
                                'title' => 'Thay pin điện thoại LG',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/thay-pin-dien-thoai-lg',
                            ),
                            array(
                                'title' => 'Thay màn hình điện thoai LG',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/thay-man-hinh-dien-thoai-lg',
                            ),
                            array(
                                'title' => 'Thay kính cảm ứng điện thoai LG',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/thay-kinh-cam-ung-dien-thoai-lg',
                            ),
                        ),
                    ),
                    array(
                        'title' => 'Sửa chữa phần cứng',
                        'link' => '',
                        'menu' => array(
                            array(
                                'title' => 'Sửa main: nguồn điện thoại LG',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/sua-main-nguon-dien-thoai-lg',
                            ),
                            array(
                                'title' => 'Sửa main: usb sạc điện thoại LG',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/sua-main-usb-sac-dien-thoai-lg',
                            ),
                            array(
                                'title' => 'Sửa main: wifi điện thoại LG',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/sua-main-wifi-dien-thoai-lg',
                            ),
                        ),
                    ),
                ),
            ),
            'oppo' => array(
                'title' => 'OPPO',
                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/oppo',
                'menu' => array(
                    array(
                        'title' => 'Thay thế linh kiện',
                        'link' => '',
                        'menu' => array(
                            array(
                                'title' => 'Thay pin điện thoại OPPO',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/thay-pin-dien-thoai-oppo',
                            ),
                            array(
                                'title' => 'Thay màn hình điện thoai OPPO',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/thay-man-hinh-dien-thoai-oppo',
                            ),
                            array(
                                'title' => 'Thay kính cảm ứng điện thoai OPPO',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/thay-kinh-cam-ung-dien-thoai-oppo',
                            ),
                        ),
                    ),
                ),
            ),
            'htc' => array(
                'title' => 'HTC',
                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/htc',
                'menu' => array(
                    array(
                        'title' => 'Thay thế linh kiện',
                        'link' => '',
                        'menu' => array(
                            array(
                                'title' => 'Thay pin điện thoại HTC',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/thay-pin-dien-thoai-htc',
                            ),
                            array(
                                'title' => 'Thay màn hình điện thoai HTC',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/thay-man-hinh-dien-thoai-htc',
                            ),
                            array(
                                'title' => 'Thay kính cảm ứng điện thoai HTC',
                                'link' => Globals::getStaticUrl().'/dich-vu-sua-chua/thay-kinh-cam-ung-dien-thoai-htc',
                            ),
                        ),
                    ),
                ),
            ),
        );

        $this->view->main_menu = $main_menu;
        $this->view->service_menu = $service_menu;
    }

    public function footerAction()
    {
        $this->view->inlineScript()->appendFile(Globals::getStaticUrl() . "/hcare/js/slick.min.js?v=" . Globals::getVersion());
        $this->view->inlineScript()->appendFile(Globals::getStaticUrl() . "/hcare/js/main.js?v=" . Globals::getVersion());
    }
}