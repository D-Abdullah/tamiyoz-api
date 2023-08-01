<?php

/* * *************************************************************************
 *
 *   PROJECT:  Aramedical system
 *   powerd by  IT PLUS
 *   Copyright 2013 IT Plus Inc
 *   http://it-plus.co/
 *
 * ************************************************************************* */
$settingsweb = new Configuration();

class Configuration {

    var $mDb;
    var $mMailer;
    var $prefix;
    var $mConfig;
    var $mDsp;
    var $config_file ="../includes/config.inc.php";

    function Configuration() {
        $this->mDb = new iplus();
        $this->mMailer = new Mailer();
        $this->mMailer->mConfig = $Config;
        $this->prefix = $Config['prefix'];
        $this->mConfig = $Config;
    }

    /**
     * Saves configuration to a file
     *
     * @param array $aConfigs modified configuration array
     *
     * @return bool
     */
    function saveConfig($aConfigs) {

        $configs = $this->mergeConfig($aConfigs);

        /** now build string that will be written to config file * */
        $out_config = "<?php\n";
        foreach ($configs as $key => $value) {


            $out_config .= '$Config[\'' . $key . "'] = '" . addslashes($value) . "';\n";
        }
        $out_config .= "?>";

        /** write configuration to a file * */
        $f = fopen($this->config_file, 'w');
        if (!$f)
            return false;
        if (0 != get_magic_quotes_gpc()) {
            $out_config = $out_config;
        }
        fwrite($f, $out_config);
        fclose($f);

        return true;
    }

    /**
     * Compares current config and changed one
     *
     * @param arr $aConfigs modified configuration array
     *
     * @return arr
     */
    function mergeConfig($aConfigs) {
        global $Config;

        $merged = array_merge($Config, $aConfigs);

        foreach ($merged as $key => $value) {
            $config[$key] = stripslashes($value);
        }

        return $config;
    }

    function getAutoLoadSettings() {
        
        $sql  = " SELECT name,value ";
        $sql .= " FROM `{$this->mPrefix}settings` ";
        $sql .= " WHERE `autoload`='1' ";

        $res  = $this->mDb->getAll($sql);
        foreach ($res as $k => $v){
            $temp[$v['name']] = $v['value'];
        }
        return $temp;
    }

    function getGeneralSettingsRow($row) {
        $sql = "SELECT $row ";
        $sql .= "FROM `{$this->mPrefix}settings` ";
        $sql .= "WHERE `id`='1' ";

        return $this->mDb->getOne($sql);
    }



}
?>