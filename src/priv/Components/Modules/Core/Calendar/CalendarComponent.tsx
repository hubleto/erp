import React, { Component, useState } from "react";
import { Badge, Calendar, Popover, Whisper } from "rsuite";
import FormActivity, { FormActivityProps, FormActivityState } from "../Customers/FormActivity";
import "rsuite/Calendar/styles/index.css";
import 'rsuite/Badge/styles/index.css';
import 'rsuite/Popover/styles/index.css';
import ModalSimple from "adios/ModalSimple";

interface CalendarProps {
}

interface CalendarState {
  events: Array<any>,
  loading: boolean,
  showIdActivity: number,
  newFormDate?: string,
}

export default class CalendarComponent extends Component<CalendarProps, CalendarState> {
  constructor(props) {
    super(props);

    this.state = {
      events: [],
      loading: true,
      showIdActivity: 0,
      newFormDate: null
    };
  }

  componentDidMount() {
    this.fetchCalendarEvents();
  }

  fetchCalendarEvents = () => {
    fetch("customers/activities/get")
      .then((response) => response.json())
      .then((data) => {
        this.setState({ events: data, loading: false });
      })
      .catch((error) => {
        console.error("Error fetching calendar events:", error);
        this.setState({ loading: false });
      });
  };

  renderCell = (date) => {
    const { events } = this.state;

    const eventsForDate = events.filter(
      (event) => new Date(event.date).toDateString() === date.toDateString()
    );
    const displayList = eventsForDate.filter((item, index) => index < 2);

    if (eventsForDate.length > 0) {
      return (
        <ul className="calendar-todo-list">
          {displayList.map((activity, index) => (
            <li
              className="text-xs text-left px-1 my-1 overflow-hidden text-ellipsis whitespace-nowrap w-full border border-purple-400 rounded hover:bg-purple-200"
              key={index}
              onClick={() => {this.setState({showIdActivity: activity.id})}}>
                <span><b>{activity.time}</b> - {activity.title}</span>
            </li>
          ))}
          <li className="flex flex-row justify-between">
            {eventsForDate.length > 2 ?
              <Whisper
                placement="top"
                trigger="click"
                speaker={
                  <Popover>
                    {eventsForDate.map((activity, index) => (
                      <p key={index} className="hover:underline" onClick={() => {this.setState({showIdActivity: activity.id})}}>
                        <b>{activity.time}</b> - {activity.title}
                      </p>
                    ))}
                  </Popover>
                }
              >
                <a className="text-xs">+ {eventsForDate.length} more</a>
              </Whisper>
            : null}
            <button
              className="btn btn-primary text-xs p-1  h-[18px] w-[18px]"
              onClick={() => {
                let year = date.getFullYear();
                let month = (date.getMonth() + 1).toString().padStart(2, '0');
                let day = date.getDate().toString().padStart(2, '0');
                var newDateString = `${year}-${month}-${day}`;

                this.setState({newFormDate: newDateString});
              }}
            ><i className="fas fa-plus w-full h-full"></i></button>
          </li>
        </ul>
      );
    }
    return null;
  };

  render(): JSX.Element {
    const { loading } = this.state;

    if (loading) {
      return <div>Loading Calendar...</div>;
    }

    const html =
    <div>
      <Calendar
        bordered
        renderCell={this.renderCell}
        cellClassName={date => (date.getDay() % 2 ? 'bg-gray-100' : undefined)}
      />;

      {this.state.showIdActivity <= 0 ? null :
        <ModalSimple
          uid='activity_form'
          isOpen={true}
          type='right'
        >
          <FormActivity
            id={this.state.showIdActivity}
            showInModal={true}
            showInModalSimple={true}
            onClose={() => { this.setState({showIdActivity: 0}); }}
            onSaveCallback={(form: FormActivity<FormActivityProps, FormActivityState>, saveResponse: any) => {
              let newEvents = this.state.events;
              for (let i in newEvents) {
                if (newEvents[i].id == this.state.showIdActivity) {
                  newEvents[i] = saveResponse.savedRecord;
                }
              }
              this.setState({events: newEvents});
            }}
          ></FormActivity>
        </ModalSimple>
      }
      {this.state.newFormDate == null ? null :
        <ModalSimple
          uid='activity_form'
          isOpen={true}
          type='right'
        >
          <FormActivity
            id={-1}
            descriptionSource="both"
            description={{
              defaultValues: {due_date: this.state.newFormDate}
            }}
            showInModal={true}
            showInModalSimple={true}
            onClose={() => { this.setState({newFormDate: null}); }}
            //onSaveCallback={() => { this.setState({}) }}
          ></FormActivity>
        </ModalSimple>
      }
    </div>

    return html;

  }
}
