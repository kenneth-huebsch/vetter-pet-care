<?php

/**
 * General Configuration
 *
 * All of your system's general configuration settings go in here.
 * You can see a list of the default settings in craft/app/etc/config/defaults/general.php
*/
return array(

//General Site config
  '*' => array(
  'devMode' => false,
  'useCompressedJs' => true,
  'cacheElementQueries' => false,
  'enableTemplateCaching' => true,
  'suppressTemplateErrors' => true,
  'omitScriptNameInUrls' => true,
  'useEmailAsUsername' => true,
  'autoLoginAfterAccountActivation' => true,
  'maxUploadFileSize' => 104857600,
  'loginPath' => 'signup?t=tab-1',
  'setPasswordPath' => 'set-password-form',
  'setPasswordSuccessPath' => 'signup?t=tab-1',
  'maxSlugIncrement' => 10000000,
),

//Local site
  'localhost' => array(
     //
     'environmentVariables' => array(
         'basePath' => 'localhost/vetter/',
         'baseUrl'  => 'http://localhost/vetter/public',
         'siteUrl' => 'http://localhost/vetter/public',
         'devMode' => false,
         'useCompressedJs' => false,
         'cacheElementQueries' => false,
         'enableTemplateCaching' => true,
         'suppressTemplateErrors' => false,
         'maxUploadFileSize' => 104857600,
         'env' => 'dev'

     )
 ),

 'vetter.home.dev' => array(
    //
    'useEmailAsUsername' => true,
    'environmentVariables' => array(
        'basePath' => 'localhost/vetter/',
        'baseUrl'  => 'http://vetter.home.dev',
        'siteUrl' => 'http://vetter.home.dev',
        'devMode' => false,
        'useCompressedJs' => false,
        'cacheElementQueries' => false,
        'enableTemplateCaching' => true,
        'suppressTemplateErrors' => false,
        'maxUploadFileSize' => 104857600,
        'env' => 'dev'
    )
),
'vetter.local.dev' => array(
   //
   'useEmailAsUsername' => true,
   'environmentVariables' => array(
       'basePath' => 'localhost/vetter/',
       'baseUrl'  => 'http://vetter.local.dev',
       'siteUrl' => 'http://vetter.local.dev',
       'devMode' => false,
       'useCompressedJs' => false,
       'cacheElementQueries' => false,
       'enableTemplateCaching' => true,
       'suppressTemplateErrors' => false,
       'maxUploadFileSize' => 104857600,
       'env' => 'dev'
   )
),



//akshay local
  'vetter' => array(
    'useEmailAsUsername' => true,
     'environmentVariables' => array(
         'basePath' => 'C:/Users/QATactile/Desktop/vetter-pet-care/',
         'baseUrl'  => 'https://vetter',
         'siteUrl' => 'https://vetter',
         'devMode' => true,
         'useCompressedJs' => false,
         'cacheElementQueries' => false,
         'enableTemplateCaching' => true,
         'suppressTemplateErrors' => false,
         'maxUploadFileSize' => 104857600,
         'env' => 'dev'
     )
 ),

  //achan local laptop site
  'vetter.lap.dev' => array(
      'siteUrl' => 'https://vetter.lap.dev',
      'baseUrl' => 'https://vetter.lap.dev',
	  'devMode' => false,
      'useEmailAsUsername' => true,
      'autoLoginAfterAccountActivation' => true,
      'overridePhpSessionLocation' => true,
      'verificationCodeDuration' => 'P7D',
     //
     'environmentVariables' => array(
         'basePath' => 'Users/angelachan/git/vetter-pet-care/',
         'baseUrl'  => 'https://vetter.lap.dev',
         'useCompressedJs' => false,
         'cacheElementQueries' => false,
         'enableTemplateCaching' => true,
         'suppressTemplateErrors' => false,
         'maxUploadFileSize' => 104857600,
         'overridePhpSessionLocation' => true,
         'env' => 'dev'
     ),

 ),

 //prash local
'vetter.test' => array(
    'useEm width: 150px;ailAsUsername' => true,
     'environmentVariables' => array(
         'basePath' => 'localhost/vetter/',
         'baseUrl'  => 'http://vetter.test',
         'siteUrl' => 'http://vetter.test',
         'devMode' => true,
         'useCompressedJs' => false,
         'cacheElementQueries' => false,
         'enableTemplateCaching' => true,
         'suppressTemplateErrors' => false,
         'maxUploadFileSize' => 104857600,
         'env' => 'dev'
     )
 ),

// stephen local
    'vetter.stephen.test'     => array(
        'autoLoginAfterAccountActivation'   => true,
        'baseUrl'                           => 'http://vetter.stephen.test/',
        'devMode'                           => true,
        'overridephpSessionLocation'        => true,
        'siteUrl'                           => 'http://vetter.stephen.test/',
        'useEmailAsUsername'                => true,
        'verificatioCodeDuration'           => 'P7D',
        'environmentVariables'              => array(
            'basePath'                  => 'C:/MAMP/htdocs/vetter-pet-care/public/',
            'baseUrl'                   => 'http://vetter.stephen.test/',
            'siteUrl'                   => 'http://vetter.stephen.test/',
            'useCompressedJs'           => false,
            'cacheElementQueries'       => false,
            'enableTemplateCaching'     => false,
            'suppressTemplateErrors'    => false,
            'maxUploadFileSize'         => 104857600,
            'overridephpSessionLocation'=> true,
            'env'                       => 'dev'
        ),
    ),
//staging site
    '162.243.217.72' => array(
        // ...
        'environmentVariables' => array(
            'basePath' => 'var/www/html/',
            'baseUrl'  => 'http://162.243.217.72',
            'siteUrl' => 'http://162.243.217.72',
            'devMode' => false,
            'useCompressedJs' => true,
            'cacheElementQueries' => false,
            'enableTemplateCaching' => true,
            'suppressTemplateErrors' => true,
            'maxUploadFileSize' => 104857600,
            'overridePhpSessionLocation' => true,
            'env' => 'dev'
        )
    ),

    'vpc.thetactilegroup.com' => array(
        // ...
        'environmentVariables' => array(
            'basePath' => 'var/www/html/',
            'baseUrl'  => 'http://vpc.thetactilegroup.com',
            'siteUrl' => 'http://vpc.thetactilegroup.com',
            'devMode' => false,
            'useCompressedJs' => true,
            'cacheElementQueries' => false,
            'enableTemplateCaching' => true,
            'suppressTemplateErrors' => true,
            'maxUploadFileSize' => 104857600,
            'overridePhpSessionLocation' => true,
            'env' => 'dev'
        )
    ),

    //production site
    '.vetterpetcare.com' => array(
	    'maxSlugIncrement' => 10000000,
        'siteUrl' => 'https://www.vetterpetcare.com',
        'basePath' => '/srv/www/vetter_prod/current/',
        'baseUrl'  => 'https://www.vetterpetcare.com',
        'overridePhpSessionLocation' => true,
        'verificationCodeDuration' => 'P7D',
        //'cacheMethod' => 'memcache',
        //'cacheDuration' => 'P1D',
        //'generateTransformsBeforePageLoad' => true,
       // 'enableTemplateCaching' => true,
        //'cacheElementQueries' => true,
      //  'maxCachedCloudImageSize' => 2880,
        // ...
        'environmentVariables' => array(
            'basePath' => '/srv/www/vetter_prod/current/',
            'baseUrl'  => 'https://www.vetterpetcare.com',
            'siteUrl' => 'https://www.vetterpetcare.com',
            'devMode' => false,
            'useCompressedJs' => true,
            'cacheElementQueries' => false,
            'enableTemplateCaching' => true,
            'suppressTemplateErrors' => true,
            'omitScriptNameInUrls' => true,
            'maxUploadFileSize' => 104857600,
            'overridePhpSessionLocation' => true,
            'verificationCodeDuration' => 'P7D',
            'env' => 'prod'
        )
    ),
    '138.197.71.127' => array(
        'environmentVariables' => array(
            'basePath' => '/var/www/html/',
            'baseUrl'  => 'http://138.197.71.127',
            'siteUrl' => 'http://138.197.71.127',
            'devMode' => true,
            'useCompressedJs' => true,
            'cacheElementQueries' => false,
            'enableTemplateCaching' => false,
            'suppressTemplateErrors' => false,
            'maxUploadFileSize' => 104857600,
            'overridePhpSessionLocation' => true,
            'env' => 'dev'
        )
   )


);
