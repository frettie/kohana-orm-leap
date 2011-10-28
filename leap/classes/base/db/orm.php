<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Copyright 2011 Spadefoot
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * This class provides a shortcut way to get the various database builder class.
 *
 * @package Leap
 * @category ORM
 * @version 2011-06-10
 *
 * @abstract
 */
abstract class Base_DB_ORM extends Kohana_Object {

    /**
    * This function returns an instance of the DB_ORM_Delete_Proxy.
    *
    * @access public
    * @static
    * @param string $model                  the model's name
    * @return DB_ORM_Delete_Proxy           an instance of the class
    */
	public static function delete($model) {
	    $statement = new DB_ORM_Delete_Proxy($model);
	    return $statement;
	}

    /**
    * This function returns an instance of the DB_ORM_Insert_Proxy.
    *
    * @access public
    * @static
    * @param string $model                  the model's name
    * @return DB_ORM_Insert_Proxy           an instance of the class
    */
	public static function insert($model) {
		$statement = new DB_ORM_Insert_Proxy($model);
	    return $statement;
	}

    /**
    * This function returns an instance of the DB_ORM_Select_Proxy.
    *
    * @access public
    * @static
    * @param string $model                  the model's name
    * @param array $columns                 the columns to be selected
    * @return DB_ORM_Select_Proxy           an instance of the class
    */
	public static function select($model, Array $columns = array()) {
		$statement = new DB_ORM_Select_Proxy($model, $columns);
	    return $statement;
	}

    /**
    * This function returns an instance of the DB_ORM_Update_Proxy.
    *
    * @access public
    * @static
    * @param string $model                  the model's name
    * @return DB_ORM_Update_Proxy           an instance of the class
    */
	public static function update($model) {
		$statement = new DB_ORM_Update_Proxy($model);
	    return $statement;
	}

}
?>