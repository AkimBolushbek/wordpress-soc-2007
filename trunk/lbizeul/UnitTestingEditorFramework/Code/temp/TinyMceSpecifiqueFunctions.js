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

function setselectedtext(start, end){ 
var inst = tinyMCE.getInstanceById("TinyMceInstance");
var focusElm = inst.getFocusElement();
inst.selection.setSelectedText(start,end);
}

