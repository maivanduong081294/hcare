<?php

class Business_Helpers_Spin {

    private static $_instance = null;
    public $_session = null;
    public $_userinfo = null;

    // module news to store

    function __construct() {
        $this->_session = new Zend_Session_Namespace('session_app');
        $this->_userinfo = $this->_session->userinfo;
    }

    public static function getPromotion($pDetail, $bbcode) {
        $brands = array("Apple","Sony","Samsung","Wiko","Oppo","Microsoft","Lg","Blackberry","Mobell","Hp","Philips","Lenovo","Asus");
        $bonus = array("khuyến mãi","ưu đãi","chương trình khuyến mãi","chương trình ưu đãi");
        if($bbcode==1) {
            $links = array(
                "[url=http://www.hnammobile.com]điện thoại giá rẻ[/url]",
                "[url=http://www.hnammobile.com/loai-dien-thoai/kho-may-cu.53.html]điện thoại cũ[/url]",
                );
        } else {
            $links = array(
                "<a href='http://www.hnammobile.com'>điện thoại giá rẻ</a>",
                "<a href='http://www.hnammobile.com/loai-dien-thoai/kho-may-cu.53.html'>điện thoại cũ</a>",
            );
        }
        
        shuffle($links);
        shuffle($brands);
        $_link = $links[0];
        $_brand1 = $brands[0];
        $_brand2 = $brands[1];
        $_brand3 = $brands[2];
        $_brand4 = $brands[3];
        $_title = $pDetail["title"];
        shuffle($bonus);
        $_bonus = $bonus[0];
        
        $p1 = array(
            "Song song với những dịch vụ khách hàng nhiều $_bonus, hệ thống điện thoại chính hãng Hnam Mobile cung cấp $_link còn triển khai loạt chương trình khuyến mãi kết hợp với những thương hiệu công nghệ hàng đầu thế giới như “Cưỡi SH – Rước Vespa – Lướt Wiko miễn phí” cùng Wiko; “Đón năm mới – Nhận quà công nghệ sành điệu” với $_brand1… ",
            "Đồng thời, khi đến với Hnam Mobile quý khách hàng còn có thể chọn mua cho mình hàng loạt sản phẩm $_link cấu hình mạnh, thương hiệu tốt, có giá giảm sốc tại Hnam Mobile như: $_title hay loạt sản phẩm từ $_brand1, $_brand2…",
        );
        $p2 = array(
            "Cùng với những dịch vụ khách hàng tuyệt vời, Hnam Mobile vói các sản phẩm triển khai nhiều chương trình khuyến mãi kết hợp cùng những thương hiệu nổi tiếng như $_brand1, $_brand2, $_brand3, $_brand4…",
            "Đồng thời, ghé đến Hnam Mobile, bạn sẽ có nhiều lựa chọn với những sản phẩm cấu hình tốt, thương hiệu mạnh, giá giảm sốc tại Hnam Mobile như: $_brand1, $_brand2, $_brand3, $_brand4…",
        );
        $p3 = array();
        
        shuffle($p1);
        shuffle($p2);
        shuffle($p3);

        $p1 = $p1[0];
        $p2 = $p2[0];
        $p3 = $p3[0];
        $arr = array($p1,$p2,$p3);
        shuffle($arr);
        
        $prefixs = array("Giới thiệu","Thông tin về","Những");
        shuffle($prefixs);
        $prefix = $prefixs[0];
        if($bbcode==1) {
            $title = "[b]$prefix chương trình khuyến mại[/b]";
        } else {
            $title = "<b>$prefix chương trình khuyến mại</b>";
        }
        return $title."<p>".implode("</p><p>", $arr)."</p>";
        
    }
    private static function removeHTML($text) {
        $ret = str_replace("<br", "___br", $text);
        $ret = strip_tags($ret);
        $ret = str_replace("___br", "<br", $ret);
        return $ret;
    }
    public static function thongsokythuat($pDetail, $bbcode){
        $_title              = $pDetail["title"];
        $_fullcontent               = $pDetail["fullcontent"];
        $_title1 = Business_Common_Utils::adaptTitleLinkURLSEO($_title);
        $link = SEOPlugin::getAccesoriesDetailLink($pDetail["itemid"], $_title1);
        if($bbcode==1) {
            $url = "[url=$link]".$_title."[/url]";
            $_title = "- Thông số kỹ thuật của [b] $url [/b]";
        } else {
            $url = "<a href='$link'>".$_title."</a>";
            $_title = "- Thông số kỹ thuật của <b> $url </b>";
        }
        $fullcontent        = self::removeHTML($_fullcontent);
        $fullcontent        = "$_title <br/>".$fullcontent."<br>";
        $top_sm         = self::getTopSmartPhone($pDetail, $bbcode);
        $arr = array($fullcontent,$top_sm);
        shuffle($arr);
        return implode("</p><p>", $arr);
    }
    public static function getTopSmartPhone($pDetail, $bbcode) {
        $_title = $pDetail["title"];
        $title1= "- Top các phụ kiện điện thoại bán chạy nhất <b>Hnammobile.com</b>";
        $title2= "- Các phụ kiện mua nhiều nhất tại <b> Hnammobile.com </b>";
        
        $title = array($title1,$title2);
        shuffle($title);
        $title  = $title[0];
        
        $p1 = array(
            "Miếng dán cường lực:  Có khả năng bảo vệ màn hình điện thoại khỏi vỡ, hư hại, trầy xước... khi bị lực tác động cực mạnh. ",
            "Cáp sạc cổng micro USB: Giúp chuyển dữ liệu từ smartphone qua máy tính dễ dàng, nhanh gọn. Đồng thời, cáp sạc cổng micro USB còn được dùng để sạc pin cho thiết bị của bạn.",
        );
        $p2 = array(
            "Thẻ nhớ: Giúp bạn mở rộng bộ nhớ của smartphone để cài đặt thêm ứng dụng, trò chơi và lưu trữ thoải mái hình ảnh, nhạc, video...",
            "Bao da iPhone, iPad: được làm từ nguyên liệu chất lượng cao, màu sắc và mẫu mã đa dạng. Được cung cấp bởi các thương hiệu sản xuất cover uy tín: Viva, Uniq, Ozaki, Spigen, SGP, Tucano, Baseus, Hoco, X-Fitted, Usams, G-Case, Yolope, Coteetci..."
        );
        $p3=array(
            "Loa di động: Loa sử dụng công nghệ Bluetooth và NFC giúp kết nối không dây với smartphone, tablet nhanh chóng và tiện lợi, cho chất lượng âm thanh sôi động.",
        );
        shuffle($p1);
        shuffle($p2);
        shuffle($p3);

        $p1 = $p1[0];
        $p2 = $p2[0];
        $p3 = $p3[0];
        $arr = array($p1,$p2,$p3);
        shuffle($arr);
        
        if($bbcode==1) {
            $title = "$title ngoài [b] $_title  [/b]";
        } else {
            $title = "$title ngoài<b> $_title </b>";
        }
        return $title.implode("</p><p>", $arr);
        
    }
    public static function getService($pDetail, $bbcode) {
        $title1= "- Dịch vụ và ưu đãi khi mua hàng tại <b>Hnammobile.com?</b>";
        $title2= "- Vì sao nên mua phụ kiện, điện thoại, máy tính bảng tại <b>Hnammobile.com</b>";
        $title = array($title1,$title2);
        shuffle($title);
        $title  = $title[0];
        
        $p1 = array(
            "Sản phẩm chính hãng 100%: : Hnam Mobile hiện đang phân phối sản phẩm của các thương hiệu nổi tiếng như: Apple, Samsung, Sony, LG, Wiko, ASUS, HTC, Blackberry…",
            "Giá cạnh tranh: Hnam Mobile tự hào là địa chỉ mua sắm tin cậy với mức giá tốt nhất trên thị trường hiện nay. Không chỉ vậy, khi mua hàng tại Hnam Mobile, bạn còn được hưởng thêm nhiều ưu đãi đặc biệt: trả góp 0%, tặng quà, giảm giá, bốc thăm trúng thưởng…",
            "Chính sách “Đổi trả 20 ngày”: Trong 20 ngày mua máy, nếu máy bị lỗi do nhà sản xuất, Hnam Mobile đổi ngay máy mới cho bạn trong 24h - 48h kể từ khi nhận máy.",
            "Ưu đãi mua gói bảo hành mở rộng: để kéo dài thời gian bảo hành lên đến 2 năm (24 tháng)."
        );
        $p2 = array(
            "Sản phẩm đa dạng:  Liên tục cập nhật và đón đầu xu hướng công nghệ tương lai, các sản phẩm tại Hnam Mobile luôn đa dạng về: mẫu mã, màu sắc, tính năng, giá cả, thương hiệu…",
            "Mua máy với giá tốt nhất: Hnam Mobile là hệ thống bán lẻ điện thoại, máy tính bảng, laptop uy tín và có mức giá cực kỳ cạnh tranh. Ngoài ra, Hnam Mobile còn tăng thêm ưu đãi bằng các chương trình khuyến mãi: tặng quà, tặng phiếu giảm giá tiền mặt, tặng phiếu bốc thăm trúng thưởng lớn... cho tất cả khách hàng.",
            "Chế độ bảo hành chuyên nghiệp:  Tất cả sản phẩm tại Hnam Mobile là hàng chính hãng và được hưởng chế độ bảo hành, đổi trả chính hãng. Đồng thời, Hnam Mobile còn có 2 Trung tâm sửa chữa – Bảo hành riêng để tư vấn, bảo hành, hỗ trợ sửa chữa, cài đặt ứng dụng trọn đời… cho sản phẩm của bạn.",
            "Miễn phí cài đặt ứng dụng trọn đời cho máy.Ưu đãi trả góp lãi suất 0%."
        );
        $p3=array(
            "Khi mua điện thoại, máy tính bảng, laptop tại Hnam Mobile, bạn được hưởng các ưu đãi vô cùng hấp dẫn sau:",
            "Đặt mua điện thoại, máy tính bảng, laptop tại website Hnammobile.com, bạn được hưởng ngay dịch vụ: Giao hàng miễn phí – Thu tiền tận nơi trên toàn quốc."
        );
        shuffle($p1);
        shuffle($p2);
        shuffle($p3);

        $p1 = $p1[0];
        $p2 = $p2[0];
        $p3 = $p3[0];
        $arr = array($p1,$p2,$p3);
        shuffle($arr);
        
        if($bbcode==1) {
            $title = "$title ";
        } else {
            $title = "$title ";
        }
        return $title.implode("</p><p>", $arr);
        
    }
    public static function getImageAcc($pDetail, $bbcode) {
        $thumb = json_decode($pDetail['thumb']);
        $thumb2 = json_decode($thumb->thumb2);
        $image = "http://stcv4.hnammobile.com/uploads/accesories/details/" . $thumb2[0];
        if ($bbcode==0)
            $str = "<img style='max-height:auto!important;max-width:auto!important' src='".$image."' alt='".$pDetail["title"]."'/>";
        else
            $str = "[img]".$image."[/img]";
        return $str;
    }
    public static function getIntroAcc($pDetail, $bbcode) {
        $locations = array("Toàn quốc","TPHCM","TP Hồ Chí Minh","Hà Nội","Đà nẵng");
        $location = $locations[array_rand($locations)];
        $prices = array("giá tốt","giá rẻ","giá cạnh tranh");
        $price = $prices[array_rand($prices)];
        $_title = Business_Common_Utils::adaptTitleLinkURLSEO($pDetail["title"]);
        $link = SEOPlugin::getAccesoriesDetailLink($pDetail["itemid"], $_title);
        $_menu = Business_Ws_MenuItem::getInstance();
        $mdetail = $_menu->getDetail($pDetail["cateid"]);
//        $mTitle = Business_Common_Utils::adaptTitleLinkURLSEO($mdetail["title"]);
        $mTitle = $mdetail["title"];
        $linkParent = SEOPlugin::getAccesoriesLink($pDetail["cateid"], Business_Common_Utils::adaptTitleLinkURLSEO($mTitle));
        $fullcontent        = $pDetail["fullcontent"];
        $mTitle = " $mTitle";
//        $pTitleSEO = Business_Common_Utils::adaptTitleLinkURLSEO($pDetail["title"]);
        
//        $titleDetail = $mdetail["title"];
//        $titleDetail = "phụ kiện " . strtolower($pDetail["title"]);
        $title_r = array(
            "Phụ kiện điện thoại",
            "Phụ kiện giá rẻ",
            "Phụ kiện giá rẻ tphcm",
            "Phụ kiện điện thoại tphcm"
        );
        shuffle($title_r);
        $title_rad = $title_r[0];
        $link_rad = "http://www.hnammobile.com/phu-kien/";
        if($bbcode==1) {
            $links = array(
//                "[url=$link]".$titleDetail."[/url]",
                "[url=$linkParent]".$mTitle."[/url]",
                
                );
        } else {
            $links = array(
                "<a href='$link_rad'>".$title_rad."</a>",
                "<a href='$linkParent'>".$mTitle."</a>",
                
            );
        }
        
        shuffle($links);
        $_link1 = $links[0];
        
        $p1 = array(
            "Tiên phong trong lĩnh vực bán lẻ sản phẩm công nghệ $_link1 trong suốt gần 9 năm hoạt động, đến nay hệ thống Hnam Mobile đã có mặt trên khắp các quận thành khu vực TP.HCM với số lượng 14 showroom.",
            "Dẫn đầu trong lĩnh vực kinh doanh/ bán lẻ sản phẩm $_link1 trên thị trường kinh doanh những sản phẩm công nghệ suốt gần 9 năm qua, hiện tại hệ thống Hnam Mobile đã có đến 15 showroom toàn  TP.HCM và ngày càng phát triển thêm .",
            "Là một trong những hệ thống bán lẻ sản phẩm $_link1 công nghệ uy tín hàng đầu TP.HCM, Hnam Mobile hiện đang phát triển thêm các cửa hàng trên khắp các quận thành với định hướng mang đến giá trị đích thực cho người dùng.",
        );
        $p2 = array(
            "Vừa qua, hệ thống đã liên tiếp đón nhận tin vui khi nhận những giải thưởng như “Nhà bán lẻ xuất sắc khu vực miền Nam” từ Samsung Mobile Việt Nam hay giải “Asus Best Partner 2014” với thành tích đơn vị bán lẻ Hỗ trợ dịch vụ xuất sắc từ Asus Việt Nam, và điều quan trọng hơn hết là Hnam Mobile đã luôn nhận được sự quan tâm ủng hộ từ người tiêu dùng trong suốt gần 9 năm qua – đó thực sự là nguồn động viên to lớn dành cho hệ thống.",
            "Mới đây, hệ thống Hnam Mobile đã liên tục đón những tin vui khi đón nhận những giải thưởng ghi nhận thành quả từ những thương hiệu công nghệ hàng đầu như “Nhà bán lẻ xuất sắc khu vực miền Nam” và “Đơn vị bán lẻ Hỗ trợ dịch vụ xuất sắc” từ Samsung và Asus Việt Nam. Trên hết, là hệ thống đã luôn được nhiều sự ủng hộ quan tâm từ khách hàng trong suốt gần 9 năm qua – quả thực đây là nguồn động viên to lớn dành cho Hnam Mobile.",
            "Vừa qua, hệ thống đã liên tiếp đón nhận tin vui khi nhận những giải thưởng như “Nhà bán lẻ xuất sắc khu vực miền Nam” từ Samsung Mobile Việt Nam hay giải “Asus Best Partner 2014” với thành tích đơn vị bán lẻ Hỗ trợ dịch vụ xuất sắc từ Asus Việt Nam. Trên hết, là hệ thống đã luôn được nhiều sự ủng hộ quan tâm từ khách hàng trong suốt gần 9 năm qua – quả thực đây là nguồn động viên to lớn dành cho Hnam Mobile.",
        );
        $p3 = array(
            "Không chỉ tập trung phát triển hệ thống bán lẻ, Hnam Mobile còn chú trọng rất nhiều đến khâu dịch vụ bảo hành và chăm sóc khách hàng. Kết hợp với những đối tác hàng đầu ở nhiều lĩnh vực như: Ngân hàng Shinhan, công ty bảo hiểm AAA, GrabTaxi…Hnam Mobile đã ra mắt hàng loạt dịch vụ cao cấp như: Dịch vụ trả góp 0% lãi suất, Bảo hành mở rộng 24 tháng, hay như những ưu đãi đi taxi miễn phí dành cho khách hàng… Đến với Hnam Mobile để được hưởng ngay 365 ngày dịch vụ ưu đãi hấp dẫn nhất!",
            "Song song với việc phát triển hệ thống bán lẻ, Hnam Mobile chú tâm đến việc phát triển khâu dịch vụ bảo hành và chăm sóc khách hàng.Cùng với những đối tác như: Ngân hàng Shinhan, công ty bảo hiểm AAA, GrabTaxi…Hnam Mobile đã ra mắt đa dạng dịch vụ cao cấp như: Dịch vụ trả góp 0% lãi suất, Bảo hành mở rộng, ưu đãi đi taxi miễn phí dành cho khách hàng…Ghé ngay Hnam Mobile để được hưởng ngay những ưu đãi hấp dẫn nhất này ngay nào!",
            "Không chỉ tập trung phát triển hệ thống bán lẻ, Hnam Mobile còn chú trọng rất nhiều đến khâu dịch vụ bảo hành và chăm sóc khách hàng. Cùng với những đối tác như: Ngân hàng Shinhan, công ty bảo hiểm AAA, GrabTaxi…Hnam Mobile đã ra mắt đa dạng dịch vụ cao cấp như: Dịch vụ trả góp 0% lãi suất, Bảo hành mở rộng, ưu đãi đi taxi miễn phí dành cho khách hàng…Đến với Hnam Mobile để được hưởng ngay 365 ngày dịch vụ ưu đãi hấp dẫn nhất!",
        );
        
        $p4 = array(
            "Hnam Mobile chuyên bán lẻ tất cả phụ kiện điện thoại, máy tính bảng, laptop... chất lượng cao như: pin sạc dự phòng, sạc (củ sạc), cáp (cable) các loại, thẻ nhớ, tai nghe, tai nghe bluetooth, kính cường lực, miếng dán màn hình, ốp lưng, bao da, gậy chụp hình selfie, loa di động, chuột máy tính, túi đựng máy tính, phụ kiện iPhone, phụ kiện Macbook...",
            "Hnam Mobile bảo đảm phụ kiện là hàng chính hãng 100%, có nguồn gốc rõ ràng, giá tốt, bảo hành từ 6 – 12 tháng...",
            "Hnam Mobile là Hệ thống bán lẻ điện thoại di động, máy tính bảng, laptop chính hãng uy tín với 14 cửa hàng và 2 trung tâm bảo hành chuyên nghiệp tại TP HCM.",
//            "Hnammobile chuyên cung cấp các dòng điện thoại ". $_link2. " chính hãng $price tại $location",
        );        
//        $p5 = array(
//            "Thông số kỹ thuật <br/> $fullcontent",
//        );
        shuffle($p1);
        shuffle($p2);
        shuffle($p3);
        shuffle($p4);
//        shuffle($p5);

        $p1 = $p1[0];
        $p2 = $p2[0];
        $p3 = $p3[0];
        $p4 = $p4[0];
        $p5 = $p5[0];
//        $arr = array($p1,$p2,$p3,$p4,$p5);
        $arr = array($p1,$p2,$p3,$p4);
        shuffle($arr);
        
        $prefixs = array("Giới thiệu","Thông tin","Về","Sơ lược");
        shuffle($prefixs);
        $prefix = $prefixs[0];
        if($bbcode==1) {
            $title = "[b]$prefix nhà cung cấp[/b]";
        } else {
            $title = "<b>$prefix nhà cung cấp</b>";
        }
        return $title."<p>".implode("</p><p>", $arr)."</p>";
        
    }
    public static function getSmartPhoneWhat($pDetail, $bbcode) {
        $title1= "- Smartphone của bạn cần gì?";
        $title2= "- Dế yêu của bạn muốn gì ?";
        $title = array($title1,$title2);
        shuffle($title);
        $title  = $title[0];
        
        $p1 = array(
            "Pin sạc dự phòng: Không lo hết pin giữa chừng, không bị nhỡ cuộc gọi, sạc dự phòng giúp sạc nhanh smartphone của bạn mọi lúc mọi nơi.",
            "Miếng dán màn hình: có tác dụng chống bụi bẩn và các vết trầy xước, giúp bảo vệ màn hình smartphone không bị hư hỏng và cảm ứng luôn nhạy như mới. Tặng miếng dán màn hình miễn phí khi mua điện thoại, máy tính bảng, laptop tại Hnam Mobile.",
        );
        $p2 = array(
            "Thẻ nhớ: Giúp bạn mở rộng bộ nhớ của smartphone để cài đặt thêm ứng dụng, trò chơi và lưu trữ thoải mái hình ảnh, nhạc, video...",
            "Tai nghe: Phục vụ tối đa nhu cầu giải trí (nghe nhạc, xem phim, chat voice) của bạn ở bất cứ đâu."
        );
        $p3=array(
            "Hnam Mobile là Hệ thống bán lẻ điện thoại di động, máy tính bảng, laptop chính hãng uy tín với 14 cửa hàng và 2 trung tâm bảo hành chuyên nghiệp tại TP HCM. ",
            "Hnam Mobile Giao hàng miễn phí – Thu tiền tận nơi trên toàn quốc cho đơn hàng từ 300.000đ trở lên."
        );
        shuffle($p1);
        shuffle($p2);
        shuffle($p3);

        $p1 = $p1[0];
        $p2 = $p2[0];
        $p3 = $p3[0];
        $arr = array($p1,$p2,$p3);
        shuffle($arr);
        
        if($bbcode==1) {
            $title = "$title ";
        } else {
            $title = "$title ";
        }
        return $title.implode("</p><p>", $arr);
        
    }
    public static function getIntro($pDetail, $bbcode) {
        $locations = array("Toàn quốc","TPHCM","TP Hồ Chí Minh","Hà Nội","Đà nẵng");
        $location = $locations[array_rand($locations)];
        $prices = array("giá tốt","giá rẻ","giá cạnh tranh");
        $price = $prices[array_rand($prices)];
        $_title = Business_Common_Utils::adaptTitleLinkURLSEO($pDetail["title"]);
        $link = SEOPlugin::getProductDetailLink($pDetail["itemid"], $_title);
        $linkTragop = SEOPlugin::getProductDetailLink($pDetail["itemid"], $_title) . "/mua-tra-gop";
        $_menu = Business_Ws_MenuItem::getInstance();
        $mdetail = $_menu->getDetail($pDetail["cateid"]);
        $mTitle = Business_Common_Utils::adaptTitleLinkURLSEO($mdetail["title"]);
        $linkParent = SEOPlugin::getProductLink($pDetail["cateid"], $mTitle);
        
        $mTitle = "điện thoại $mTitle";
        $pTitleSEO = Business_Common_Utils::adaptTitleLinkURLSEO($pDetail["title"]);
        
        $titleDetail = $mdetail["title"];
        $titleDetailTragop = "mua trả góp điện thoại " . strtolower($pDetail["title"]);
        $titleDetail = "điện thoại " . strtolower($pDetail["title"]);
        
        if($bbcode==1) {
            $links = array(
                "[url=$link]".$titleDetail."[/url]",
                "[url=$linkParent]".$mTitle."[/url]",
                
                );
        } else {
            $links = array(
                "<a href='$link'>".$titleDetail."</a>",
                "<a href='$linkParent'>".$mTitle."</a>",
                
            );
        }
        
        if($bbcode==1) {
            $linksTG = array(
                "[url=$linkTragop]".$titleDetailTragop."[/url]",
                );
        } else {
            $linksTG = array(
                "<a href='$linkTragop'>".$titleDetailTragop."</a>",
            );
        }
        
        shuffle($links);
        $_link1 = $links[0];
        $_link2 = $links[1];
        
        $_linkTG = $linksTG[0];
        
        $p1 = array(
            "Tiên phong trong lĩnh vực bán lẻ sản phẩm công nghệ $_link1 trong suốt gần 9 năm hoạt động, đến nay hệ thống Hnam Mobile đã có mặt trên khắp các quận thành khu vực TP.HCM với số lượng 12 showroom",
            "Dẫn đầu trong lĩnh vực kinh doanh/ bán lẻ sản phẩm $_link1 trên thị trường kinh doanh những sản phẩm công nghệ suốt gần 9 năm qua, hiện tại hệ thống Hnam Mobile đã có đến 12 showroom toàn  TP.HCM và ngày càng phát triển thêm ",
            "Là một trong những hệ thống bán lẻ sản phẩm $_link1 công nghệ uy tín hàng đầu TP.HCM, Hnam Mobile hiện đang phát triển thêm các cửa hàng trên khắp các quận thành với định hướng mang đến giá trị đích thực cho người dùng",
        );
        $p2 = array(
            "Vừa qua, hệ thống đã liên tiếp đón nhận tin vui khi nhận những giải thưởng như “Nhà bán lẻ xuất sắc khu vực miền Nam” từ Samsung Mobile Việt Nam hay giải “Asus Best Partner 2014” với thành tích đơn vị bán lẻ Hỗ trợ dịch vụ xuất sắc từ Asus Việt Nam, và điều quan trọng hơn hết là Hnam Mobile đã luôn nhận được sự quan tâm ủng hộ từ người tiêu dùng trong suốt gần 9 năm qua – đó thực sự là nguồn động viên to lớn dành cho hệ thống.",
            "Mới đây, hệ thống Hnam Mobile đã liên tục đón những tin vui khi đón nhận những giải thưởng ghi nhận thành quả từ những thương hiệu công nghệ hàng đầu như “Nhà bán lẻ xuất sắc khu vực miền Nam” và “Đơn vị bán lẻ Hỗ trợ dịch vụ xuất sắc” từ Samsung và Asus Việt Nam. Trên hết, là hệ thống đã luôn được nhiều sự ủng hộ quan tâm từ khách hàng trong suốt gần 9 năm qua – quả thực đây là nguồn động viên to lớn dành cho Hnam Mobile.",
            "Vừa qua, hệ thống đã liên tiếp đón nhận tin vui khi nhận những giải thưởng như “Nhà bán lẻ xuất sắc khu vực miền Nam” từ Samsung Mobile Việt Nam hay giải “Asus Best Partner 2014” với thành tích đơn vị bán lẻ Hỗ trợ dịch vụ xuất sắc từ Asus Việt Nam. Trên hết, là hệ thống đã luôn được nhiều sự ủng hộ quan tâm từ khách hàng trong suốt gần 9 năm qua – quả thực đây là nguồn động viên to lớn dành cho Hnam Mobile.",
        );
        $p3 = array(
            "Không chỉ tập trung phát triển hệ thống bán lẻ, Hnam Mobile còn chú trọng rất nhiều đến khâu dịch vụ bảo hành và chăm sóc khách hàng. Kết hợp với những đối tác hàng đầu ở nhiều lĩnh vực như: Ngân hàng Shinhan, công ty bảo hiểm AAA, GrabTaxi…Hnam Mobile đã ra mắt hàng loạt dịch vụ cao cấp như: Dịch vụ trả góp 0% lãi suất, Bảo hành mở rộng 24 tháng, hay như những ưu đãi đi taxi miễn phí dành cho khách hàng… Đến với Hnam Mobile để được hưởng ngay 365 ngày dịch vụ ưu đãi hấp dẫn nhất!",
            "Song song với việc phát triển hệ thống bán lẻ, Hnam Mobile chú tâm đến việc phát triển khâu dịch vụ bảo hành và chăm sóc khách hàng.Cùng với những đối tác như: Ngân hàng Shinhan, công ty bảo hiểm AAA, GrabTaxi…Hnam Mobile đã ra mắt đa dạng dịch vụ cao cấp như: Dịch vụ trả góp 0% lãi suất, Bảo hành mở rộng, ưu đãi đi taxi miễn phí dành cho khách hàng…Ghé ngay Hnam Mobile để được hưởng ngay những ưu đãi hấp dẫn nhất này ngay nào!",
            "Không chỉ tập trung phát triển hệ thống bán lẻ, Hnam Mobile còn chú trọng rất nhiều đến khâu dịch vụ bảo hành và chăm sóc khách hàng. Cùng với những đối tác như: Ngân hàng Shinhan, công ty bảo hiểm AAA, GrabTaxi…Hnam Mobile đã ra mắt đa dạng dịch vụ cao cấp như: Dịch vụ trả góp 0% lãi suất, Bảo hành mở rộng, ưu đãi đi taxi miễn phí dành cho khách hàng…Đến với Hnam Mobile để được hưởng ngay 365 ngày dịch vụ ưu đãi hấp dẫn nhất!",
        );
        
        $p4 = array(
            "Hnammobile chuyên cung cấp các dòng điện thoại ". $_link2. " chính hãng $price tại $location",
            "Bạn có thể ". $_linkTG. " chính hãng $price tại $location",
        );        
        
        shuffle($p1);
        shuffle($p2);
        shuffle($p3);
        shuffle($p4);

        $p1 = $p1[0];
        $p2 = $p2[0];
        $p3 = $p3[0];
        $p4 = $p4[0];
        $arr = array($p1,$p2,$p3,$p4);
        shuffle($arr);
        
        $prefixs = array("Giới thiệu","Thông tin","Về","Sơ lược");
        shuffle($prefixs);
        $prefix = $prefixs[0];
        if($bbcode==1) {
            $title = "[b]$prefix nhà cung cấp[/b]";
        } else {
            $title = "<b>$prefix nhà cung cấp</b>";
        }
        return $title."<p>".implode("</p><p>", $arr)."</p>";
        
    }
    
    public static function getImage($pDetail, $bbcode) {
        $pDetail = Business_Helpers_Products::updateProductDetail($pDetail);
        if ($bbcode==0)
            $str = "<img src='".$pDetail["thumb_home"][0]."' alt='".$pDetail["title"]."'/>";
        else
            $str = "[img]".$pDetail["thumb_home"][0]."[/img]";
        return $str;
    }
    
    public static function getPrice($pDetail) {
        $pDetail = Business_Helpers_Products::updateProductDetail($pDetail);
        $price = $pDetail["price"];
        if ($price == 0) {
            $price = $pDetail["original_price"];
        }
        return number_format($price)."đ";
    }
    
    public static function getTitle($raw_title) {
        $locations = array("Toàn quốc","TPHCM","TP Hồ Chí Minh","Hà Nội","Đà nẵng");
        $location = $locations[array_rand($locations)];
        
        $prefixs = array("Mua","Cần bán");
        $prefix = $prefixs[array_rand($prefixs)];
        
        $afters = array("giá rẻ","giá tốt");
        $after = $afters[array_rand($afters)];
        
//        echo "<pre>";
//        var_dump($raw_title, $location, $prefix, $after);
//        die();
            
        $_title = array($prefix, $raw_title, $after);
        $_title = implode(" ", $_title);
        $__title = array($_title, $location);
        shuffle($__title);
        $__title = implode(" - ", $__title);
        return $__title;
        
    }
    
    public static function getFeatures($itemid) {
        $_ph = Business_Helpers_Products::getInstance();
        $fList = $_ph->getFeaturesDetail($itemid);
        shuffle($fList);
        $flistShort = array_slice($fList, 0, 15);
        return $flistShort;        
    }
    
    public static function getProductDetail($itemid){
        $pDetail = null;
        $counter=0;
        if ($itemid==0){
            while(true) {
                if ($counter > 100) {break;}
                if ($pDetail==null) { 
                    $itemid = rand(1,5000);
                    $pDetail = Business_Ws_ProductsItem::getInstance()->getDetail($itemid);
                    if ($pDetail == null || $pDetail["productsid"]!=3 || $pDetail["onstock"]!=1) {
                        $pDetail=null;
                        continue;
                        $counter++;
                    }
                } else {
                    break;
                }
            }
        } else {
            $pDetail = Business_Ws_ProductsItem::getInstance()->getDetail($itemid);
        }
        return $pDetail;
    }
}

?>
