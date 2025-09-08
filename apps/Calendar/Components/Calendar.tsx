import React, { Component } from "react";
import FullCalendar from '@fullcalendar/react'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import listPlugin from '@fullcalendar/list';

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

interface CalendarState {
  events: Array<any>,
  newFormDateTime?: string,
  dateClicked?: string,
  timeClicked?: string,
  headerToolbar: any,
}

export default class Calendar extends Component {
  props: CalendarProps;
  state: CalendarState;

  constructor(props) {
    super(props);

    this.state = {
      events: [],
      dateClicked: "",
      timeClicked: "",
      headerToolbar: this.props.headerToolbar ?? {
        left: 'prev,next today',
        center: 'title',
        right: this.props.views ?? 'timeGridDay,timeGridWeek,dayGridMonth'
      },
    };
  }

  renderCell = (eventInfo) => {
    return <>
      {eventInfo.event.extendedProps.icon ? <i className={"ml-2 " + eventInfo.event.extendedProps.icon}></i> : null}
      <b className="ml-2">{eventInfo.timeText}</b>
      <span className="ml-2">{eventInfo.event.title}</span>
      {eventInfo.event.extendedProps.details ? 
        <div className="ml-2"><small>
          <i>{eventInfo.event.extendedProps.details}</i>
        </small></div>
      : null}
    </>
  }

  render(): JSX.Element {
    return (
      <div>
        <FullCalendar
          eventClassNames={"truncate cursor-pointer"}
          height={this.props.height}
          plugins={[dayGridPlugin, timeGridPlugin, interactionPlugin, listPlugin]}
          firstDay={1}
          scrollTime='10:30:00'
          headerToolbar={this.state.headerToolbar}
          initialView={this.props.initialView}
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
          events={{url: this.props.eventsEndpoint}}
          eventsSet={(events) => {
            if (this.props.onEventsLoaded) this.props.onEventsLoaded(events);
          }}
          //initialEvents={this.state.events} // alternatively, use the `events` setting to fetch from a feed
          //select={handleDateSelect}
          dateClick={(info) => {
            if (this.props.readonly) return;

            const year = info.date.getFullYear();
            const month = String(info.date.getMonth() + 1).padStart(2, '0');
            const day = String(info.date.getDate()).padStart(2, '0');

            const hours = String(info.date.getHours()).padStart(2, '0');
            const minutes = String(info.date.getMinutes()).padStart(2, '0');
            const seconds = String(info.date.getSeconds()).padStart(2, '0');

            const date = `${year}-${month}-${day}`;
            const time = `${hours}:${minutes}:${seconds}`;

            this.props.onDateClick(date, time, info);
          }}
          eventContent={this.renderCell} // custom render function
          eventClick={(info) => this.props.onEventClick(info)}
        />
      </div>
    )
  }
}
