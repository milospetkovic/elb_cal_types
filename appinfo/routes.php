<?php
/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> OCA\ElbCalTypes\Controller\PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */
return [
    'resources' => [
        'elbcaltype' => ['url' => '/caltypes'],
    ],
    'routes' => [
        [ 'name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
        [ 'name' => 'user#isusersuperadmin', 'url' => '/isusersuperadmin', 'verb' => 'POST'],
        [ 'name' => 'elbcaldefreminder#getdefaultreminders', 'url' => '/getdefaultreminders', 'verb' => 'POST'],
        [ 'name' => 'elbcaltypereminder#getassignedreminders', 'url' => '/getassignedreminders', 'verb' => 'POST'],
        [ 'name' => 'elbcaltypereminder#assigndefreminderstocaltype', 'url' => '/assigndefreminderstocaltype', 'verb' => 'POST'],
        [ 'name' => 'elbcaltypereminder#removereminderforcaltype', 'url' => '/removereminderforcaltype', 'verb' => 'POST'],
        [ 'name' => 'elbgroupfolder#getallgroupfolders', 'url' => '/getallgroupfolders', 'verb' => 'GET'],
    ]
];