import React, { Component, useState } from "react";
import { setUrlParam, deleteUrlParam } from "adios/Helper";
import Calendar from "./Calendar";
import ModalForm from "adios/ModalForm";
import FormActivitySelector from "./FormActivitySelector";
import { log } from "console";


interface CalendarMainProps {
  eventSource?: string,
  eventId?: number,
  children: any,
  eventsEndpoint: string,
  views?: string,
  height?: any,
  readonly?: number,
  onCreateCallback?: any
  onDateClick: any,
  onEventClick: any,
  configs?: any,
}

interface CalendarMainState {
  eventSource?: string,
  eventId?: number,
  events: Array<any>,
  showIdActivity?: number,
  dateClicked?: string,
  timeClicked?: string,
  activityFormComponent?: JSX.Element,
  activityFormModalProps?: any,
  newActivity: boolean,
}

export default class CalendarComponent extends Component<CalendarMainProps, CalendarMainState> {
  constructor(props) {
    super(props);

    this.state = {
      eventSource: this.props.eventSource ?? '',
      eventId: this.props.eventId ?? 0,
      events: [],
      showIdActivity: 0,
      dateClicked: "",
      timeClicked: "",
      newActivity: false,
    };
  }

  renderCell = (eventInfo) => {
    return <>
      <b>{eventInfo.timeText}</b>
      <span style={{marginLeft: 4}}>{eventInfo.event.title}</span>
      <i style={{marginLeft: 4}}>({eventInfo.event.extendedProps.type})</i>
    </>;
  }

  render(): JSX.Element {
    let activityFormModalProps = {
      uid: 'activity_form',
      isOpen: true,
      type: 'right',
      ...this.state.activityFormModalProps
    };

    return (<>
      <Calendar
        readonly={false}
        views={"timeGridDay,timeGridWeek,dayGridMonth,listYear"}
        initialView="timeGridWeek"
        eventsEndpoint={globalThis.main.config.accountUrl + '/calendar/get-calendar-events'}
        onEventsLoaded={(events) => {
          for (let i in events) {
            if (
              !this.state.activityFormComponent
              && events[i].extendedProps?.source == this.state.eventSource
              && events[i].id == this.state.eventId
            ) {

              this.setState({
                eventSource: '',
                eventId: 0,
                activityFormComponent: globalThis.main.renderReactElement(events[i].extendedProps.SOURCEFORM,
                  {
                    id: events[i].id,
                    showInModal: true,
                    showInModalSimple: true,
                    onClose:() => {this.setState({activityFormComponent: null})},
                    onSaveCallback:() => {this.setState({activityFormComponent: null})}
                  }
                )
              });
            }
          }
        }}
        onDateClick={(date, time, info) => {
          deleteUrlParam('eventSource');
          deleteUrlParam('eventId');

          this.setState({
            activityFormComponent: null,
            newActivity: true,
            dateClicked: date,
            timeClicked: info.view.type == "dayGridMonth" ? null : time
          });
        }}
        onEventClick={(info) => {
          setUrlParam('eventSource', info.event.extendedProps.source);
          setUrlParam('eventId', info.event.id);

          this.setState({
            newActivity: false,
            // activityFormModalProps: info.event.extendedProps.SOURCEFORM_MODALPROPS,
            activityFormComponent: globalThis.main.renderReactElement(info.event.extendedProps.SOURCEFORM,
              {
                id: info.event.id,
                showInModal: true,
                showInModalSimple: true,
                onClose:() => {this.setState({activityFormComponent: null})},
                onSaveCallback:() => {this.setState({activityFormComponent: null})}
              }
            )
          });
          info.jsEvent.preventDefault();
        }}
      ></Calendar>
      {this.state.activityFormComponent ?
        <ModalForm {...activityFormModalProps}>
          {this.state.activityFormComponent}
        </ModalForm>
      : <></>}
      {this.state.newActivity ?
        <ModalForm
          uid='activity_new_form'
          isOpen={true}
          type='right'
        >
          <FormActivitySelector
            onCallback={() => this.setState({newActivity: false})}
            calendarConfigs={this.props.configs}
            clickConfig={{
              date: this.state.dateClicked,
              time: this.state.timeClicked
            }}
          />
        </ModalForm>
      : <></>}
    </>
    )
  }
}
