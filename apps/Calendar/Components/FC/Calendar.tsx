import React, { useState } from "react";
import FullCalendar from '@fullcalendar/react';
import themePlugin from "@fullcalendar/react/themes/forma";
import dayGridPlugin from '@fullcalendar/react/daygrid';
import timeGridPlugin from '@fullcalendar/react/timegrid';
import interactionPlugin from '@fullcalendar/react/interaction';
import listPlugin from '@fullcalendar/react/list';
import request from "@hubleto/react-ui/core/Request";

//@ts-ignore
import '@fullcalendar/react/skeleton.css'; // ALWAYS NEED SKELETON
//@ts-ignore
import '@fullcalendar/react/themes/forma/theme.css'; // YOUR THEME
//@ts-ignore
import '@fullcalendar/react/themes/forma/palettes/blue.css'; // YOUR THEME'S PALETTE

const currentLang = globalThis.hubleto.config.language;

interface CalendarProps {
  eventsEndpoint: string,
  views?: string,
  initialView: string,
  height?: any,
  readonly?: boolean,
  onCreateCallback?: any
  onEventsLoaded?: any,
  onDateClick: any,
  onEventClick: any,
  headerToolbar?: any,
}

const Calendar = (props: CalendarProps) => {

  const headerToolbar = props.headerToolbar ?? {
    left: 'prev,next today',
    center: 'title',
    right: props.views ?? 'timeGridDay,timeGridWeek,dayGridMonth'
  };

  const renderCell = (eventInfo) => {
    let color = "";

    //choose the correct color based on the current view and the calendar color config
    if (eventInfo.view.type == "dayGridMonth" && eventInfo.event.allDay) {
      color = ""
    } else if (eventInfo.view.type == "dayGridMonth" || eventInfo.view.type == "listYear") {
      color = eventInfo.event.backgroundColor;
    }

    const cellContent = <>
      {eventInfo.event.extendedProps.completed ? <i className='fas fa-check ml-1'></i> : null}
      <b className="ml-2">{eventInfo.timeText}</b>
      <span className="ml-2">{eventInfo.event.title}</span>
      {eventInfo.event.extendedProps.details ?
        <div className="ml-2"><small>
          <i>{eventInfo.event.extendedProps.details}</i>
        </small></div>
      : null}
    </>;

    return cellContent;
  }

  return <div>
    <FullCalendar
      // eventClassNames={"truncate cursor-pointer"}
      height={props.height}
      plugins={[themePlugin, dayGridPlugin, timeGridPlugin, interactionPlugin, listPlugin]}
      firstDay={1}
      scrollTime='10:30:00'
      headerToolbar={headerToolbar}
      initialView={props.initialView}
      eventTimeFormat={{
        hour: '2-digit',
        minute: '2-digit',
        meridiem: false,
        hour12: false,
      }}
      editable={false}
      selectable={false}
      selectMirror={false}
      dayMaxEvents={true}
      weekends={true}
      events={{url: props.eventsEndpoint}}
      eventsSet={(events) => {
        if (props.onEventsLoaded) props.onEventsLoaded(events);
      }}
      //initialEvents={this.state.events} // alternatively, use the `events` setting to fetch from a feed
      //select={handleDateSelect}
      viewDidMount={(data: any) => {
        request.post('calendar/api/set-initial-view', {initialView: data.view.type});
      }}
      dateClick={(info) => {
        if (props.readonly) return;

        const year = info.date.getFullYear();
        const month = String(info.date.getMonth() + 1).padStart(2, '0');
        const day = String(info.date.getDate()).padStart(2, '0');

        const hours = String(info.date.getHours()).padStart(2, '0');
        const minutes = String(info.date.getMinutes()).padStart(2, '0');
        const seconds = String(info.date.getSeconds()).padStart(2, '0');

        const date = `${year}-${month}-${day}`;
        const time = `${hours}:${minutes}:${seconds}`;

        props.onDateClick(date, time, info);
      }}
      eventContent={renderCell} // custom render function
      eventClick={(info) => props.onEventClick(info)}
    />
  </div>;
}

export default Calendar;