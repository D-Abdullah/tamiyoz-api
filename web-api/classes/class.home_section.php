<?php

$homeSectionObj = new homeSectionClass();

class homeSectionClass {

    var $mDb;
    var $mConfig;
    var $lang;

    function homeSectionClass() {
        global $Config;
        $this->mDb = new iplus();
        $this->mConfig = $Config;
        $this->lang = LANGUAGE;
    }

    function getAllHomeSectionItems($lang) {
        $lang = $lang ? $lang : "ar";
        $sql = "SELECT p.id, p.`ico`,p.`img`, pl.`title`, pl.`description`  FROM `home_section` p ";
        $sql .= " LEFT JOIN `home_section_langs` pl ON (p.id = pl.home_section_id) ";
        $sql .= " WHERE p.`status` = '1'  AND   pl.`lang_code` = '{$lang}' ";
        return $this->mDb->getAll($sql);
    }

}

?>
