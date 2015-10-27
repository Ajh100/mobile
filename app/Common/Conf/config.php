<?php
return array(
    'MULTI_MODULE' => false,
    'DEFAULT_MODULE' => 'Home',
    
    /* 数据库设*/
    'DB_TYPE'   =>  'mysql',
    'DB_HOST'   =>  '104.131.144.192',
    'DB_NAME'   =>  'jh100',
    'DB_USER'   =>  'xxg3053',
    'DB_PWD'    =>  'xxg111063053',
    'DB_PREFIX' => 'tc_', 
    'DB_CHARSET'=> 'utf8',
    

    'DATA_AUTH_KEY'  => 'tke")*Bs6]2<Sg8Ca3Y>5dFq@,znLH4KI%?R=.~h',
    'DATA_CRYPT_TYPE' =>  'JH100',
    
    /* URL配置 */
    'URL_CASE_INSENSITIVE' => true,
    'URL_MODEL' => 2,
    
    //session cookie
    'SESSION_PREFIX' =>  'jh100_', 
    'COOKIE_PREFIX'  =>  'jh100_',
    
    
	
	//缓存设置
	'APP_S_CACHE_TIME'  => 60,
    
    'UPLOAD_SAVE_PATH' => '../res/',
    
    'IP_LOCATION_URL' =>  'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=',
    'WEB_BASE_URL'    =>  'http://m.jh100.com/',
    'WEB_RES_URL'     =>  'http://res.jh100.com/',
    'WEB_IMG_URL'     =>  'http://image.jh100.com/',
	
	'WEB_PC_URL' => 'http://www.jh100.com',
);