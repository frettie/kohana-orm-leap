<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Copyright © 2011–2013 Spadefoot Team.
 *
 * Unless otherwise noted, LEAP is licensed under the Apache License,
 * Version 2.0 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License
 * at:
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
 * This class builds a MS SQL update statement.
 *
 * @package Leap
 * @category MS SQL
 * @version 2015-08-23
 *
 * @see http://msdn.microsoft.com/en-us/library/aa260662%28v=sql.80%29.aspx
 *
 * @abstract
 */
abstract class Base_DB_MsSQL_Update_Builder extends DB_SQL_Update_Builder {

	/**
	 * This function returns the SQL statement.
	 *
	 * @access public
	 * @override
	 * @param boolean $terminated           whether to add a semi-colon to the end
	 *                                      of the statement
	 * @return string                       the SQL statement
	 *
	 * @see http://stackoverflow.com/questions/655010/how-to-update-and-order-by-using-ms-sql
	 */
	public function statement($terminated = TRUE) {
		$alias = ($this->data['table'] == 't0') ? 't1' : 't0';

		$sql = "WITH {$alias} AS (";

		$sql .= 'SELECT';

		if ($this->data['limit'] > 0) {
			$sql .= " TOP {$this->data['limit']}";
		}

		$sql .= " * FROM {$this->data['table']}";

		if ( ! empty($this->data['where'])) {
			$append = FALSE;
			$sql .= ' WHERE ';
			foreach ($this->data['where'] as $where) {
				if ($append AND ($where[1] != DB_SQL_Builder::_CLOSING_PARENTHESIS_)) {
					$sql .= " {$where[0]} ";
				}
				$sql .= $where[1];
				$append = ($where[1] != DB_SQL_Builder::_OPENING_PARENTHESIS_);
			}
		}

		if ( ! empty($this->data['order_by'])) {
			$sql .= ' ORDER BY ' . implode(', ', $this->data['order_by']);
		}
		/*
		if (($this->data['offset'] >= 0) AND ($this->data['limit'] > 0) AND ! empty($this->data['order_by'])) {
			$sql = 'SELECT [outer].* FROM (';
			$sql .= 'SELECT ROW_NUMBER() OVER(ORDER BY ' . implode(', ', $this->data['order_by']) . ') as ROW_NUMBER, ' . $columns_sql . ' FROM ' . $this->data['from'] . ' ' . $where_sql;
			$sql .= ') AS [outer] ';
			$sql .= 'WHERE [outer].[ROW_NUMBER] BETWEEN ' . ($this->data['offset'] + 1) . ' AND ' . ($this->data['offset'] + $this->data['limit']);
			$sql .= ' ORDER BY [outer].[ROW_NUMBER]';
		}
		*/
		$sql .= ") UPDATE {$alias}";

		$table = $this->data['table'];
		$table = str_replace(DB_MsSQL_Precompiler::_OPENING_QUOTE_CHARACTER_, '', $table);
		$table = str_replace(DB_MsSQL_Precompiler::_CLOSING_QUOTE_CHARACTER_, '', $table);

		$ts = DB_SQL::select('default')
				->from('INFORMATION_SCHEMA.COLUMNS')
				->column('COLUMN_NAME')
				->where('TABLE_SCHEMA', '=', 'dbo')
				->where(DB_SQL::expr('COLUMNPROPERTY(object_id(TABLE_NAME), COLUMN_NAME, \'IsIdentity\')'), '=', 1)
				->where('TABLE_NAME', '=', $table)
				->query();

		$identity_column = $ts->is_loaded() ? $ts->get('COLUMN_NAME') : NULL;

		if ( ! empty($this->data['column'])) {
			$column = $this->data['column'];

			if ( ! empty($identity_column)) {
				unset($column[DB_MsSQL_Precompiler::_OPENING_QUOTE_CHARACTER_ . strtolower($identity_column) . DB_MsSQL_Precompiler::_CLOSING_QUOTE_CHARACTER_]);
			}
			$sql .= ' SET ' . implode(', ', array_values($column));
		}

		if ($terminated) {
			$sql .= ';';
		}

		return $sql;
	}

}
