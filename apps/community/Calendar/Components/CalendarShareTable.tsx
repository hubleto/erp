import React, { Component, useState } from "react";
import request from "adios/Request";
import {curryRight} from "lodash-es";

interface CalendarShareTableProps {
  configs?: any,
}

interface CalendarShareTableState {
  configs: any,
  loading: boolean,
}

export default class CalendarShareTable extends Component<CalendarShareTableProps, CalendarShareTableState> {

  refCalendar: any;

  constructor(props) {
    super(props);

    this.state = {
      configs: props.configs,
      loading: false,
    };
  }

  componentDidMount() {
  }

  shareCalendar(calendar: any) {
    this.setState({
        loading: true,
        configs: this.state.configs,
      }, () =>
    {
      request.post(
        'calendar/api/share-calendar',
        {
          calendar: calendar,
        }, {}, (data: any) => {
          let backup = this.state.configs;
          data.forEach(calendar => {
            if (calendar.calendar in backup) {
              backup[calendar.calendar].shared = true;
              backup[calendar.calendar].share_key = calendar.share_key;
            }
          });
          this.setState({
            loading: false,
            configs: backup
          });
        });
    });
  }

  stopSharingCalendar(calendar: any) {
    this.setState({
      loading: true,
      configs: this.state.configs,
    },
      () => { request.post(
        'calendar/api/stop-sharing-calendar',
        {
          calendar: calendar,
        }, {}, (data: any) => {
          let backup = Object.fromEntries(
            Object.entries(this.state.configs).map(([key, value]) => [
              key,
              {
                ...value,
                shared: false
              }
            ])
          );

          data.forEach((calendar: { calendar: string; }) => {
            if (calendar.calendar in backup) {
              backup[calendar.calendar].shared = true;
            }
          });

          this.setState({
            configs: backup,
            loading: false,
          })
        });
        }
      );
  }

  renderCalendars(calendars: any) {
    return calendars.map((calendarObject: any[]) => (
      <tr key={calendarObject[0]}>
        <td>{calendarObject[1].title}</td>
        <td className="text-right">
          {this.state.loading ? (
            <div className="flex justify-end">
              <div role="status">
                <svg aria-hidden="true" className="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
                     viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path
                    d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                    fill="currentColor"/>
                  <path
                    d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                    fill="currentFill"/>
                </svg>
                <span className="sr-only">Loading...</span>
              </div>
            </div>
          ) : <>
              { calendarObject[1].shared ? (
                  <>
                    <button onClick={() => navigator.clipboard.writeText(globalThis.app.config.rootUrl + "/calendar/" + calendarObject[1]['share_key'] + "/ics")} className="btn btn-transparent btn-small">
                      <span className="icon"><i className="fas fa-copy"></i></span>
                      <span className="text">Copy url</span>
                    </button>
                    <button onClick={() => this.stopSharingCalendar(calendarObject[0])}
                            className="btn btn-transparent btn-small">
                      <span className="icon"><i className="fas fa-link-slash"></i></span>
                      <span className="text">Stop sharing</span>
                    </button>
                  </>
                ) : (
                  <button onClick={() => this.shareCalendar(calendarObject[0])} className="btn btn-transparent btn-small">
                    <span className="icon"><i className="fas fa-share-nodes"></i></span>
                    <span className="text">Share calendar</span>
                  </button>
                )}</>
            }
        </td>
      </tr>
    ));
  }

  render(): JSX.Element {
    let calendars = this.renderCalendars(Object.entries(this.state.configs));

    return <div className="card w-1/2 m-auto">
      <div className="card-header">
        Share calendar as ICS
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
