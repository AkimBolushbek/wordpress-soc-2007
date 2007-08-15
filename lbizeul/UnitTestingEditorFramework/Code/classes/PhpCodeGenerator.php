<?php
define('INPUT_FILE',"../temp/The_Testing_Text.txt");

function Write_Output_File($order,$name,$Texte){
	$fp = fopen("../temp/".$order."_".$name,"w");
	fwrite($fp,$Texte);
}

function Write_ToTheEndOfOutput_File($file,$Texte){
	$fp = fopen($file,"a");
	fwrite($fp,$Texte);
}

function TakeAllDatasInAFile($PathToFile){
	$FileSize=filesize($PathToFile); 
	$fp=fopen($PathToFile,"r");
	$AllContent=fread($fp,$FileSize); 
return $AllContent;
}

function CountNumberOfCharsInAText($InputFileContent){#string
	$NumberOfChar = strlen($InputFileContent);
return $NumberOfChar;
}

function TakeOnePartOfAString($InputFileContent,$StartChar,$SelectionSize){
	$SubString =  substr($InputFileContent, $StartChar, $SelectionSize);
return($SubString);
}

function ReplaceTexte($OriginalString, $SearchString,$ReplaceString){
}

function ConvertAnActionInATag($Action){
	$ActionToTag = array ('Bold'=>'strong', 'Italic' =>'em');
	$tag = $ActionToTag[$Action];
	return $tag;
}
function AdATagOnAString($tag, $SelectedText){
	$StringWithTag="<".$tag.">".$SelectedText."</".$tag.">";
	return $StringWithTag;
}
function GenerateResult($InputText,$StartChar,$SelectionSize,$Tag){
	$SelectedText = TakeOnePartOfAString($InputText,$StartChar,$SelectionSize);
	$NewString     = AdATagOnAString($Tag, $SelectedText);
	$WaitedText   = str_replace($SelectedText,$NewString,$InputText);
	return $WaitedText;
}

function GenerateAJsUnitTest($order,$StartChar,$SelectionSize,$Action,$InputText,$WaitedText ){
	$test="function testScenariosGenerate_".$order."(){ \n \t SetContentToEditor(\"".$InputText."\n); \n \t setselectedtext(".$StartChar.",".$EndChar."); \n \t ExecuteCommand(".$Action."); \n \t this->assertEqual(\"".$WaitedText."\",GetContentFromEditor()) ;\n } \n";
return $test;
}


@unlink(".../temp/JsUnitGeneratingTestsScenario.js");

$InputText=TakeAllDatasInAFile(INPUT_FILE);
$FirstTest="\n function testExecutionOfScenariosGenerateByPhp(){ \n assertEquals('Hello World', 'Hello World') ;\n } \n \n";

Write_ToTheEndOfOutput_File('../temp/JsUnitGeneratingTestsScenario.js',$FirstTest);

$FileContent = file('TestScenario.csv');
$i=0;
	foreach ($FileContent as $FileLine){
		$Line = split(";",$FileLine);
		$Scenarios[$i]['Order']           =$Line[0];
		$Scenarios[$i]['Action']          =$Line[1];
		$Scenarios[$i]['StartChar']      =$Line[2];
		$Scenarios[$i]['EndChar'] 	 =$Line[3];
		$Scenarios[$i]['InputText'] 	 =$Line[4];
		$Scenarios[$i]['OuputText'] 	 =$Line[5];
	$i++;
	}

	foreach ($Scenarios as $Scenario){
		$Tag            = ConvertAnActionInATag($Scenario['Action']);
		$WaitedText  =GenerateResult($InputText,$Scenario['StartChar'],$Scenario['SelectionSize'],$Tag);
		$UnitTest      =GenerateAJsUnitTest($Scenario['Order'],$Scenario['StartChar'],$Scenario['EndChar'],$Scenario['Action'],$InputText,$WaitedText );
		Write_ToTheEndOfOutput_File('../temp/JsUnitGeneratingTestsScenario.js',$UnitTest);
	}




?>