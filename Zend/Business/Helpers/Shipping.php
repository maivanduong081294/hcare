<?php
class Business_Helpers_Shipping
{
    private static $_instance = null;
    // module news to store
    
    function __construct()
    {

    }

    /**
     * get instance of Business_Helpers_Shipping
     *
     * @return Business_Helpers_Shipping
     */
    public static function getInstance()
    {
            if(self::$_instance == null)
            {
                    self::$_instance = new Business_Helpers_Shipping();
            }
            return self::$_instance;
    }

    public static function getShipPrice($type=''){
        switch($type){            
            default:
                return (int)Zend_Registry::get('configuration')->feeShipping;
                break;
                
        }
    }
    
    public static function getShipPayment($no){
        $status = array(
            '0' => 'Chưa thanh toán',
            '1' => 'Đã thanh toán'
        );
        if ($no==''){
            return $status;
        }
        return $status[$no];
    }
    

    public static function getShipStatus($no){

        $status = array(
            '0' => 'Chưa giao hàng',
            '1' => 'Đang giao hàng',
            '2' => 'Hoàn tất',
            '3' => 'Trả lại'
        );
        if ($no==''){
            return $status;
        }
        return $status[$no];
    }
    /*
     * return 0; // khu vuc ngoai TPHCM, nguoi nhan va nguoi mua la mot, thuc hien COD
     * return 1; // khu vuc ngoai thanh, va cac tinh lan can, nguoi nhan va nguoi mua khac nhau, chuyen khoan
     */
    public function getShipType($districtid, $info = array(), $info_order = array()){
//        $zone = array(1,2,3,4,5,6,7,10,11, Tân Bình, Bình Thạnh, Phú Nhuận);
        $zone = array(1,2,3,4,5,6,7,10,11,16,18,19);
        if (    in_array($districtid, $zone) &&
                $info['fullname'] == $info_order['fullname'] &&
                $info['address'] == $info_order['address'] &&
                $info['phone'] == $info_order['phone'] &&
                $info['cityid'] == $info_order['cityid'] &&
                $info['districtid'] == $info_order['districtid']
            )
            return 0;
        return 1;
    }

    public function getCompanyInfo(){
        $_contents = Business_Ws_Contents::getInstance();
        $detail = $_contents->getDetail(65);
        if ($detail['fullcontent'] == '' || is_null($detail['fullcontent'])){
            return 'Thêu tay Ninh Khương, 44 Lê Lợi, Q.1';
        }else
            return $detail['fullcontent'];
    }

    public function getShipInfo(){
        $_contents = Business_Ws_Contents::getInstance();
        $detail = $_contents->getDetail(64);
        return $detail['fullcontent'];
    }

    public function getShipFee($cityid, $districtid, $price, $weight){
        
        if ($cityid > 1){ // not TP HCM
            $districtid = $cityid;
            $internal = 0; // ngoai thanh
        }
        else{ // in TP HCM, check interal
            $interal_list = $this->getInternalList();
            if (in_array($districtid, $interal_list))
                $internal = 1;
            else
                $internal = 0;
        }

//        1,3,4,2,5,6,7,8,10,11,16,18,19,9,12,17,680,15,685,681,679,687,684,683
        $ship_rule1 = array(
            "districtid" => array(1,3,4),
            "fee" => 0,
            "range" => 0,
            "internal" => 1
        ); // quan thuoc TPHCM, mien phi giao hang o cac quan co ID 1,3,4,10
        
        $ship_rule2 = array(
            "districtid" => array(2,5,6,7,8,10,11,16,18,19),
            "fee" => 30000,
            "range" => 500000,
            "internal" => 1
        ); // quan thuoc TPHCM, tinh phi giao hang cho cac quan id: 2,5,6,7... neu gia tien lon hon range (300.000) la fee:20000
        
        $ship_rule3 = array(
            "districtid"=> array(9,12,17,680),
            "fee" => 40000,
            "range" => 700000,
            "internal" => 1
        ); // quan thuoc TPHCM

        $ship_rule4 = array(
            "districtid"=> array(15,685,681,679,687,684,683),
            "fee" => 0,
            "range" => 1000000,
            "internal" => 1
        ); // quan thuoc lan can TPHCM

        $ship_rule5 = array(
            "internal" => 0, // khu vuc ngoai thanh, va cac tinh lan can
            "weight_list" => array(50,100,250,500,1000,1500,2000),
            "weight_after_limit" => 500,           
            "fee_range" => array(
                array( // ngoai thanh
                    "districtid" => array(15,685,681,679,687,684,683),
                    "fee"     => array(10000,10000,12000,15000,18000,22000,25000),//tien tuong ung voi weight_list
                    "fee_after_limit" => 2000
                ),
                array( // vung 1
                    "districtid" => array(7,13,15,16,8,17,11,18,6,23,24,31,20,21,35,41,38,45,53,59,55,62,60),
                    "fee"     => array(14000,18000,24000,33000,47000,58000,69000),
                    "fee_after_limit" => 5000
                ),
                array( // vung 2
                    "districtid" => array(5,2),
                    "fee"     => array(15000,20000,29000,38000,56000,72000,86000),
                    "fee_after_limit" => 10000
                ),
                array( // vung 3
                    "districtid" => array(14,4,9,10,12,19,22,26,27,29,30,3,36,34,47,48.49,50,32,33,40,37,39,42,43,44,46,51,54,56,57,58,61,63,64),
                    "fee"     => array(15000,20000,29000,38000,56000,72000,86000),
                    "fee_after_limit" => 12000
                )
            )
        ); // neu la khu vuc ngoai thanh internal == 0, thi tinh gia theo fee_range & trong luong
        if ($internal){ // noi thanh
            for($i=1; $i<=3; $i++){
                $ship_rule = "ship_rule".$i;
                
                $fee = $this->getFeeByDistrictPrice($districtid, $price, $weight, $$ship_rule, $internal);

                if ($fee >= 0)
                    return $fee;
            }            
            return $this->getFeeByDistrictPrice($districtid, $price, $weight, $ship_rule5, $internal=2);
        }else{            
            return $this->getFeeByDistrictPrice($districtid, $price, $weight, $ship_rule5, $internal);
        }

    }

    /* check district id in arrdistrict
     * return fee if price >= price_range
     * esle fee = 0
     */
    private function getFeeByDistrictPrice($districtid=0, $price=0, $weight=0, $ship_rule, $internal = 1){
        if ($internal == 1){
            if (in_array($districtid, $ship_rule['districtid'])){

                    if ($price <= $ship_rule['range'] )
                       return $ship_rule['fee'];
                    else
                       return 0;
            }
            return -1;
        }else{ //khu vuc ngoai thanh va cac tinh lan can
            //check khu vuc cac quan huyen lan can: Thủ Đức, Bình Tân, Hóc Môn, Bình Chánh, Củ chi, Cần Giờ, Nhà Bè
            if ($internal == 2){
                if ($price > 1000000)
                    return 0; // tong don hang < 1tr, free
            }
            
            $cur =0;
            foreach($ship_rule['fee_range'] as $fee){
                if (in_array($districtid, $fee['districtid'])){ // districtid in list                    
                    for($i=0; $i<count($ship_rule['weight_list']);$i++){ // loop each weight list
                        
                        if ($weight <= $ship_rule['weight_list'][$i]){// trong luong nam trong gioi han
                            return $fee['fee'][$i];
                        }
                        
                    }
                    // trong luong  vuot gioi han cho phep
                    $_fee = $fee['fee'][count($fee['fee'])-1];
                    
                    $_weight_remain = $weight - $ship_rule['weight_list'][count($ship_rule['weight_list'])-1];
                    
                    $_fee_remain =  round($_weight_remain / $ship_rule['weight_after_limit'], 0, PHP_ROUND_HALF_ODD) * $fee['fee_after_limit'];
                    
                    if ($_weight_remain % $ship_rule['weight_after_limit'] > 0){
                        $_fee_remain += $fee['fee_after_limit'];                        
                    }

                    return (int)$_fee + $_fee_remain;
                }
            }
        }


    }

    private function getInternalList(){
        return array(1,3,4,2,5,6,7,8,10,11,16,18,19,9,12,17,680,15,685,681,679,687,684,683);	
    }
    
 }
?>
