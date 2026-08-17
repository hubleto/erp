import Translator from "@hubleto/react-ui/core/Translator";
import { setUrlParam, deleteUrlParam } from "@hubleto/react-ui/core/Helper";
import React, { useState } from "react";
import moment from 'moment';
import Calendar from "./Calendar";
import Modal from "@hubleto/react-ui/components/fc/Modal";

interface CalendarMainProps {
  showActivity?: string,
  children: any,
  eventsEndpoint: string,
  views?: string,
  initialView?: string,
  height?: any,
  readonly?: number,
  onCreateCallback?: any
  onDateClick: any,
  onEventClick: any,
  calendars?: any,
}

const T = new Translator('HubletoApp\\Community\\Calendar\\Loader', 'Components\\CalendarMain');

const CalendarMain = (props: CalendarMainProps) => {
  const [showActivity, setShowActivity] = useState(props.showActivity ?? '');
  const [dateClicked, setDateClicked] = useState("");
  const [timeClicked, setTimeClicked] = useState("");
  const [calendars, setCalendars] = useState(props.calendars);
  const [fOwnership, setFOwnership] = useState(0);
  const [fCompleted, setFCompleted] = useState(0);

  const getActivitiesEndpointUrl = (showActivity?: string, eventId?: number): string => {
    return (
      'calendar/api/get-calendar-events?'
      + 'fOwnership=' + fOwnership
      + '&fCompleted=' + fCompleted
      + '&' + Object.keys(calendars)
        .filter((calendarName) => calendars[calendarName].show)
        .map((calendarName) => 'calendars[]=' + calendarName)
        .join('&')
      + (eventId ? '&id=' + eventId : '')
    );
  }

  const renderCell = (eventInfo) => {
    return <>
      <b>{eventInfo.timeText}</b>
      <span style={{marginLeft: 4}}>{eventInfo.event.title}</span>
      <i style={{marginLeft: 4}}>({eventInfo.event.extendedProps.type})</i>
    </>;
  }


  let eventForm = null;
  if (showActivity) {

    setUrlParam('showActivity', showActivity);

    const activityCalendar = showActivity.split(',')[0];
    const activityId = showActivity.split(',')[1];
    let calendar = calendars[activityCalendar];

    eventForm = globalThis.hubleto.renderReactElement(calendar.formComponent,
      {
        description: {
          defaultValues: {
            date_start: dateClicked,
            time_start: timeClicked ?? moment().format('HH:mm:ss'),
            date_end: dateClicked,
            time_end: moment().add(30, 'minutes').format("HH:mm:ss"),
          }
        },
        id: activityId,
        onClose: () => setShowActivity(''),
        onSaveCallback: () => setShowActivity(''),
      }
    );
  } else {
    deleteUrlParam('showActivity');
  }

  return <div className="flex gap-2 flex-col md:flex-row">
    <div className="flex flex-col gap-2 text-nowrap">
      <button
        className="btn btn-add-outline mb-2"
        onClick={() => {
          setShowActivity('calendar,-1');
          setDateClicked(moment().format('YYYY-MM-DD'));
          setTimeClicked(moment().format('HH:mm:ss'));
        }}
      >
        <span className="icon"><i className="fas fa-plus"></i></span>
        <span className="text">{T.translate("New activity")}</span>
      </button>

      <b>{T.translate('Calendars')}</b>
      <div className="list">
        {Object.keys(props.calendars).map((calendarName: any) => {
          const calendar = props.calendars[calendarName];

          return <button
            className="btn btn-small btn-list-item btn-transparent"
            style={{"borderLeft": "1em solid " + calendar.color}}
            onClick={() => {
              let newCalendars = calendars;
              calendars[calendarName].show = !calendars[calendarName].show;
              setCalendars({...newCalendars});
            }}
          >
            <span className="icon"><input type="checkbox" checked={calendars[calendarName].show}></input></span>
            <span className="text">{calendar.title}</span>
            {calendar.missedActivities > 0 ?
              <div className="badge badge-small badge-red ml-auto mr-1">{calendar.missedActivities}</div>
            : null}
          </button>;
        })}
      </div>

      <div className="list">
        <button
          className={"btn btn-small btn-list-item " + (fOwnership == 0 ? "btn-primary" : "btn-transparent")}
          onClick={() => { setFOwnership(0); }}
        ><span className="text">{T.translate("All")}</span></button>
        <button
          className={"btn btn-small btn-list-item " + (fOwnership == 1 ? "btn-primary" : "btn-transparent")}
          onClick={() => { setFOwnership(1); }}
        ><span className="text">{T.translate("My")}</span></button>
      </div>

      <div className="list">
        <button
          className={"btn btn-small btn-list-item " + (fCompleted == 0 ? "btn-primary" : "btn-transparent")}
          onClick={() => { setFCompleted(0); }}
        ><span className="text">{T.translate("All")}</span></button>
        <button
          className={"btn btn-small btn-list-item " + (fCompleted == 1 ? "btn-primary" : "btn-transparent")}
          onClick={() => { setFCompleted(1); }}
        ><span className="text">{T.translate("Open")}</span></button>
        <button
          className={"btn btn-small btn-list-item " + (fCompleted == 2 ? "btn-primary" : "btn-transparent")}
          onClick={() => { setFCompleted(2); }}
        ><span className="text">{T.translate("Completed")}</span></button>
      </div>

      <a
        className="btn btn-primary-outline mt-2"
        href={globalThis.hubleto.config.projectUrl + "/calendar/share"}
      >
        <span className="icon"><i className="fa-solid fa-share-nodes"></i></span>
        <span className="text">{T.translate("Share calendar")}</span>
      </a>
    </div>
    <div className="w-full">
      <Calendar
        readonly={false}
        views={"timeGridDay,timeGridWeek,dayGridMonth,listYear"}
        height={props.height}
        initialView={props.initialView ?? "timeGridWeek"}
        eventsEndpoint={globalThis.hubleto.config.projectUrl + '/' + getActivitiesEndpointUrl(props.showActivity)}
        onEventsLoaded={(events) => {
        }}
        onDateClick={(date, time, info) => {
          setShowActivity('calendar,-1');
          setDateClicked(date);
          setTimeClicked(info.view.type == "dayGridMonth" ? null : time);
        }}
        onEventClick={(info) => {
          if (info.event.url) {
            globalThis.window.open(globalThis.hubleto.config.projectUrl + '/' + info.event.url);
          } else {
            setShowActivity(info.event.extendedProps.source + ',' + info.event.id);
          }

          info.jsEvent.preventDefault();
        }}
      ></Calendar>
    </div>
    {eventForm ? <Modal
      uid='event_modal'
      isOpen={true}
      type='right'
    >{eventForm}</Modal> : null}
  </div>;
}

export default CalendarMain;

// import React, { Component, useState } from "react";
// import { setUrlParam, deleteUrlParam } from "@hubleto/react-ui/core/Helper";
// import Calendar from "./Calendar";
// import ModalForm from "@hubleto/react-ui/components/cc/ModalForm";
// import FormActivitySelector from "./FormActivitySelector";
// import request from "@hubleto/react-ui/core/Request";
// import moment from 'moment';
// import TranslatedComponent from "@hubleto/react-ui/components/cc/TranslatedComponent";
// import FormActivity from "./FormActivity";


// interface CalendarMainProps {
//   showActivity?: string,
//   children: any,
//   eventsEndpoint: string,
//   views?: string,
//   initialView?: string,
//   height?: any,
//   readonly?: number,
//   onCreateCallback?: any
//   onDateClick: any,
//   onEventClick: any,
//   calendars?: any,
// }

// interface CalendarMainState {
//   showActivity?: string,
//   events: Array<any>,
//   showIdActivity?: number,
//   dateClicked?: string,
//   timeClicked?: string,
//   activityFormComponent?: React.JSX.Element,
//   activityFormModalProps?: any,
//   newActivity: boolean,
//   calendars: any,
//   fOwnership: number,
//   fCompleted: number,
// }

// export default class CalendarComponent extends TranslatedComponent<CalendarMainProps, CalendarMainState> {

//   props: CalendarMainProps;
//   state: CalendarMainState;

//   translationContext: string = 'Hubleto\\App\\Community\\Calendar\\Loader';
//   translationContextInner: string = 'Components\\CalendarMain';

//   refCalendar: any;
//   refActivityModal: any;
//   refActivityForm: any;
//   // refActivityModal: any;
//   // refActivityForm: any;

//   constructor(props) {
//     super();

//     this.props = props;

//     this.refCalendar = React.createRef();
//     this.refActivityModal = React.createRef();
//     this.refActivityForm = React.createRef();
//     // this.refActivityModal = React.createRef();
//     // this.refActivityForm = React.createRef();

//     this.state = {
//       showActivity: props.showActivity ?? '',
//       events: [],
//       showIdActivity: 0,
//       dateClicked: "",
//       timeClicked: "",
//       newActivity: false,
//       calendars: props.calendars,
//       fOwnership: 0,
//       fCompleted: 0,
//     };
//   }

//   getActivitiesEndpointUrl(showActivity?: string, eventId?: number): string {
//     return (
//       'calendar/api/get-calendar-events?'
//       + 'fOwnership=' + this.state.fOwnership
//       + '&fCompleted=' + this.state.fCompleted
//       + '&' + Object.keys(this.state.calendars)
//         .filter((calendarName) => this.state.calendars[calendarName].show)
//         .map((calendarName) => 'calendars[]=' + calendarName)
//         .join('&')
//       + (eventId ? '&id=' + eventId : '')
//     );
//   }

//   renderCell = (eventInfo) => {
//     return <>
//       <b>{eventInfo.timeText}</b>
//       <span style={{marginLeft: 4}}>{eventInfo.event.title}</span>
//       <i style={{marginLeft: 4}}>({eventInfo.event.extendedProps.type})</i>
//     </>;
//   }

//   render(): React.JSX.Element {
//     let eventForm = null;
//     if (this.state.showActivity) {

//       setUrlParam('showActivity', this.state.showActivity);

//       const activityCalendar = this.state.showActivity.split(',')[0];
//       const activityId = this.state.showActivity.split(',')[1];
//       let calendar = this.state.calendars[activityCalendar];

//       eventForm = globalThis.hubleto.renderReactElement(calendar.formComponent,
//         {
//           ref: this.refActivityForm,
//           description: {
//             defaultValues: {
//               date_start: this.state.dateClicked,
//               time_start: this.state.timeClicked ?? moment().format('HH:mm:ss'),
//               date_end: this.state.dateClicked,
//               time_end: moment().add(30, 'minutes').format("HH:mm:ss"),
//             }
//           },
//           id: activityId,
//           modal: this.refActivityModal,
//           onClose:() => {this.setState({showActivity: ''})},
//           onSaveCallback:() => {this.setState({showActivity: ''})}
//         }
//       );
//     } else {
//       deleteUrlParam('showActivity');
//     }

//     return <div className="flex gap-2 flex-col md:flex-row">
//       <div className="flex flex-col gap-2 text-nowrap">
//         <button
//           className="btn btn-add-outline mb-2"
//           onClick={() => {
//             this.setState({
//               showActivity: 'calendar,-1',
//               dateClicked: moment().format('YYYY-MM-DD'),
//               timeClicked: moment().format('HH:mm:ss'),
//             });
//           }}
//         >
//           <span className="icon"><i className="fas fa-plus"></i></span>
//           <span className="text">{T.translate("New activity")}</span>
//         </button>

//         <b>{T.translate('Calendars')}</b>
//         <div className="list">
//           {Object.keys(this.props.calendars).map((calendarName: any) => {
//             const calendar = this.props.calendars[calendarName];

//             return <button
//               className="btn btn-small btn-list-item btn-transparent"
//               style={{"borderLeft": "1em solid " + calendar.color}}
//               onClick={() => {
//                 let calendars = this.state.calendars;
//                 calendars[calendarName].show = !calendars[calendarName].show;
//                 this.setState({calendars: calendars});
//               }}
//             >
//               <span className="icon"><input type="checkbox" checked={this.state.calendars[calendarName].show}></input></span>
//               <span className="text">{calendar.title}</span>
//               {calendar.missedActivities > 0 ?
//                 <div className="badge badge-small badge-red ml-auto mr-1">{calendar.missedActivities}</div>
//               : null}
//             </button>;
//           })}
//         </div>

//         <div className="list">
//           <button
//             className={"btn btn-small btn-list-item " + (this.state.fOwnership == 0 ? "btn-primary" : "btn-transparent")}
//             onClick={() => { this.setState({fOwnership: 0}); }}
//           ><span className="text">{T.translate("All")}</span></button>
//           <button
//             className={"btn btn-small btn-list-item " + (this.state.fOwnership == 1 ? "btn-primary" : "btn-transparent")}
//             onClick={() => { this.setState({fOwnership: 1}); }}
//           ><span className="text">{T.translate("My")}</span></button>
//         </div>

//         <div className="list">
//           <button
//             className={"btn btn-small btn-list-item " + (this.state.fCompleted == 0 ? "btn-primary" : "btn-transparent")}
//             onClick={() => { this.setState({fCompleted: 0}); }}
//           ><span className="text">{T.translate("All")}</span></button>
//           <button
//             className={"btn btn-small btn-list-item " + (this.state.fCompleted == 1 ? "btn-primary" : "btn-transparent")}
//             onClick={() => { this.setState({fCompleted: 1}); }}
//           ><span className="text">{T.translate("Open")}</span></button>
//           <button
//             className={"btn btn-small btn-list-item " + (this.state.fCompleted == 2 ? "btn-primary" : "btn-transparent")}
//             onClick={() => { this.setState({fCompleted: 2}); }}
//           ><span className="text">{T.translate("Completed")}</span></button>
//         </div>

//         <a
//           className="btn btn-primary-outline mt-2"
//           href={globalThis.hubleto.config.projectUrl + "/calendar/share"}
//         >
//           <span className="icon"><i className="fa-solid fa-share-nodes"></i></span>
//           <span className="text">{T.translate("Share calendar")}</span>
//         </a>
//       </div>
//       <div className="w-full">
//         <Calendar
//           ref={this.refCalendar}
//           readonly={false}
//           views={"timeGridDay,timeGridWeek,dayGridMonth,listYear"}
//           height={this.props.height}
//           initialView={this.props.initialView ?? "timeGridWeek"}
//           eventsEndpoint={globalThis.hubleto.config.projectUrl + '/' + this.getActivitiesEndpointUrl(this.props.showActivity)}
//           onEventsLoaded={(events) => {
//           }}
//           onDateClick={(date, time, info) => {
//             this.setState({
//               showActivity: 'calendar,-1',
//               dateClicked: date,
//               timeClicked: info.view.type == "dayGridMonth" ? null : time
//             });
//           }}
//           onEventClick={(info) => {
//             if (info.event.url) {
//               globalThis.window.open(globalThis.hubleto.config.projectUrl + '/' + info.event.url);
//             } else {
//               this.setState({
//                 showActivity: info.event.extendedProps.source + ',' + info.event.id,
//               });
//             }

//             info.jsEvent.preventDefault();
//           }}
//         ></Calendar>
//       </div>
//       {eventForm ? <ModalForm
//         ref={this.refActivityModal}
//         form={this.refActivityForm}
//         uid='event_modal'
//         isOpen={true}
//         type='right'
//         onClose={() => this.setState({showActivity: ''})}
//       >{eventForm}</ModalForm> : null}
//     </div>;
//   }
// }
