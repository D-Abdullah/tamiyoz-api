<?php

$pagesObj2 = new pages();

class pages {

    var $mDb;
    var $mConfig;
    var $lang;

    function pages() {
        global $Config;
        $this->mDb = new iplus();
        $this->mConfig = $Config;
        $this->lang = LANGUAGE;
    }

    function getOnePage($data) {
        $sql = "SELECT p.id, pl.`title`, pl.`description`,pl.`sub_title`  FROM `{$this->mPrefix}pages` p ";
        $sql .= " LEFT JOIN `{$this->mPrefix}pages_langs` pl ON (p.id = pl.page_id) ";
        $sql .= " WHERE p.`id` = '{$data['id']}' AND   p.`status` = '1'  AND   pl.`lang_code` = '{$data['lang_code']}'   ";
//       echo "$sql" . "<hr>";
        return $this->mDb->getRow($sql);
    }

}

?>