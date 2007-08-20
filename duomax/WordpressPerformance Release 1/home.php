<?php
/*
 * Created on Jul 4, 2007
 *
 * Author: Andrew Nelson
 * Processes the input from home.html
 */
 $action = $_POST["operation"];
 if($action != null && $action != "")
 {
 	if($action == "generate")
 	{
 		$fileName = $_POST["saveFile"];
 		$runs = $_POST["runs"];
 		if($fileName != null && $fileName != "")
 		{
 			echo "Generating Your Results. Please be Patient<br />";
 			ob_flush();
			flush();
			 		
 			include "results.php";
	 		$result = new Results(); 
    
 			$result ->runTests($runs);
 			$result ->writeXML($fileName);
 			
 			echo "Your file has been saved to $fileName<br />";
 			echo "<a href = 'home.php'>Run the Tool Again</a><br />";
 			return;
 		}
 		
 		else
 			echo "You must input a file name!<br />";
 	}
 	
 	if($action == "compare")
 	{
 		$fileOne = $_POST["firstFile"];
 		$fileTwo = $_POST["secondFile"];
 		
 		if($fileOne != null && $fileOne != "" && $fileTwo != null && $fileTwo != "")
 		{
 			
 			if(!file_exists($fileOne))
 			{
 				echo "The first specified file doesn't exist<br/>";
 				echo "<a href = 'home.php'>Run the Tool Again</a><br />";
 				return;
 			}
 			
 			if(!file_exists($fileTwo))
 			{
 				echo "The second specified file doesn't exist<br/>";
 				echo "<a href = 'home.php'>Run the Tool Again</a><br />";
 				return;
 			}
 
 			
 			//echo "<style>";
 			//include "stylenew.css";
 			//echo "</style>";
 			include "xmlLoader.php";
 			$xmlLoader = new XMLLoader($fileOne);
 			$doc = new DOMDocument();
 			$doc->load($fileTwo);
 			$results; 
 			$xmlLoader ->compareXML($doc, $results);
 			
 			$previousCategory = "";
 			echo "<link rel=\"stylesheet\" href=\"style.css\" media=\"screen\" type=\"text/css\" />";
 			foreach($results as $result)
 			{				
 				if(strcmp($previousCategory,$result->category) != 0)
 				{
 					if(strcmp($previousCategory,"") !=0)
 					{
 						echo "</table>";
 						echo "<br/>";
 					}
 					echo "<table summary=\"Result Table - $result->category\" class=\"main\" cellspacing=\"0\">";
					echo "<tr>";
					echo "<td colspan=\"3\" class=\"header\">Category: $result->category</td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td class=\"header\">Function</td>";
						echo "<td class=\"header\">Description</td>";
						echo "<td class=\"header\">Result Difference</td>";
					echo "</tr>";
 				}
 				echo "<tr>"; 
					echo "<td class=\"body\">$result->name</td>";  
					echo "<td class=\"body\">$result->description</td>"; 
					echo "<td class=\"body\">$result->result %</td>"; 
				echo "</tr>"; 
				
				//update for next loop
				$previousCategory = $result->category;
 			}
 			
 			return;
 		}
 		
 		else
 			echo "You must input names for both files!<br />";		
 			
 	}
 	
 }  
	
include "home.html";
?>
