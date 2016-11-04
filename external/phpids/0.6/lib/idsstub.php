<?php
set_include_path(
   get_include_path()
   . PATH_SEPARATOR
   . '/var/www/html/app/external/phpids/0.6/lib'
  );

  require_once 'IDS/Init.php';
  $request = array(
      'REQUEST' => $_REQUEST,
      'GET' => $_GET,
      'POST' => $_POST,
      'COOKIE' => $_COOKIE
  );
  $init = IDS_Init::init('/var/www/html/app/external/phpids/0.6/lib/IDS/Config/Config.ini');
  $ids = new IDS_Monitor($request, $init);
  $result = $ids->run();

  if (!$result->isEmpty()) {
   // Take a look at the result object
   echo $result;
   require_once 'IDS/Log/File.php';
   require_once 'IDS/Log/Composite.php';

   $compositeLog = new IDS_Log_Composite();
   $compositeLog->addLogger(IDS_Log_File::getInstance($init));
   $compositeLog->execute($result);
  }
?>
