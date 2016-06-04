<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2016, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2016, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
 
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/libraries/config.html
 */
 /**
 * Application Controller Class
 *
 * This class object is the super class that every library in
 * CodeIgniter will be assigned to.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/general/controllers.html
 */
 /**
     *
     * ************** For Models  *****************
     *
     *
     *============ Codeigniter Core System ================
     * @property CI_Benchmark $benchmark              Benchmarks
     * @property CI_Config $config                    This class contains functions that enable config files 
     * @property CI_Controller $controller            This class object is the super class that every library in.
     * @property CI_Exceptions $exceptions            Exceptions Class
     * @property CI_Hooks $hooks                      Provides a mechanism to extend the base system 
     * @property CI_Input $input                      Pre-processes global input data for security
     * @property CI_Lang $lang                        Language Class
     * @property CI_Loader $load                      Loads views and files
     * @property CI_Log $log                          Logging Class
     * @property CI_Output $output                    Responsible for sending final output to browser
     * @property CI_Profiler $profiler                Display benchmark results, queries you have run, etc
     * @property CI_Router $router                    Parses URIs and determines routing
     * @property CI_URI $uri                          Retrieve information from URI strings
     * @property CI_Utf8 $utf8                        Provides support for UTF-8 environments
     *
     *
     * @property CI_Model $model                      Codeigniter Model Class
     *
     * @property CI_Driver $driver                    Codeigniter Drivers
     *
     *
     *============ Codeigniter Libraries ================
     *
     * @property CI_Cache $cache                      Caching
     * @property CI_Calendar $calendar                This class enables the creation of calendars
     * @property CI_Email $email                      Permits email to be sent using Mail, Sendmail, or SMTP.
     * @property CI_Encryption $encryption            The Encryption Library provides two-way data encryption.
     * @property CI_Upload $upload                    File Uploading class
     * @property CI_Form_validation $form_validation  Form Validation class
     * @property CI_Ftp $ftp                          FTP Class
     * @property CI_Image_lib $image_lib              Image Manipulation class
     * @property CI_Migration $migration              Tracks & saves updates to database structure
     * @property CI_Pagination $pagination            Pagination Class
     * @property CI_Parser $parser                    Template parser
     * @property CI_Security $security                Processing input data for security.
     * @property CI_Session $session                  Session Class
     * @property CI_Table $table                      HTML table generation
     * @property CI_Trackback $trackback              Trackback Sending/Receiving Class
     * @property CI_Typography $typography            Typography Class
     * @property CI_Unit_test $unit_test              Simple testing class
     * @property CI_User_agent $user_agent            Identifies the platform, browser, robot, or mobile
     * @property CI_Xmlrpc $xmlrpc                    XML-RPC request handler class
     * @property CI_Xmlrpcs $xmlrpcs                  XML-RPC server class
     * @property CI_Zip $zip                          Zip Compression Class
     *
     *
     *                          *============ Database Libraries ================
     *
     *
     * @property CI_DB_query_builder $db   Database
     * @property CI_DB_forge $dbforge     Database
     * @property CI_DB_result $result                 Database
     *
     *
     *
     *                            *============ Codeigniter Depracated  Libraries ================
     *
     * @property CI_Javascript $javascript            Javascript (not supported
     * @property CI_Jquery $jquery                    Jquery (not supported)
     * @property CI_Encrypt $encrypt                  Its included but move over to new Encryption Library
     *
     *
     *                            *============ Codeigniter Project Models ================
     *  Models that are in your project. if the model is in a folder, still just use the model name.
     *
     *  load the model with Capital letter $this->load->model('People') ;
     *  $this->People-> will show all the methods in the People model
     *
     * @property People $People
     *
     * @property Products $Products
     *
     */
class CI_Model {

	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		log_message('info', 'Model Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * __get magic
	 *
	 * Allows models to access CI's loaded classes using the same
	 * syntax as controllers.
	 *
	 * @param	string	$key
	 */
	public function __get($key)
	{
		// Debugging note:
		//	If you're here because you're getting an error message
		//	saying 'Undefined Property: system/core/Model.php', it's
		//	most likely a typo in your model code.
		return get_instance()->$key;
	}

}
