@extends('layouts.app')

@section('content')



<div class="" style="padding:50px 50px 50px 50px">
    <div class="row justify-content-center">
      <div class="col-xl-6 col-lg-12">
        <div class=" text-center">
      <h2>Показывает отпуска по месяцам</h2>
        </div>
        <table class="table table-striped table-bordered table-hover" id="tableMain">
          <thead>
            <tr>
              <td>Сотрудник</td>
              <td>Дата начала</td>
              <td>Дата конца</td>
              <td>Утверждение Руководителем</td>
            </tr>
          </thead>
          <tbody>

          </tbody>

        </table>
        <h3>Инструкция</h3>
        <p>Выбрать дни отпуска можно в календаре. Чтобы выбрать несколько дней зажать левой кнопкой мыши и выбрать промежуток дней.</p>
        <p>Чтобы изменить отпуск, нужно зажать левой кнопкой мыши отпуск в календаре и перенести его. Можно менять только свой отпуск.</p>
        <p>Что бы удалить отпуск, нужно кликнуть по нему левой кнопкой мыши.</p>
        <p>Руководитель может утвердить отпуск, после этого нельзя изменить отпуск.</p>
        <p>Любой сотрудник может выбрать неограниченное число отпусков и дней.</p>
        </div>

        <div class="col-xl-6 col-lg-12">
          <div class="container">
              <div class="response"></div>
              <div id='calendar'></div>
          </div>

        </div>
      </div>
</div>

<td></td>
<style media="screen">
  .fc-title{
    color:white;
  }
  .fc-time{
    opacity: 0
  }
</style>
<script>
$(document).ready(function() {
  var user = {!! auth()->user()->toJson() !!};

  var SITEURL = "{{url('/')}}";

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	var calendar = $('#calendar').fullCalendar({
		editable: true,
		events: SITEURL + "/fullcalendar",
		displayEventTime: true,
		editable: true,
		eventRender: function(event, element, view) {

      var prov = 0
      $("tr").each(function() {

        if (event._id == $(this).attr('data-idt'))
        {
          prov = 1
        }

      });

      if (prov == 0)
      {
        st = event.start['_i'].split(' ')[0]
        end = event.end['_i'].split(' ')[0]
        if (event.user == undefined){
          nameuser = user.name
        }
        else{
          nameuser = event.user
        }

        edit = 0
        ytvform = '<input  type="checkbox"  id="ytv" '
        if(event.ytv=="1"){
          ytvform = ytvform+" "+ "checked"
          edit = 1
          stform = '<input  id="startdate" type="date" value="'+st+'" disabled>'
          endform = '<input  id="enddate" type="date" value="'+end+'" disabled>'
        }
        else{
          stform = '<input  id="startdate" type="date" value="'+st+'" >'
          endform = '<input  id="enddate" type="date" value="'+end+'" >'
        }
        if (user.Director == "1"){
          console.log('ok')
        }else{
          ytvform = ytvform+" "+ " disabled"
        }
        ytvform = ytvform+'>'

        $('#tableMain tbody').append('<tr id="'+event.id+'" data-idt="'+event._id+'" data-edit="'+edit+'"> <td id="nameuser">'+nameuser+'</td> <td>'+stform+'</td> <td>'+endform+'</td> <td>'+ytvform+'</td> </tr>')
      }
			if(event.allDay === 'true') {
				event.allDay = true;
			} else {
				event.allDay = false;
			}
		},
		selectable: true,
		selectHelper: true,
		select: function(start, end, allDay) {
			var title = user.name//prompt('Event Title:');
			if(title) {
				var start = $.fullCalendar.formatDate(start, "Y-MM-DD HH:mm:ss");
				var end = $.fullCalendar.formatDate(end, "Y-MM-DD HH:mm:ss");
				$.ajax({
					url: SITEURL + "/fullcalendar/create",
					data: 'title=' + title + '&start=' + start + '&end=' + end + '&user=' + user.name,
					type: "POST",
					success: function(data) {
						displayMessage("Added Successfully");
					}
				});
				calendar.fullCalendar('renderEvent', {
					title: title,
					start: start,
					end: end,
					allDay: allDay
				}, true);
			}
			calendar.fullCalendar('unselect');
		},
		eventDrop: function(event, delta) {
      if (event.user == user.name){
        $("tr").each(function() {
          if (event._id == $(this).attr('data-idt'))
          {
            if ($(this).attr('data-edit') ==1)
            {
              alert("Руководитель уже утвердил отпуск! Вы не можете его менять!")
              location.reload()
              // TODO:
            }else{
              var start = $.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm:ss");
              var end = $.fullCalendar.formatDate(event.end, "Y-MM-DD HH:mm:ss");

              $.ajax({
                url: SITEURL + '/fullcalendar/update',
                data: 'title=' + event.title + '&start=' + start + '&end=' + end + '&id=' + event.id,
                type: "POST",
                success: function(response) {
                  $("tr").each(function() {
                    if (event._id == $(this).attr('data-idt'))
                    {

                      if ((event.start['_i'][1]).toString().length == 1){n2 = "0"+event.start['_i'][1]}else{n2 = event.start['_i'][1]}
                      if ((event.start['_i'][2]).toString().length == 1){n3 = "0"+event.start['_i'][2]}else{n3 = event.start['_i'][2]}

                      if ((event.end['_i'][1]).toString().length == 1){e2 = "0"+event.end['_i'][1]}else{e2 = event.end['_i'][1]}
                      if ((event.end['_i'][2]).toString().length == 1){e3 = "0"+event.end['_i'][2]}else{e3 = event.end['_i'][2]}

                      st = event.start['_i'][0]+'-'+n2+'-'+n3
                      end = event.end['_i'][0]+'-'+e2+'-'+e3

                      $(this).find('#startdate').val(st)
                      $(this).find('#enddate').val(end)
                    }

                  });
                  displayMessage("Updated Successfully");
                }
              });
            }
          }
        })

      }else{
        alert("Вы можете менять только свой отпуск!");
        location.reload()
        // TODO:
      }

		},
		eventClick: function(event) {

      if (event.title == user.name){
        var deleteMsg = confirm("Do you really want to delete?");
        if(deleteMsg) {
          $.ajax({
            type: "POST",
            url: SITEURL + '/fullcalendar/delete',
            data: "&id=" + event.id,
            success: function(response) {

              $("tr").each(function() {
                if (event._id == $(this).attr('data-idt'))
                {
                 $(this).remove()
                }
              });


              if(parseInt(response) > 0) {
                $('#calendar').fullCalendar('removeEvents', event.id);
                displayMessage("Deleted Successfully");
              }
            }
          });
        }
      }else{
        alert("Вы можете менять только свой отпуск!");
      }

		}
	});
  $(document).on('click', ".fc-button-group", function(){

    $('#tableMain tbody').html(' ')
  })

  $(document).on('click', "#ytv", function(){
    if (user.Director == "1"){
      var start = $(this).parent().parent().find('#startdate').val() + " " + "00:00:00"
      var end = $(this).parent().parent().find('#enddate').val() + " " + "00:00:00"
      var title = $(this).parent().parent().find('#nameuser').text()
      if ($(this).is(':checked')){
        var ytv = "1"
      } else {
        var ytv = "0"
      }
      $.ajax({
        url: SITEURL + '/fullcalendar/update',
        data: 'title=' + title + '&start=' + start + '&end=' + end + '&id=' + $(this).parent().parent().attr('id')+ '&ytv=' +ytv,
        type: "POST",
        success: function(response) {
          displayMessage("Updated Successfully");
          location.reload()
          // TODO:
        }
    })
    }
  })
  $(document).on('change', "#startdate", function(){
      if (user.name == $(this).parent().parent().find('#nameuser').text()){
      var start = $(this).parent().parent().find('#startdate').val() + " " + "00:00:00"
      var end = $(this).parent().parent().find('#enddate').val() + " " + "00:00:00"
      var title = $(this).parent().parent().find('#nameuser').text()

      var ytv = '0'

      $.ajax({
        url: SITEURL + '/fullcalendar/update',
        data: 'title=' + title + '&start=' + start + '&end=' + end + '&id=' + $(this).parent().parent().attr('id')+ '&ytv=' +ytv,
        type: "POST",
        success: function(response) {
          displayMessage("Updated Successfully");
          location.reload()
          // TODO:
        }
    })
    }else{
      alert("Вы можете менять только свой отпуск!");
    }
  })
  $(document).on('change', "#enddate", function(){
      if (user.name == $(this).parent().parent().find('#nameuser').text()){
      var start = $(this).parent().parent().find('#startdate').val() + " " + "00:00:00"
      var end = $(this).parent().parent().find('#enddate').val() + " " + "00:00:00"
      var title = $(this).parent().parent().find('#nameuser').text()

      var ytv = '0'

      $.ajax({
        url: SITEURL + '/fullcalendar/update',
        data: 'title=' + title + '&start=' + start + '&end=' + end + '&id=' + $(this).parent().parent().attr('id')+ '&ytv=' +ytv,
        type: "POST",
        success: function(response) {
          displayMessage("Updated Successfully");
          location.reload()
          // TODO:
        }
    })
    }else{
      alert("Вы можете менять только свой отпуск!");
    }
  })

});

function displayMessage(message) {
	$(".response").html("<div class='success'>" + message + "</div>");
	setInterval(function() {
		$(".success").fadeOut();
	}, 1000);
}
</script>

@endsection
