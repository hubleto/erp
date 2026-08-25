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
        defaultValues: {
          date_start: dateClicked,
          time_start: timeClicked ?? moment().format('HH:mm:ss'),
          date_end: dateClicked,
          time_end: moment().add(30, 'minutes').format("HH:mm:ss"),
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
      <div className='app-main-title'>
        <span>{T.translate('Calendars')}</span>
        <button
          className="btn btn-add"
          onClick={() => {
            setShowActivity('calendar,-1');
            setDateClicked(moment().format('YYYY-MM-DD'));
            setTimeClicked(moment().format('HH:mm:ss'));
          }}
        >
          <span className="icon"><i className="fas fa-plus"></i></span>
        </button>

      </div>
      <div className="list">
        {Object.keys(props.calendars).map((calendarName: any) => {
          const calendar = props.calendars[calendarName];

          return <button
            className="btn btn-list-item btn-transparent"
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
        className="btn btn-transparent mt-2"
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
