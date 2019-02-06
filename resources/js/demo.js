console.log("Hey, My JS is working!");


$('#parameterDiv').click(function(){
	$.get(mw.util.wikiScript(),{
	action:'ajax',
	rs:'DemoExtension::AjaxFunction',
	rsargs:[$(this).text()]
	},function(data){
		$("#parameterDiv").html(data);
	});
});
