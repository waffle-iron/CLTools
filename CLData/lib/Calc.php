<?php
namespace CLTools\CLData;

	/**
	 *  CLTools
	 *  Author: Josh Carlson
	 *  Email: jcarlson(at)carlso(dot)net
	 */

	/*
	 *  Calc.php - a library for performing various calculations on CL data
	 */

	class Calc extends Data
	{
		private $measurement = '';
		public $searchString = '';

		public function __construct(
			$dbConfig,
			$measurement = 'max',
			$dataField = 'price',
			$searchString = '')
		{
			// no sanatization needed, results will be verified as they're utilized within functions
			parent::__construct($dbConfig);
			$this->setMeasurementType($measurement);
			$this->setDataField($dataField);
			$this->searchString = $searchString;
		}
		
		//  SETTERS
		public function setMeasurementType($measurement)
		{
			/*
			 * 	Purpose: set which measurement type to find from a set list
			 *
			 * 	Params:
			 * 		$measurement :: string :: measurement to calculate
			 *
			 * 	Returns: bool
			 */
			
			$measurementTypesAvailable = array(
				'AVG',
				'MAX',
				'MIN',
				'COUNT'
			);
			
			// normalize measurement
			$measurement = strtoupper($measurement);
			
			// check if given measurement is in list of approved measurement types
			if(in_array($measurement, $measurementTypesAvailable, true))
			{
				// approved :)
				$this->measurement = $measurement;
				
				return true;
			}
			
			// not approved >:|
			throw new \Exception('invalid measurement type provided');
		}
		
		public function setDataField($dataField)
		{
			/*
			 * 	Purpose: select data field to fetch from a set list
			 *
			 * 	Params:
			 * 		$dataField :: string :: data field to fetch
			 *
			 * 	Returns: bool
			 */
			
			$availableDataFields = array(
				'location',
				'price',
				'name'
			);
			
			// normalize given datafield to all lowercase
			$dataField = strtolower($dataField);
			
			// check if data field is listed as apart of approved list
			if(in_array($dataField, $availableDataFields, true))
			{
				// approved :)
				$this->dataField = $dataField;
				
				return true;
			}
			
			// not approved >:|
			throw new \Exception('invalid data field provided');
		}

		// OTHER FUNCTIONS
		public function calculate($sort = 'asc', $limit = 0)
		{
			/*
			 * 	Purpose: calculate metric based on measurement type set and field
			 *
			 * 	Params:
			 * 		* $sort :: str :: sort order of SQL results
			 * 		* $limit :: int :: max number of results to return (<0 = unlimited)
			 *
			 * 	Returns: NONE
			 */
			
			$sqlParams = array();
			$lcMeasurement = strtolower($this->measurement);
			
			// generate sql for calculation based on measurement and data field
			$sql = '';
			if($lcMeasurement === 'count')
			{
				// append clause to add data field as index
				$sql .= '
					SELECT
						'.$this->dataField.',
						count(*) as count_'.$this->dataField;
			}
			else
			{
				$sql .= '
					SELECT
						'.$this->measurement.'('.$this->dataField.') AS '.$lcMeasurement.'_'.$this->dataField;
			}
			$sql .= '
					FROM
						listings';
			
			// check if searchString is set
			if(!empty($this->searchString))
			{
				// append searchString SQL clause
				$sql .= '
					WHERE
						(
							listing_id = :searchString
							OR url like (%:searchString%)
							OR location like (%:searchString%)
							or name like (%:searchString%)
						)';
				
				$sqlParams['searchString'] = $this->searchString;
			}
			
			// add sort and limit clauses if set and needed
			if($lcMeasurement === 'count')
			{
				// sort
				// normalize
				$sort = strtoupper($sort);
				if($sort === 'ASC' || $sort === 'DESC')
				{
					$sql .= '
						GROUP BY 1
						ORDER BY 2 '.$sort;
				}
				// limit
				// normalize
				$limit = filter_var($limit, \FILTER_SANITIZE_NUMBER_INT);
				if(0 < $limit)
				{
					$sql .= '
						LIMIT '.$limit;
				}
			}
			
			// init stmt and execute
			$stmt = $this->dbConn->prepare($sql);
			$stmt->execute($sqlParams);
			
			// fetch result
			if(!$result = $stmt->fetchAll(\PDO::FETCH_NUM))
			{
				// request is bad
				$pdoError = $stmt->errorInfo();
				throw new \Exception('query could not be completed [ '.$pdoError[2].' ]');
			}
			
			// set result array as data
			parent::setData($result);
			
		}
	}
?>