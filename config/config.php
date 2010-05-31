<?php

Configure::write('Project',array('name'=>'Spark Plug Cakephp Plugin'));


Configure::write('UserPermissions',array(
				'controllers/Posts/index',
				'controllers/Posts/edit',
				'controllers/Websites'
));

Configure::write('rootURL','127.0.0.1/spark');
Configure::write('httpRootURL','http://127.0.0.1/spark');
Configure::write('projectName','Spark Plug Cakephp Plugin');
Configure::write('logged-in-menu','logged_in_menu');
Configure::write('front_end_layout','default');
Configure::write('dashboard_layout','default');

Configure::write('SparkPlug.open_registration', true);
Configure::write('SparkPlug.default_groupid_for_registration', 2); // id of the group used to register new users via users/register
Configure::write('SparkPlug.register_defaults.optin', false);
Configure::write('SparkPlug.register_defaults.agreement', false);

Configure::write('SparkPlug.OriginAfterLogin', true); // used to redirect after login to the page that triggered the login action
Configure::write('SparkPlug.loginRedirect', '/users/dashboard');
Configure::write('SparkPlug.registerRedirect', false);
Configure::write('SparkPlug.registerAutoLogin', false);
Configure::write('SparkPlug.administrator.from_name', 'Administrator');
Configure::write('SparkPlug.administrator.email', 'noreply@tdservers.com');
Configure::write('SparkPlug.hash.method', 'md5');	// use sha1 to be compatible with passwords generated by Cake's Auth Component
Configure::write('SparkPlug.hash.salt', false);		// use true to be compatible with passwords generated by Cake's Auth Component
Configure::write('SparkPlug.allow.login_as_user', false);

// creating a specific cache config for rules
Cache::config('SparkPlug', array(  
    'engine' => 'File',  
    'duration'=> '+3 months',  
    'path' => CACHE,  
    'prefix' => 'SparkPlug_'
));



function SparkPlugIt(&$controller)
{
    $pageRedirect = $controller->Session->read('permission_error_redirect');
    $controller->Session->delete('permission_error_redirect');

    $controller->company_id = $controller->Session->read('Company.id');

    if (empty($pageRedirect))
    {
        $actionUrl = $controller->params['url']['url'];
        $controller->Session->write('SparkPlug.OriginAfterLogin', '/'.$actionUrl);

//        if (isset($controller->params['slug']))
//            $website = $controller->Website->find('Website.subdomain = "'.$controller->params['slug'].'"');
//        else
//            $website = null;
//
//        if (!$website)
//        {
            $user = $controller->Authsome->get();
            if (!$user)
            {
                //anonymous?
                if (!$controller->UserGroup->isGuestAccess($actionUrl))
                {
                    $controller->Session->write('permission_error_redirect','/users/login');
                    $controller->Session->setFlash('Please login to view this page.');

                    $controller->redirect('/users/login');
                }
            }
            else
            {
                if (!$controller->UserGroup->isUserGroupAccess($user['User']['user_group_id'],$actionUrl))
                {
                    $controller->Session->write('permission_error_redirect','/users/login');
                    $controller->Session->setFlash('Sorry, You don\'t have permission to view this page. '.$user['User']['user_group_id'].':('.$actionUrl.')');

                    //$controller->redirect(Configure::read('SparkPlug.loginRedirect'));
                    $controller->redirect('/errors/unauthorized');
                }
            }
//        }
    }
}
?>