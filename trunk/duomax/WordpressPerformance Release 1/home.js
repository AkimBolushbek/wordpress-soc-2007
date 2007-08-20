
function handleChange(object)
{
	if(object.value == "generate")
	{
		document.getElementById("formBoxGenerate").style.display = "block";
		document.getElementById("formBoxCompare").style.display = "none";
	}
	
	if(object.value == "compare")
	{
		document.getElementById("formBoxGenerate").style.display = "none";
		document.getElementById("formBoxCompare").style.display = "block";
	}
}