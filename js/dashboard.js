jQuery(function($) {

   $('body').on('change', '#selectPeriod', function() { if($(this).val() == '') { alert('Under constraction'); return false; } load_data($(this).val()); });

   var labelsDay = ['1am', '3am','5am','7am','9am','11am','1pm','3pm','5pm','7pm','9pm','11pm'];
   var labelsWeek = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

   function startChart(data, period) {
      if(period == 'last24h') dataLabels = labelsDay;
      if(period == 'thisWeek' || period == 'lastWeek') dataLabels = labelsWeek;
      if(period == 'thisMonth' || period == 'lastMonth') { dataLabels = []; var i = 1; $(data['0']).each(function() { dataLabels.push(i); i++; }); }

      var chart = new Chartist.Line('#main-chart', 
      {
         labels: dataLabels,
         series: data 
      }, 
      {
         low: 0,
         showArea: true,
         fullWidth: true,
         axisY: { onlyInteger: true  },
         plugins: [
            Chartist.plugins.tooltip()
         ]
      }); 

      chart.on('draw', function(data) {
        if(data.type === 'line' || data.type === 'area') {
          data.element.animate({
            d: {
              begin: 2000 * data.index,
              dur: 2000,
              from: data.path.clone().scale(1, 0).translate(0, data.chartRect.height()).stringify(),
              to: data.path.clone().stringify(),
              easing: Chartist.Svg.Easing.easeOutQuint
            }
          });
        }
      });      
   }

   function load_data(period) {
      $('#main-chart').html('<div class="loader"></div>'); 
      $.ajax({
         url: 'getChartData',
         type:"POST",
         data: { 'period': period },
         success: function(data){
            $('#main-chart .loader').remove();
            startChart(data['chart'], period);
            $('#emailSentCount').text(data['sentTotal']);
            $('#openedCount').text(data['openedTotal']);
            $('#clicksCount').text(data['clicksTotal']);
            $('#subscribersAddedCount').text('+'+data['subscribersAdded']);
         }
      });   
   }

   load_data('last24h');

});
