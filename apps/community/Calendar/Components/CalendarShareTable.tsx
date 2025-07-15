import React, { Component, useState } from "react";
import request from "adios/Request";

interface CalendarShareTableProps {
  configs?: any,
}

interface CalendarShareTableState {}

export default class CalendarShareTable extends Component<CalendarShareTableProps, CalendarShareTableState> {

  refCalendar: any;

  constructor(props) {
    super(props);
  }

  componentDidMount() {
    console.log("what")
  }

  shareCalendar(calendar: any) {
    request.post(
      'calendar/api/share-calendar',
      {
        calendar: calendar,
      }
    );
  }

  renderCalendars(calendars: any) {
    console.log("calendars", calendars);
    return calendars.map(calendarObject => (
      <tr key={calendarObject[0]}>
        <td>{calendarObject[1].title}</td>
        <td className="text-right">
          {calendarObject[1].shared ? (
            <>
              <a href={`deals/${calendarObject[0]}`} className="btn btn-transparent btn-small">
                <span className="icon"><i className="fas fa-copy"></i></span>
                <span className="text">Copy url</span>
              </a>
              <a href={`deals/${calendarObject[0]}`} className="btn btn-transparent btn-small">
                <span className="icon"><i className="fas fa-link-slash"></i></span>
                <span className="text">Stop sharing</span>
              </a>
            </>
          ) : (
            <button onClick={() => this.shareCalendar(calendarObject[0])} className="btn btn-transparent btn-small">
              <span className="icon"><i className="fas fa-share-nodes"></i></span>
              <span className="text">Share calendar</span>
            </button>
          )}
        </td>
      </tr>
    ));
  }

  render(): JSX.Element {
    console.log(this.props.configs)
    let calendars = this.renderCalendars(Object.entries(this.props.configs));

    return <div className="card w-1/2 m-auto">
      <div className="card-header">
        Sharing
      </div>
      <div className="card-body">
        <table className="table-default dense w-full">
          <tbody>
            {calendars}
          </tbody>
        </table>
      </div>
    </div>
      ;
  }
}
