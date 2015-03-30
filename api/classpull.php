<?php
/*
 * AJAX call connector for communicating class enrollment info to page scripts
 * Created February 2015
 */
/*
ini_set('display_errors','On');
error_reporting(E_ALL | E_STRICT); //*/

//Verify orderby - Separate query to verify that input orderby parameter is a valid column
$db2 = new PDO('mysql:host=db1.otc.edu;dbname=schedulesearch;charset=utf8', 'web3', 'BvCgWHyq');
$db2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql2 = "SELECT column_name FROM information_schema.columns WHERE table_name = 'class'";
$stmt2 = $db2->prepare($sql2);
$stmt2->execute();
$rows2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
$columns = array();
foreach($rows2 as $column_names)
{
	foreach($column_names as $col_name)
	{
		$columns[] = $col_name;
	}
}
//custom orderby parameters, not in the db
$columns[] = 'total_seats';
$columns[] = 'empty_seats';
$columns[] = 'total_seats_a';
$columns[] = 'empty_seats_a';
$columns[] = 'total_seats_d';
$columns[] = 'empty_seats_d';
//end verify orderby

//Connection used to service AJAX query
$db = new PDO('mysql:host=db1.otc.edu;dbname=schedulesearch;charset=utf8', 'web3', 'BvCgWHyq');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//components of the sql query, to be gathered from GET query
$select_asset = '';
$where_clause = "WHERE title IS NOT NULL AND LENGTH(title) > 0 ";
$group_by = "";
$order_by = "";
$sql = "";

//Set ordering of the rows
if(isset($_GET['orderby']) && $_GET['orderby'] != "")
{
	if(in_array($_GET['orderby'], $columns))
	{
		//COLUMN EXISTS 
		switch($_GET['orderby'])
		{
			case 'total_seats_a':
				$order_by = "ORDER BY total_seats ASC, empty_seats DESC";
				break;
			case 'total_seats':
				$order_by = "ORDER BY total_seats ASC, empty_seats DESC";
				break;
			case 'total_seats_d':
				$order_by = "ORDER BY total_seats DESC, empty_seats ASC";
				break;
			case 'empty_seats_a':
				$order_by = "ORDER BY empty_seats ASC";
				break;
			case 'empty_seats':
				$order_by = "ORDER BY empty_seats ASC";
				break;
			case 'empty_seats_d':
				$order_by = "ORDER BY empty_seats DESC";
				break;
			default:
				break;
		}
		
	}
	else
	{
		//COLUMN DOES NOT EXIST
		$order_by = "ORDER BY total_seats, empty_seats, department";
	}
}
else
{
	$order_by = "ORDER BY total_seats, empty_seats, department";
}

//Set the data selected, and so the grouping
if (isset($_GET['data']) && ($_GET['data'] == "enrollment")) 
{
    $select_asset = ' department';    
	
	if (isset($_GET['department']) && $_GET['department'] != " ") 
	{
		$select_asset .= (isset($_GET['data'])) ? '': ' department';
		$select_asset .= ', course, title';
		$department_code = strtoupper($_GET['department'])."%";
			
		$where_clause .= " AND department LIKE :department ";

		$class = '';
		$section = '';

		if (isset($_GET['course']) && $_GET['course'] != " ")
		{
			//note department must be set in order to specify a course
			$select_asset .= ', section';
			$where_clause .= " AND course = :course ";
			$course = $_GET['course'];
			
			if(isset($_GET['section']) && $_GET['section'] != " ")
			{
				$where_clause .= " AND section = :section ";
				$section = $_GET['section'];
				$select_asset .= ', sum(total_seats) as total_seats, sum(empty_seats) as empty_seats';
			}
			else
			{
				$select_asset .= ', sum(total_seats) as total_seats, sum(empty_seats) as empty_seats'; 
				$group_by .= " GROUP BY section";
			}
		} 
		else 
		{
			$select_asset .= ', sum(total_seats) as total_seats, sum(empty_seats) as empty_seats'; 
			$group_by .= " GROUP BY course";
		}
		
	}
	else
	{
		$select_asset .= ', sum(total_seats) as total_seats, sum(empty_seats) as empty_seats'; 
		$group_by = "GROUP BY department";
	}
}
else
{
	$select_asset = '*';
	if (isset($_GET['department']) && $_GET['department'] != " ") 
	{
		$department_code = "%".strtoupper($_GET['department'])."%";
		$where_clause .= " AND department LIKE :department ";

		if (isset($_GET['course']) && $_GET['course'] != " ")
		{
			//note department must be set in order to specify a class
			$where_clause .= " AND course = :course ";
			$course = $_GET['course'];

			if(isset($_GET['section']) && $_GET['section'] != " ")
			{
				$where_clause .= " AND section = :section ";
				$section = $_GET['section'];
			}
			else
			{
				$group_by .= " GROUP BY section";
			}
		} 
		else 
		{
			$group_by .= " GROUP BY course";
		}
	}
	else
	{
		$group_by = "GROUP BY department";
	}
	
}

//Specifying semester
if(isset($_GET['semester']) && $_GET['semester'] != "" && $_GET['semester'] != "all")
{
	$where_clause.= " AND semester = :semester";
}


//Build the sql and bind the parameters
$sql = "SELECT $select_asset FROM class $where_clause $group_by $order_by";

$query = $db->prepare($sql);

if(isset($_GET['semester']) && $_GET['semester'] != "" && $_GET['semester'] != "all")
{
	$semester = ucfirst($_GET['semester']);
	$query->bindParam(':semester', $semester);
}
if(isset($_GET['department']))
{
	$query->bindParam(':department', $department_code);
	if(isset($_GET['course']))
	{
		$query->bindParam(':course', $course);
		if(isset($_GET['section']))
		{
			$query->bindParam(':section', $section);
		}
	}
}
		

if(isset($_GET['department']) || isset($_GET['data']))
{
	$class_info = $query->execute();
}
		
		
		
		
		$rowcount = $query->rowCount();

		$department_array = array();
		$class_row = $query->fetchAll(PDO::FETCH_ASSOC);
		for ($i = 0; $i < $rowcount; $i++) {
			array_push($department_array, $class_row[$i]);
		}
		
		foreach($department_array as $key=>$row)
		{
			$department_array[$key]["department"] = substr($row['department'], 0, 3);
		}

		$return_array = array();
		if(isset($_GET['department']))
		{
			include('departments.inc.php');
			if(isset($department_titles[strtoupper($_GET['department'])]))
			{
				$return_array['department_name'] = $department_titles[strtoupper($_GET['department'])];
			}
			else
			{
				$return_array['department_name'] = $class_row[0]['department'];
			}
			
			$return_array['department_code'] = strtoupper($_GET['department']);
			if(isset($_GET['course']))
			{
				$return_array['course'] = $_GET['course'];	
				if(isset($_GET['section']))
				{
						$return_array['section'] = $_GET['section'];
				}
			}
		}

		
		
		$return_array['row_count'] = $rowcount;

		$return_array['courses'] = $department_array;
		echo json_encode($return_array);
		


	
	

$db = null;
$db2 = null;
exit();
?>
<!DOCTYPE html>
<html>
    <head><title>Testing database class pulls</title></head>
    <body>
        <p>This text should not be visible. This 'page' is only meant to be access by AJAX calls.</p>
    </body>
</html>