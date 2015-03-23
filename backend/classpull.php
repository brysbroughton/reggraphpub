<?php
/*
 * AJAX call connector for communicating class enrollment info to page scripts
 * Created February 2015
 */
ini_set('display_errors','On');
error_reporting(E_ALL | E_STRICT); //*/
//include('/var/www/html.intra/webservices/dbFunctions.php');
$db2 = new PDO('mysql:host=db1.otc.edu;dbname=schedulesearch;charset=utf8', 'web3', 'BvCgWHyq');
$db2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql2 = "SELECT column_name FROM information_schema.columns WHERE table_name = 'class'";
$stmt2 = $db2->prepare($sql2);
$stmt2->execute();
$rows2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
//var_dump($rows2);
$columns = array();
foreach($rows2 as $column_names)
{
	foreach($column_names as $col_name)
	{
		$columns[] = $col_name;
	}
}

$columns[] = 'total_seats';
$columns[] = 'empty_seats';
$columns[] = 'total_seats_a';
$columns[] = 'empty_seats_a';
$columns[] = 'total_seats_d';
$columns[] = 'empty_seats_d';

$db = new PDO('mysql:host=db1.otc.edu;dbname=schedulesearch;charset=utf8', 'web3', 'BvCgWHyq');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
$select_asset = '';
$where_clause = "WHERE title IS NOT NULL AND LENGTH(title) > 0 ";
$group_by = "";
$order_by = "";
$sql = "";

if(isset($_GET['orderby']) && $_GET['orderby'] != "")
{
	if(in_array($_GET['orderby'], $columns))
	{
		//echo "COLUMN EXISTS";
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
		//echo "COLUMN DOES NOT EXIST";
		$order_by = "ORDER BY total_seats, empty_seats, department";
	}
}
else
{
	$order_by = "ORDER BY total_seats, empty_seats, department";
}


if (isset($_GET['data']) && ($_GET['data'] == "enrollment")) 
{
    $select_asset = ' department';    
	//rows without title are notes
	if (isset($_GET['department']) && $_GET['department'] != " ") 
	{
		$select_asset .= (isset($_GET['data'])) ? '': ' department';
		$select_asset .= ', course, title';
		$department_code = strtoupper($_GET['department'])."%";
		//print "<p>$department_code</p>"; //debug
			
		//$department_code_esc = mysql_real_escape_string($department_code, $db);
		
		$where_clause .= " AND department LIKE :department ";
		//print "<p>DEPARTMENT SQL: $sql</p>"; //debug
		$class = '';
		$section = '';
		 //complete me
		if (isset($_GET['course']) && $_GET['course'] != " ") {//note department must be set in order to specify a class
			//$sql .= " AND course = \"{$_GET['class']}\" ";
			$select_asset .= ', section';
			$where_clause .= " AND course = :course ";
			$course = $_GET['course'];
			//print "<p>COURSE SQL: $sql</p>"; //debug
			
			if(isset($_GET['section']) && $_GET['section'] != " ")
			{
				//$sql .= " AND section = \"{$_GET['section']}\" ";
				$where_clause .= " AND section = :section ";
				$section = $_GET['section'];
				$select_asset .= ', sum(total_seats) as total_seats, sum(empty_seats) as empty_seats';
				//print "<p>SECTION SQL: $select_asset</p>"; //debug
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
		//print "<p>$department_code</p>"; //debug
		$where_clause .= " AND department LIKE :department ";
		 //complete me
		if (isset($_GET['course']) && $_GET['course'] != " ") {//note department must be set in order to specify a class
			$where_clause .= " AND course = :course ";
			$course = $_GET['course'];
			//print "<p>COURSE SQL: $sql</p>"; //debug
			if(isset($_GET['section']) && $_GET['section'] != " ")
			{
				//$sql .= " AND section = \"{$_GET['section']}\" ";
				$where_clause .= " AND section = :section ";
				$section = $_GET['section'];
				//print "<p>SECTION SQL: $sql</p>"; //debug
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
		if(isset($_GET['semester']) && $_GET['semester'] != "" && $_GET['semester'] != "all")
		{
			$where_clause.= " AND semester = :semester";
		}
		$sql = "SELECT $select_asset FROM class $where_clause $group_by $order_by";
		//print('<p>SQL: '.$sql.'</p>');
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
		
		
		//print(":semester = $semester");
		//print "<p>$sql</p>"; //debug
		//print "<p>DEPARTMENT: $dept_code</p>"; //debug
		//print "<p>CLASS: $class</p>"; //debug
		//print "<p>SECTION: $section</p>"; //debug
		//echo '<br /><br />&nbsp;';
		//$query->debugDumpParams();
		//echo '<br /><br />';
		if(isset($_GET['department']) || isset($_GET['data']))
		{
			$class_info = $query->execute();
			//print("SQL EXECUTED: " .$sql);
		}
		
		
		
		
		$rowcount = $query->rowCount();
		//print "<p>rowcount: $rowcount</p>"; //debug
		$department_array = array();
		$class_row = $query->fetchAll(PDO::FETCH_ASSOC);
		for ($i = 0; $i < $rowcount; $i++) {
			
			array_push($department_array, $class_row[$i]);
			//print "<p>{$class_row[$i]['title']}</p>"; //debug
			//print "<p>" . $class_row[$i]['title']
		}
		
		foreach($department_array as $key=>$row)
		{
			$department_array[$key]["department"] = substr($row['department'], 0, 3);
		}
		//var_dump($department_array);
		//echo "<br /><br /><br />";
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
		else
		{
			/* foreach($department_array as $row)
			{
				$return_array['department_name'] = $department_array[$key]["department"];
			} */
		}
		
		
		$return_array['row_count'] = $rowcount;
		//foreach($dept_array as $key1=>$i1)
		//{
		//	foreach($i1 as $key2=>$i2)
		//	{
		//		foreach($i2 as $key3=>$i3)
		//		{
		//			/* echo "<br /><br />";
		//			print ($key3.": ");
		//			print($i3); */
		//			$return_array[$key3] = $i3;
		//		}
		//		//print("<hr />");
		//	}
		//}
		//
		
		//var_dump($department_titles);
		$return_array['courses'] = $department_array;
		//$encoded = json_encode($return_array);
		//print $return_array;
		echo json_encode($return_array);
		
		//return json_encode($return_array);

	
	
//$query = null;
//$db = null;
exit();
?>
<!DOCTYPE html>
<html>
    <head><title>Testing database class pulls</title></head>
    <body>
        <p>This text should not be visible. This 'page' is only meant to be access by AJAX calls.</p>
    </body>
</html>