import React, { Component, useState } from "react";
import Calendar from "./Calendar";

interface CalendarMainProps {
  children: any,
  eventsEndpoint: string,
  views?: string,
  height?: any,
  readonly?: number,
  onCreateCallback?: any
  onDateClick: any,
  onEventClick: any,
}

interface CalendarMainState {
  events: Array<any>,
  showIdActivity?: number,
  dateClicked?: string,
  timeClicked?: string,
}

export default class CalendarComponent extends Component<CalendarMainProps, CalendarMainState> {
  constructor(props) {
    super(props);

    this.state = {
      events: [],
      showIdActivity: 0,
      dateClicked: "",
      timeClicked: "",
    };
  }

  renderCell = (eventInfo) => {
    return (
      <>
        <b>{eventInfo.timeText}</b><span style={{marginLeft: 4}}>{eventInfo.event.title}</span><i style={{marginLeft: 4}}>({eventInfo.event.extendedProps.type})</i>
      </>
    )
  }

  render(): JSX.Element {
    return (
      <Calendar
        readonly={false}
        views={"timeGridDay,timeGridWeek,dayGridMonth,listYear"}
        eventsEndpoint={globalThis.app.config.rewriteBase + '/calendar/get-calendar-events'}
        onDateClick={(date, time, info) => {
          alert('Zobrazit formular na vyber a) customera, b) leadu alebo c) dealu. Ak si jedno z toho zvoli, otvori sa prislusny form. Ak si nezvoli nic, otvori sa form, ktory bude ukladat do noveho modelu Calendar/Models/Activity');
        }}
        onEventClick={(info) => {
          console.log('event click', info);
        }}
      ></Calendar>
    )
  }
}
