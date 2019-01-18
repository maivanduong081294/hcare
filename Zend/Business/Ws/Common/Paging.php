<?

class Business_Ws_Common_Paging {

    static $_cookie_name = 'rpp';
    static $_instance = null;
//                static $_pre_button = '<img src="/images/data/icon-prev.gif" align="absmiddle" />';
//                static $_next_button = '<img src="/images/data/icon-next.gif" align="absmiddle"/>';
    static $_pre_button = '<img src="/backend/images/icon_paging_left.gif" align="absmiddle" />';
    static $_next_button = '<img src="/backend/images/icon_paging_right.gif" align="absmiddle"/>';
    static $_first_button = '<img src="/images/data/icon-first.gif" align="absmiddle"/>';
    static $_last_button = '<img src="/images/data/icon-last.gif" align="absmiddle"/>';
    
    static $_pre_button2 = '<img src="/lb/images/layout/btn_pre.png" align="absmiddle" />';
    static $_next_button2 = '<img src="/lb/images/layout/btn_next.png" align="absmiddle"/>';
    static $_first_button2 = '<img src="/images/data/icon-first.gif" align="absmiddle"/>';
    static $_last_button2 = '<img src="/images/data/icon-last.gif" align="absmiddle"/>';

    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new Business_Ws_Common_Paging();
        }
        return self::$_instance;
    }

    public static function setRPP($amount) {
        $_COOKIE[self::$_cookie_name] = $amount;
    }

    //vao file  javascript setcokie cua language viet them ham changeRecordPerPage(tham so truyen vao)
    //{
    //copy doan setcokkie cua ngon ngu xuat va dua tham so truyen vao la ok
    //} cho selectbox
    public static function getRPP($amount = 20) {
        //read from cookie
        if (isset($_COOKIE[self::$_cookie_name])) {
            return $_COOKIE[self::$_cookie_name];
        }
        else
            return $amount;
    }

    public static function getOffset($page = 1) {
        if ($page == 0)
            $page = 1;
        $rpp = self::getRPP();
        return ($page - 1) * $rpp;
    }

    public static function doPaging($curpage, $total_records, $url, $pagerange = 5, $template = "tg") {

        if ($total_records == 0)
            $total_records = 1;

        $page_range = 5; // so luong page hien thi giua < 1 2 3 4 5 > or < 2 3 4 5 6 >
        $rpp = self::getRPP();
        $page = array();
        // set start page

        if ($curpage == 0)
            $curpage == 1;
        $total_page = (int) ((int) $total_records / $rpp);
        $total_page == 0 ? $total_page = 1 : $total_page;
        if ($template != 'tg') {
            if ($total_records % $rpp > 0)
                $total_page++;
            // get total page list page
            $run = 0;
            while ($total_records >= 0) {
                $page[] = $run++;
                $total_records -= $rpp;
            }
        } else { //for tg
            if ($total_records % $rpp > 0 && $total_records > $rpp)
                $total_page++;
            for ($i = 0; $i < $total_page; $i++) {
                $page[] = $i + 1;
            }
        }

        $page = self::getPageRange($page, $curpage, $pagerange, $total_page, $url, $template);
        $button = self::getBackNext($curpage, $total_page, $url, $template);

        $view = new Zend_View();
        $view->curpage = $curpage;
        $view->total_page = $total_page;
        $view->page_link = $page;
        $view->button = $button;
        $view->list = self::getList();
        if ($template == "hnam") {
            $view->setBasePath(APPLICATION_PATH . "/modules/hnam/views", "phtml");
            $content = $view->render('paging/paging_template.phtml');
        } else {
            $view->setBasePath(APPLICATION_PATH . "/modules/website_admin/views", "phtml");
            if ($template == '')
                $content = $view->render('common/paging_template.phtml');
            elseif ($template == 'bella') {
                $view->setBasePath(APPLICATION_PATH . "/modules/bella/views", "phtml");
                $content = $view->render('Common/bella_paging_template.phtml');
            } elseif ($template == 'tg') {
                $content = $view->render('common/paging_template.phtml');
            } else {
                $content = $view->render('common/admin_paging_template.phtml');
            }
        }
        return $content;
    }

    public static function doPagingComment($curpage, $total_records, $url, $pagerange = 5, $template = "tg") {

        if ($total_records == 0)
            $total_records = 1;

        $page_range = 5; // so luong page hien thi giua < 1 2 3 4 5 > or < 2 3 4 5 6 >
        $rpp = self::getRPP();
        $page = array();
        // set start page

        if ($curpage == 0)
            $curpage == 1;
        $total_page = (int) ((int) $total_records / $rpp);
        $total_page == 0 ? $total_page = 1 : $total_page;
            
        if ($template != 'tg') {
            if ($total_records % $rpp > 0 && $total_page > $rpp)
                $total_page++;
            // get total page list page
            $run = 0;
            while ($total_records >= 0) {
                $page[] = $run++;
                $total_records -= $rpp;
            }
        }

        $page = self::getPageRange($page, $curpage, $pagerange, $total_page, $url, $template);
        $button = self::getBackNext($curpage, $total_page, $url, $template);

        $view = new Zend_View();
        $view->url = $url;
        $view->curpage = $curpage;
        $view->total_page = $total_page;
        $view->page_link = $page;
        $view->button = $button;
        $view->list = self::getList();
        if ($template == "hnam") {
            $view->setBasePath(APPLICATION_PATH . "/modules/hnam/views", "phtml");
            $content = $view->render('paging/paging_template_comment.phtml');
        } else {
            $view->setBasePath(APPLICATION_PATH . "/modules/website_admin/views", "phtml");
            if ($template == '')
                $content = $view->render('common/paging_template.phtml');
            elseif ($template == 'bella') {
                $view->setBasePath(APPLICATION_PATH . "/modules/bella/views", "phtml");
                $content = $view->render('Common/bella_paging_template.phtml');
            } elseif ($template == 'tg') {
                $content = $view->render('common/paging_template.phtml');
            } else {
                $content = $view->render('common/admin_paging_comment_template.phtml');
            }
        }
        return $content;
    }

    private function getPageRange($page = array(), $curpage, $page_range, $total_page, $url, $template = '') {
        $_page = array();
        $_range = (int) ($page_range / 2) + $page_range % 2;

        if ($curpage <= $total_page) {
            if ($template != 'tg') {
                for ($i = $_range; $i > 0; $i--) {
                    if ($curpage - $i >= 0)
                        $_page[] = ($curpage - $i) + 1;
                }
                for ($i = 0; $i < $_range; $i++) {
                    if (($curpage + $i) < $total_page)
                        $_page[] = ($curpage + $i) + 1;
                }
                //get array of page $_page
                // return page link 
                foreach ($_page as $p) {
                    if ($p == $curpage)
//						$temp .= '&nbsp;'. $p . '&nbsp;';
                        $temp .= '&nbsp;<span>' . $p . '</span>&nbsp;';
                    else {
                        $_url = $url . "page=" . $p;
                        $temp .= '&nbsp;<a class="pagingItems" href="' . $_url . '">' . $p . "</a>&nbsp;";
                    }
                }
                return substr($temp, 0, strlen($temp) - 6);
            } else {//tg
                if ($total_page < 7) {

                    foreach ($page as $p) {
                        if ($p == $curpage)
                        //						$temp .= '&nbsp;'. $p . '&nbsp;';
                            $temp .= '&nbsp;<span>' . $p . '</span>&nbsp;';
                        else {
                            $_url = $url . "page=" . $p;
                            $temp .= '&nbsp;<a class="pagingItems" href="' . $_url . '">' . $p . "</a>&nbsp;";
                        }
                    }
                    return substr($temp, 0, strlen($temp) - 6);
                } else {
                    if ($curpage <= $total_page - 5) {

                        for ($i = $curpage - 1; $i <= $curpage + 3; $i++) {
                            if ($i == 0)
                                continue;
                            if ($i == $curpage)
                                $temp .= '&nbsp;<span>' . $i . '</span>&nbsp;';
                            else {
                                $_url = $url . "page=" . $i;
                                $temp .= '&nbsp;<a class="pagingItems" href="' . $_url . '">' . $i . "</a>&nbsp;";
                            }
                        }
                        //if ($curpage+3<$total_page-2) $temp .= ' ... ';
                        for ($i = $total_page - 2; $i <= $total_page; $i++) {
                            if ($i == $curpage)
                            //						$temp .= '&nbsp;'. $p . '&nbsp;';
                                $temp .= '&nbsp;<span>' . $i . '</span>&nbsp;';
                            else {
                                $_url = $url . "page=" . $i;
                                $temp .= '&nbsp;<a class="pagingItems" href="' . $_url . '">' . $i . "</a>&nbsp;";
                            }
                        }
                        return substr($temp, 0, strlen($temp) - 6);
                    } else {//curpage > total_page - 5
                        for ($i = $total_page - 5; $i <= $total_page; $i++) {
                            if ($i == $curpage)
                            //						$temp .= '&nbsp;'. $p . '&nbsp;';
                                $temp .= '&nbsp;<span>' . $i . '</span>&nbsp;';
                            else {
                                $_url = $url . "page=" . $i;
                                $temp .= '&nbsp;<a class="pagingItems" href="' . $_url . '">' . $i . "</a>&nbsp;";
                            }
                        }
                        return substr($temp, 0, strlen($temp) - 6);
                    }
                }
            }//end tg
        }
        else
            return "";
    }

    private function getBackNext($curpage, $total_page, $url, $template) {

        $arr = array();
        if ($curpage - 1 > 0) {
            $_url = $url . "page=" . ($curpage - 1);
            if ($template != 'bella')
                $arr[] = '<a href="' . $_url . '">' . self::$_pre_button . "</a>&nbsp;";
            else
                $arr[] = '<a href="' . $_url . '">' . "<img src='/images/bella/icon_btn_pre.png' align='absmiddle' border='0' />" . "</a>&nbsp;";
        }
        elseif ($curpage - 1 == 0) {
            if ($template != 'bella')
                $arr[] = self::$_pre_button;
            else
                $arr[] = "<img src='/images/bella/icon_btn_pre.png' align='absmiddle' border='0' />";
        }
        if ($curpage + 1 <= $total_page) {
            $_url = $url . "page=" . ($curpage + 1);
            if ($template != 'bella')
                $arr[] = '<a href="' . $_url . '">' . self::$_next_button . "</a>&nbsp;";
            else
                $arr[] = '<a href="' . $_url . '">' . "<img src='/images/bella/icon_btn_next.png' align='absmiddle' border='0' />" . "</a>&nbsp;";
        }
        else {
            if ($template != 'bella') {
                $arr[] = self::$_next_button;
            }
            else
                $arr[] = "<img src='/images/bella/icon_btn_next.png' align='absmiddle' border='0' />";
        }

        // get first and last page
//                        $arr[] = "<a href='" .$url . "page=1" . "'>" . self::$_first_button . "</a>";
//                        $arr[] = "<a href='" .$url . "page=$total_page" . "'>" . self::$_last_button . "</a>";

        return $arr;
    }

    private static function getList($arr = array('20', '50', '100', '200', '500', '1000')) {
        $temp = "";
        $rpp = self::getRPP();
        for ($i = 0; $i < count($arr); $i++) {
            if ($rpp == $arr[$i])
                $temp .= '<option value="' . $arr[$i] . '" SELECTED>' . $arr[$i] . '</option>' . '\n';
            else
                $temp .= '<option value="' . $arr[$i] . '">' . $arr[$i] . '</option>' . '\n';
        }
        return $temp;
    }

}

?>