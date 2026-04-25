(function($){
	"use strict";

	if ( typeof order_time != 'undefined' ) {
	 	document.addEventListener('DOMContentLoaded', function() {
			$('.ovabrw__product_calendar').each( function(e) {

			    var id = $(this).data('id');

			    var srcCalendarEl = document.getElementById(id);
			    if ( srcCalendarEl === null ) return;

			    var nav = srcCalendarEl.getAttribute('data-nav');
			    var default_view = srcCalendarEl.getAttribute('data-default_view');
			    var cal_lang = srcCalendarEl.getAttribute( 'data-lang' ).replace(/\s/g, '');
			    var data_event_number = parseInt( srcCalendarEl.getAttribute('data_event_number') );
			    //var events = order_time;
//추가
// [최종] 패키지 투어 출발일에만 가격 표시 (중복 제거 로직)
var events = [];
var seenDates = {}; // 이미 가격을 표시한 날짜를 기억하는 저장소

if (typeof order_time !== 'undefined' && Array.isArray(order_time)) {
    order_time.forEach(function(event) {
        // 날짜 부분만 추출 (예: 2026-07-31)
        var dateStr = event.start.split('T')[0];
        
        // 해당 날짜에 처음 나타난 데이터이고, 제목에 가격 정보가 있다면 표시
        if (!seenDates[dateStr]) {
            events.push(event);
            // 만약 제목에 가격이 포함되어 있다면 해당 날짜는 '처리 완료'로 표시
            if (event.title && event.title.indexOf('원') !== -1) {
                seenDates[dateStr] = true;
            }
        } else {
            // 이미 가격이 찍힌 날짜에 중복으로 들어온 '여행 기간' 데이터는 제목을 비워서 추가
            var hiddenEvent = JSON.parse(JSON.stringify(event));
            hiddenEvent.title = ""; 
            events.push(hiddenEvent);
        }
    });
} else {
    events = typeof order_time !== 'undefined' ? order_time : [];
}
//추가끝
			    
			    var srcCalendar = new FullCalendar.Calendar(srcCalendarEl, {
			        editable: true,
			        events: events,
			        eventDisplay: 'block',
			        height: '100%',
			        headerToolbar: {
			            left: 'prev,next,today,' + nav,
			            right: 'title',
			        },
			        initialView: default_view,
			        locale: cal_lang,
			        firstDay: 1,
			        dayMaxEventRows: true, // for all non-TimeGrid views
		          	views: {
			           	dayGrid: {
			                dayMaxEventRows: data_event_number
			              // options apply to dayGridMonth, dayGridWeek, and dayGridDay views
			            },
			            timeGrid: {
			                dayMaxEventRows: data_event_number
			              // options apply to timeGridWeek and timeGridDay views
			            },
			            week: {
			                dayMaxEventRows: data_event_number
			              // options apply to dayGridWeek and timeGridWeek views
			            },
			            day: {
			                dayMaxEventRows: data_event_number
			              // options apply to dayGridDay and timeGridDay views
			            }
			        },
			    });

			    srcCalendar.render();
			});
		}); 
	}
}) (jQuery);