jQuery(function($) {
	var url = 'quiz_'+quizName+'.php';
	//var url = 'quiz_test.php';
	var statAll = 'quiz_stat.php';
	var period = 'today';
	var users = 'all';

	$('.page .date-range').change(function() {
	   period = $(this).find('option:selected').attr('name');
	   users = $('select.select-users').find('option:selected').attr('name');
	   getStatData();
	   getPerQuestionStat();
	   getStatAll();
	});

	$('.page .select-users').change(function() {
	   period = $('select.date-range').find('option:selected').attr('name');
	   users = $(this).find('option:selected').attr('name');
	   getStatData();
	   getPerQuestionStat();
	});	

	$('.header .select-quiz').change(function() {
	   quiz = $(this).find('option:selected').attr('name');
	   window.location = 'quiz_'+quiz+'_admin.php';
	});	

	function getStatData() {
    	$('.page .quiz-stats').html('<div class="quiz-title">'+quizName+' quiz</div><div class="loader"></div>');

		$.ajax({ 
		    url: statAll+'?action=getQuizStat&range='+period+'&users='+users+'&quiz='+quizName, 
		    type:"POST",
		    datatype: "json",
		    data: {  },
		    success: function(response){
		    	data = $.parseJSON(response);
		    	$('.page .quiz-stats .loader').remove();
		    	$('.page .quiz-stats').append('<div class="stat total-users"><h2>Total users:</h2><div class="val">'+data.total_users+'</div></div>');
		    	$('.page .quiz-stats').append('<div class="stat "><h2>Remove uses:</h2><div class="val">'+data.remove_two_count+' ('+data.remove_two_percentage+'%)'+'</div></div>');
		    	$('.page .quiz-stats').append('<div class="stat page-views-per-user"><h2>Pageviews/visitor:</h2><div class="val">'+data.views_per_user+'</div></div>');	
		    	$('.page .quiz-stats').append('<div class="stat amazon-users"><h2>Logged to Amazon:</h2><div class="val">'+data.amazon_users+' ('+data.percent_amazon_users+'%)'+'</div></div>');
		    	$('.page .quiz-stats').append('<div class="stat finished-users"><h2>Completed:</h2><div class="val">'+data.total_finished_users+' ('+data.percent_finished_users+'%)'+'</div></div>');	
		    	$('.page .quiz-stats').append('<div class="stat avg-ans-right"><h2>Average answer right:</h2><div class="val">'+data.avg_correct_ans+'%</div></div>');
		    	
		    }
		});		
	}

	function getStatAll() {
    	$('.page .overall-stat').html('<h1>STATS FROM ALL QUIZZES AND SLIDE SHOWS</h1><div class="loader"></div>');		
		$.ajax({ 
		    url: statAll+'?action=getStat&range='+period, 
		    type:"POST",
		    datatype: "json",
		    data: {  },
		    success: function(response){
		    	data = $.parseJSON(response);
		    	$('.page .overall-stat .loader').remove();
		    	$('.page .overall-stat').append('<div class="total-visitors">Total visitors: <span class="val">'+data.total_visitors+'</span></div>');
		    	$('.page .overall-stat').append('<div class="total-page-views-per-user">Average page views per visitors: <span class="val">'+data.pages_per_user+'</span></div>');
		    }
		});		
	}

	var table = '';
	function getPerQuestionStat() {
		if(table !== '') table.destroy();
		$('#questions-table').html('<div class="loader"></div>');
		$.ajax({ 
		    url: url+'?action=getPerQuestionStat&range='+period+'&users='+users,
		    type:"POST",
		    datatype: "json",
		    data: {  },
		    success: function(response){
		    	questions = $.parseJSON(response);
		    	$('#questions-table').html('<thead><tr><th>Q numb</th><th>pic</th><th>attempts</th><th>correct</th><th>correct answer</th><th>remove two uses</th></tr></thead><tbody></tbody>'); //<td>%'+item.answers+'</td> <td>'+item.count+', got it right: '+item.right_ans_count+'</td>
				//console.log(questions);
				var arr = [];
				$.each(questions, function(index, item) {
					arr.push(item.answers);
				});
				arr.sort(function(a, b) { return b - a; });
				var maxValues = [];
				maxValues.push(arr[0]);
				maxValues.push(arr[1]);
				maxValues.push(arr[2]);
				var minValues = [];
				minValues.push(arr[arr.length-1]);
				minValues.push(arr[arr.length-2]);
				minValues.push(arr[arr.length-3]);
				$.each(questions, function(index, item) {
				    if(parseInt(item.question) == 1) hash = '';
				    else hash = '#'+item.question;
				    if($.inArray(item.answers, maxValues) >= 0) item.answers = '<span style="color:#0fdc0f; font-weight:bold;">'+item.answers+'</span>';
				    if($.inArray(item.answers, minValues) >= 0) item.answers = '<span style="color:red; font-weight:bold;">'+item.answers+'</span>';					
				    $('#questions-table tbody').append('<tr><td>'+item.question+'</td><td><a href="'+quizUrl+hash+'" target="_blank"><img src="'+item.image+'"</a></td><td>'+item.count+'</td><td>'+item.right_ans_count+' ( '+item.answers+'% )</td><td>'+item.correct_answer+'</td><td>'+item.remove_two_count+'</td></tr>');
				});
				table = $('#questions-table').DataTable({"pageLength": 50, "searching": false, "paging": false, "ordering": false, "lengthChange": false, "info": false}); 
		    }
		});		
	}	

	getStatData();	

	getStatAll();

	getPerQuestionStat();	
});

