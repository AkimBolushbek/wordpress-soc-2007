
 function testExecutionOfScenariosGenerateByPhp(){ 
 assertEquals('Hello World', 'Hello World') ;
 } 
 
function testScenariosGenerate_1(){ 
 	 SetContentToEditor("Hello World I'm Luc
I live In Paris, Je suis francais j'�cris donc avec des accents and some sp�cials chars like % ^$ ' '' 
); 
 	 SetFocus(0,125); 
 	 ExecuteCommand(Bold); 
 	 this->assertEqual("<strong>Hello World I'm Luc
I live In Paris, Je suis francais j'�cris donc avec des accents and some sp�cials chars like % ^$ ' '' </strong>",GetContentFromEditor()) ;
 } 
function testScenariosGenerate_2(){ 
 	 SetContentToEditor("Hello World I'm Luc
I live In Paris, Je suis francais j'�cris donc avec des accents and some sp�cials chars like % ^$ ' '' 
); 
 	 SetFocus(0,125); 
 	 ExecuteCommand(Italic); 
 	 this->assertEqual("<em>Hello World I'm Luc
I live In Paris, Je suis francais j'�cris donc avec des accents and some sp�cials chars like % ^$ ' '' </em>",GetContentFromEditor()) ;
 } 
function testAssertAlwaysFalse(){
SetContentToEditor('Hello World');
assertNotEquals('Hello', GetContentFromEditor());
}