(function($){
    "use strict";
	
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/ova_events_simple_calendar.default', function() {
			let calendars = {};

            $('.ovaev_simple_calendar').each( function(e) {
                const thisMonth 	= moment().format('YYYY-MM');
                const daysOfTheWeek = $(this).data('days-of-the-week');
                let events 			= $(this).attr('events');
                
                if ( events && events.length > 0 ) {
                    let rawEvents = JSON.parse( events );
                    
                    // [핵심 로직] 각 날짜별로 '가장 처음 등록된 데이터'만 가격을 남김
                    let seenDates = {};
                    events = rawEvents.map(function(event) {
                        // date, startDate, singleDay 중 있는 값을 날짜 기준으로 삼음
                        let eventDate = event.date || event.startDate || event.singleDay;
                        
                        if (eventDate && !seenDates[eventDate]) {
                            // 해당 날짜에 처음 등장한 데이터는 그대로 둠 (가격 노출)
                            seenDates[eventDate] = true;
                            return event;
                        } else {
                            // 이미 가격이 표시된 날짜의 중복 데이터는 제목(title)을 비움
                            let hiddenEvent = Object.assign({}, event);
                            hiddenEvent.title = ""; 
                            hiddenEvent.name = ""; // 이름도 있다면 함께 비움
                            return hiddenEvent;
                        }
                    });
                }

                // 수정된 events 데이터를 가지고 달력을 렌더링
                calendars.clndr1 = $(this).find('.ovaev_events_simple_calendar').clndr({
                    events: events,
                    daysOfTheWeek: daysOfTheWeek,
                    clickEvents: {
                        click: function (target) {
                            if (target.events.length > 0) {
                                location.assign(target.events[0].url);
                            }
                        }
                    },
                  	multiDayEvents: {
                      	singleDay: 'date',
                      	endDate: 'endDate',
                      	startDate: 'startDate'
                  	},
                  	showAdjacentMonths: true,
                  	adjacentDaysChangeMonth: false
              	});

               	$(document).keydown( function(e) {
                  	if (e.keyCode == 37) { calendars.clndr1.back(); }
                  	if (e.keyCode == 39) { calendars.clndr1.forward(); }
              	});
            });
		});
	});

})(jQuery);