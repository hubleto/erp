import React, { Component, useState } from "react";
import Calendar from "./Calendar";
import ModalSimple from "adios/ModalSimple";
import FormActivitySelector from "./FormActivitySelector";
import { log } from "console";


interface CalendarMainProps {
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
      events: [],
      showIdActivity: 0,
      dateClicked: "",
      timeClicked: "",
      newActivity: false,
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
        eventsEndpoint={globalThis.main.config.rewriteBase + '/calendar/get-calendar-events'}
        onDateClick={(date, time, info) => {
          this.setState({
            activityFormComponent: null,
            newActivity: true,
            dateClicked: date,
            timeClicked: info.view.type == "dayGridMonth" ? null : time
          });
        }}
        onEventClick={(info) => {
          this.setState({
            newActivity: false,
            activityFormModalProps: info.event.extendedProps.SOURCEFORM_MODALPROPS,
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
        <ModalSimple {...activityFormModalProps}>
          {this.state.activityFormComponent}
        </ModalSimple>
      : <></>}
      {this.state.newActivity ?
        <ModalSimple
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
        </ModalSimple>
      : <></>}
    </>
    )
  }
}
