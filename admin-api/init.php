<?php
error_reporting( 0 );
/* * *************************************************************************
 *
 *   PROJECT: BigWish App
 *   powerd by IT PLUS Team
 *   Copyright 2020 IT Plus Inc
 *   http://it-plus.co/
 *
 * ************************************************************************* */
//error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING &~E_DEPRECATED );

// header('Access-Control-Allow-Origin: *');

if( !function_exists('apache_request_headers') ) {
    ///
    function apache_request_headers() {
      $arh = array();
      $rx_http = '/\AHTTP_/';
      foreach($_SERVER as $key => $val) {
        if( preg_match($rx_http, $key) ) {
          $arh_key = preg_replace($rx_http, '', $key);
          $rx_matches = array();
          // do some nasty string manipulations to restore the original letter case
          // this should work in most cases
          $rx_matches = explode('_', $arh_key);
          if( count($rx_matches) > 0 and strlen($arh_key) > 2 ) {
            foreach($rx_matches as $ak_key => $ak_val) $rx_matches[$ak_key] = ucfirst($ak_val);
            $arh_key = implode('-', $rx_matches);
          }
          $arh[$arh_key] = $val;
        }
      }
      return( $arh );
    }
    ///
    }

date_default_timezone_set('Asia/Qatar');

require_once('../vendor/autoload.php');

require_once('../includes/config.inc.php');



//
require_once('../utils/util.php');

require_once('classes/plusMailer.php');

require_once('classes/iplus.php');

require_once('classes/upload-class.php');

// Main Components

require_once('classes/class.api-push-notifications.php');
require_once('classes/class.settings.php');
require_once('classes/class.hs256.php');
require_once('classes/class.users.php');
require_once('classes/class.levels.php');
require_once('classes/class.pages.php');
// Sections

require_once('classes/class.languages.php');
require_once('classes/class.frequently_questions.php');
require_once('classes/class.correspondencemails.php');
require_once('classes/class.categories.php');
require_once('classes/class.services.php');
require_once('classes/class.chances.php');
require_once('classes/class.countries.php');
require_once('classes/class.partners.php');

require_once('classes/class.rentes.php');
require_once('classes/class.stages.php');
require_once('classes/class.subjects.php');
require_once('classes/class.tests.php');
require_once('classes/class.stations.php');
require_once('classes/class.shops.php');
require_once('classes/class.grades.php');
require_once('classes/class.units.php');
require_once('classes/class.training_courses.php');
require_once('classes/class.news.php');
require_once('classes/class.orders.php');
require_once('classes/class.projects.php');
require_once('classes/class.sliders.php');
require_once('classes/class.notifications.php');
require_once('classes/class.statistics.php');
require_once('classes/class.home_section.php');


//error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING &~E_DEPRECATED );
