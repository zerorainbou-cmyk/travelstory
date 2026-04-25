(function($){
    "use strict";
	
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/ova_events_simple_calendar.default', function() {
			let calendars = {};

            $('.ovaev_simple_calendar').each( function(e) {
                const daysOfTheWeek = $(this).data('days-of-the-week');
                let eventsAttr = $(this).attr('events');
                let filteredEvents = [];

                if ( eventsAttr && eventsAttr.length > 0 ) {
                    try {
                        let rawEvents = JSON.parse( eventsAttr );
                        let seenDates = {};

                        // [핵심 필터링] 날짜별 중복 제거 로직
                        rawEvents.forEach(function(event) {
                            // 이벤트에서 날짜 정보를 추출 (여러 포맷 대응)
                            let eventDate = event.date || event.startDate || event.singleDay || "";
                            
                            if (eventDate !== "") {
                                if (!seenDates[eventDate]) {
                                    // 해당 날짜에 처음 나타난 데이터만 가격(title)을 살려둠
                                    seenDates[eventDate] = true;
                                    filteredEvents.push(event);
                                } else {
                                    // 이미 가격이 표시된 날짜의 다음 일정들은 제목을 강제로 비움
                                    let hiddenEvent = Object.assign({}, event);
                                    hiddenEvent.title = "";
                                    hiddenEvent.name = ""; // 혹시 name 필드를 쓴다면 이것도 비움
                                    filteredEvents.push(hiddenEvent);
                                }
                            } else {
                                // 날짜 정보가 없는 예외 데이터는 그대로 추가
                                filteredEvents.push(event);
                            }
                        });
                    } catch (err) {
                        console.error("Calendar data error:", err);
                    }
                }

                // 수정된 filteredEvents를 clndr에 전달
                calendars.clndr1 = $(this).find('.ovaev_events_simple_calendar').clndr({
                    events: filteredEvents,
                    daysOfTheWeek: daysOfTheWeek,
                    clickEvents: {
                        click: function (target) {
                            if (target.events.length > 0 && target.events[0].url) {
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