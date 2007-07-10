//SetOfEditorCommandFunctions

 function testExecutionOfScenariosGenerateByPhp(){ 
 assertEquals('Hello World', 'Hello World') ;
 } 
 
 
function SetContentToEditor(InputText){
tinyMCE.setContent(InputText);
}
function GetContentFromEditor(){
var EditorContent;
var TinyId = document.getElementById("TinyMceInstance");
EditorContent = tinyMCE.getContent();
return EditorContent
}

function ExecuteCommand(command){
tinyMCE.execCommand(command);
}

//a revori si valide
function SetFocus(StartChar,SelectionSize){ // fake all selection
TinyMceInstance.focus();
TinyMceInstance.select();
}

