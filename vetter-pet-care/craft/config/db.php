<?php

/**
 * Database Configuration
 *
 * All of your system's database configuration settings go in here.
 * You can see a list of the default settings in craft/app/etc/config/defaults/db.php
 */

return array(
    // if not all above go this default
    '*' => array(
        'tablePrefix' => 'craft',


//     'tablePrefix' => 'craft',

        // The database server name or IP address. Usually this is 'localhost' or '127.0.0.1'.
//	'server' => 'localhost',

        // The name of the database to select.
//	'database' => 'vetter',

        // The database username to connect with.
//	'user' => 'root',

        // The database password to connect with.
//	'password' => 'StetSon2016!',

        // The prefix to use when naming tables. This can be no more than 5 characters.

    ),

//Local site
 'localhost' => array(

	'password' => 'mysql',
    'tablePrefix' => 'craft',
    'database' => 'vetter',
    'user' => 'root',
    'server' => 'localhost',


	),
    //akshay local
    'vetter' => array(

        'password' => 'mysql',
        'tablePrefix' => 'craft',
        'database' => 'vetter',
        'user' => 'root',
        'server' => 'localhost',
	    'password' => 'mysql',


	),
// Prash local env
	'vetter.test' => array(
        'password' => 'tactil3215',
        'tablePrefix' => 'craft',
        'database' => 'vetter',
        'user' => 'root',
        'server' => 'vetter.test',
    ),
//Matt Local env
  'vetter.local.dev' => array(
      'password' => 'mysql',
      'tablePrefix' => 'craft',
      'database' => 'vetter',
      'user' => 'root',
      'server' => 'localhost',
        'password' => 'mysql',
      ),
   'vetter.home.dev' => array(
       'password' => 'mysql',
       'tablePrefix' => 'craft',
       'database' => 'vetter',
       'user' => 'root',
       'server' => 'localhost',
            'password' => '',
          ),

	// achan local env
//achan local lap site
    'vetter.lap.dev' => array(
        'tablePrefix' => 'craft',
        'database' => 'vetter',
        'user' => 'root',
        'server' => 'localhost',
        'password' => 'tactil3215',
    ),

// stephen local
    'vetter.stephen.test' => array(
        'database'      => 'vetter',
        'password'      => 'mysql',
        'server'        => 'localhost',
        'tablePrefix'   => 'craft',
        'user'          => 'root',
//        'tablePrefix' => 'craft',
//        'database' => 'vetter',
//        'user' => 'root',
//        'server' => 'vetter-dev.cix08mfuleus.us-east-1.rds.amazonaws.com',
//        'password' => 'tactil3215',
    ),
  'http://162.243.217.72' => array (
      'password' => 'mysql',
      'tablePrefix' => 'craft',
      'database' => 'vetter',
      'user' => 'root',
      'server' => 'localhost',
	'password' => 'StetSon2016!',
	),

  'vpc.thetactilegroup.com'   => array (
      'password' => 'mysql',
      'tablePrefix' => 'craft',
      'database' => 'vetter',
      'user' => 'root',
      'server' => 'localhost',
      'password' => 'StetSon2016!',
  ),

    '.vetterpetcare.com'   => array (
        'tablePrefix' => 'craft',
        'server' => 'vetter.cix08mfuleus.us-east-1.rds.amazonaws.com',
        'database' => 'vetter',
        'user' => 'vetterdb',
        'password' => '2017wKRNGjaY',
    ),

    // for all other domains
    '.thevetter.com'   => array (
        'tablePrefix' => 'craft',
        'server' => 'vetter.cix08mfuleus.us-east-1.rds.amazonaws.com',
        'database' => 'vetter',
        'user' => 'vetterdb',
        'password' => '2017wKRNGjaY',
    ),

    '.dogvetter.com'   => array (
        'tablePrefix' => 'craft',
        'server' => 'vetter.cix08mfuleus.us-east-1.rds.amazonaws.com',
        'database' => 'vetter',
        'user' => 'vetterdb',
        'password' => '2017wKRNGjaY',
    ),

    '.catvetter.com'   => array (
        'tablePrefix' => 'craft',
        'server' => 'vetter.cix08mfuleus.us-east-1.rds.amazonaws.com',
        'database' => 'vetter',
        'user' => 'vetterdb',
        'password' => '2017wKRNGjaY',
    ),
  '138.197.71.127' => array (
      'tablePrefix' => 'craft',
      'database' => 'vetter',
      'user' => 'root',
      'server' => 'localhost',
	'password' => 'StetSon2016!',
	)


);
