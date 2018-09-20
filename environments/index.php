<?php
return array (
  'Development' => 
  array (
    'path' => 'dev',
    'setWritable' => 
    array (
      6 => 'apps/api/web/assets'
    ),
    'setExecutable' => 
    array (
      0 => 'scripts/yii',
    ),
    'setCookieValidationKey' => 
    array (
      6 => 'apps/api/config/main-local.php'
    ),
  ),
  'Production' => 
  array (
    'path' => 'prod',
    'setWritable' => 
    array (
      6 => 'apps/api/web/assets',
    ),
    'setExecutable' => 
    array (
      0 => 'scripts/yii',
    ),
    'setCookieValidationKey' => 
    array (
      6 => 'apps/api/config/main-local.php'
    ),
  ),
);