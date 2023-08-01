<?php

/* * *************************************************************************
 *
 *   PROJECT: school managment system
 *   powerd by ashraf hamdy
 *   Copyright 2018 IT Plus Inc
 *   http://it-plus.co/
 *
 *
 * ************************************************************************* */

class iplus {

    public  $mDbhost;
    public  $mDbuser;
    public  $mDbpwd;
    public  $mDbname;
    public  static $handel;

    function iplus() {
        global $Config;


        $this->mDbhost = $Config['dbhost'];
        $this->mDbuser = $Config['dbuser'];
        $this->mDbpwd = $Config['dbpwd'];
        $this->mDbname = $Config['dbname'];
        $this->connect();

        $this->mMailer = new Mailer();
        $this->mMailer->mConfig = $Config;

        $this->mConfig = $Config;
    }

    /**
     * Connects to database
     */
    public  function connect() {

        if(self::$handel === null){
            try{
                self::$handel = new PDO('mysql:host=' . $this->mDbhost . ';dbname=' . $this->mDbname, $this->mDbuser, $this->mDbpwd);
                self::$handel ->query("set names 'utf8';");
            }catch (Exception $e) {
                die($e->getMessage());
            }
        }
        return self::$handel;
    }



    /**
     * Close connection to database
     *
     * @param $aConnection connection
     *
     * return bool
     */
    function close() {
//        unset(self::$handel);
       return self::$handel;
    }

    /**
     * @param $aSql
     * @return bool
     */
    function query($aSql) {
        $query = $this->connect()->prepare($aSql);
        return $query->execute();
    }
    
    /*function queryapi($aSql) {
        $query = $this->connect()->prepare($aSql);
        $query->execute();
        return $query->rowCount() ? true : false;
    }*/
    
    function queryreturnlastid($aSql) {
        $query = $this->connect()->prepare($aSql);
        $query->execute();
        return $this->connect()->lastInsertId();
    }

    /**
     * @param $aSql
     * @return array
     */
    /*function getRow($aSql) {
        $query = $this->connect()->prepare($aSql);
        $query->execute();
        return $query->fetch();
    }*/
    
    function getRow($aSql) {
        $query = $this->connect()->prepare($aSql);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @param $aSql
     * @return array
     */
    /*function getAll($aSql) {

        $query = $this->connect()->prepare($aSql);
        $query->execute();


        $out = Array();

        while ($temp = $query->fetch()) {
            $out[] = $temp;
        }

        return $out;
    }*/
    
    function getAll($aSql) {

        $query = $this->connect()->prepare($aSql);
        $query->execute();


        $out = Array();

        while ($temp = $query->fetch(PDO::FETCH_ASSOC)) {
            $out[] = $temp;
        }

        return $out;
    }

    /**
     * @param $aSql
     * @return array
     */
    function &getAssoc($aSql) {
        $out = Array();
        $res = & $this->connect()->prepare($aSql);
        $res->execute();

        while ($temp = $res->fetch(PDO::FETCH_ASSOC)) {
            $key = array_shift($temp);
            $out[$key][] = $temp;
        }
        return $out;
    }

    /**
     * @param $aSql
     * @return string
     */
    function getOne($aSql) {

        $query = $this->connect()->prepare($aSql);
        $query->execute();
        return $result = $query->fetchColumn(0);
    }

    /**
     * Returns array of tables
     *
     * @return arr
     */
    function getTables() {
        $out = Array();

        $sql = "SHOW TABLES FROM {$this->mDbname}";
        $res = $this->connect()->prepare($sql);
        $res->execute();
        while ($row = $res->fetch()) {
            $out[] = $row[0];
        }

        return $out;
    }

    /**
     * Prints out block with error
     */
    function printError($aError) {
        echo $aError;
    }

    /**
     * Returns recordset as associative array where the key is the first field
     *
     * @param str $aSql sql query
     *
     * @return arr
     */
    function getKeyValue($aSql) {
        $out = Array();
        $res = $this->connect()->prepare($aSql);
        $res->execute();
        while ($temp = $res->fetch()) {
            $out[$temp[0]] = $temp[1];
        }
        return $out;
    }

    public function getLastInsertId() {
        return $this->connect()->lastInsertId();
    }
    
      public function getDateNow() {
        return date("Y-m-d H:i:s");
    }

}




