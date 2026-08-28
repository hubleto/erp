import React, { JSX, useState } from 'react';
import request from "@hubleto/react-ui/core/Request";
import moment from 'moment';
import Calendar from './Calendar';
import Modal from '@hubleto/react-ui/components/fc/Modal';
import Translator from "@hubleto/react-ui/core/Translator";
import Form, { FormMetaContext } from "@hubleto/react-ui/components/fc/Form";
import { useRecordField } from "@hubleto/react-ui/components/fc/FormRecordStore";
import Divider from '@hubleto/react-ui/components/fc/FormComponents/Divider';

export interface CalendarTabProps {
  loadEventsEndpoint: string,
  logActivityEndpoint: string,
  // logActivityEndpointParams: any,
  showIdActivity?: number,
  renderActivityForm: (calendarTab: any) => React.JSX.Element,
  children?: JSX.Element,
}

export const CalendarTabContext = React.createContext<{
  showIdActivity,
  activityTime,
  activityDate,
  activitySubject,
  activityAllDay,
  setShowIdActivity,
}>(null);


const ActivityFormRenderer = (p: { renderer: any, calendarTab: any }): React.JSX.Element => p.renderer(p.calendarTab);

const CalendarTab = (props: CalendarTabProps) => {
  const form = React.useContext(FormMetaContext);

  // const R = useRecord();
  const id = useRecordField('id');
  const isClosed: boolean = useRecordField('is_closed');
  const ACTIVITIES: Array<object> = useRecordField('ACTIVITIES');

  const T = new Translator(
    'Hubleto/ReactUi',
    'Components/Form/CalendarTab'
  );
  
  const [showIdActivity, setShowIdActivity] = useState(props.showIdActivity ?? 0);
  const [activityTime, setActivityTime] = useState('');
  const [activityDate, setActivityDate] = useState('');
  const [activitySubject, setActivitySubject] = useState('');
  const [activityAllDay, setActivityAllDay] = useState(false);

  const refLogActivityInput = React.createRef<HTMLInputElement>();
  const refActivityForm = React.createRef<typeof Form>();

  const logCompletedActivity = (): void => {
    request.get(
      props.logActivityEndpoint,
      {
        // ...props.logActivityEndpointParams,
        activity: refLogActivityInput.current.value,
      },
      (result: any) => {
        form.loadRecord();
        refLogActivityInput.current.value = '';
      }
    );
  }

  const scheduleActivity = (): void => {
    setShowIdActivity(-1);
    setActivityDate(moment().add(1, 'week').format('YYYY-MM-DD'));
    setActivityTime(moment().add(1, 'week').format('H:00:00'));
    setActivitySubject(refLogActivityInput.current.value);
    setActivityAllDay(false);
  }

  const getEventsEndpoint = (): string => {
    const endpoint = globalThis.hubleto.config.projectUrl
      + '/' + props.loadEventsEndpoint;
    //   + '/calendar/api/get-calendar-events'
    //   + '?' + new URLSearchParams({
    //     ...props.leadEventsEndpointParams,
    //     calendar: props.calendarSource,
    //   }).toString()
    // ;
    return endpoint;
  }

  const myself: any = {
    form,
    showIdActivity,
    activityTime,
    activityDate,
    activitySubject,
    activityAllDay,
    renderActivityForm: props.renderActivityForm,
    setShowIdActivity,
  }

  const tmpCalendarLarge = <Calendar
    onCreateCallback={() => form.loadRecord()}
    readonly={isClosed}
    initialView='timeGridWeek'
    views={"timeGridDay,timeGridWeek,dayGridMonth,listYear"}
    eventsEndpoint={getEventsEndpoint()}
    onDateClick={(date: any, time: any, info: any) => {
      setActivityDate(date);
      setActivityTime(time);
      setActivityAllDay(false);
      setShowIdActivity(-1);
    }}
    onEventClick={(info: any) => {
      setShowIdActivity(parseInt(info.event.id));
      info.jsEvent.preventDefault();
    }}
  ></Calendar>;

  return <CalendarTabContext.Provider value={myself}>
    {form.id > 0 ? <div className='flex-dyn'>
      <div className='flex-2 gap-2'>
        <div className='card'>
          <div className='card-header'>Follow-ups</div>
          <div className='card-body'>
            <button onClick={() => {scheduleActivity()}} className="btn btn-add-outline">
              <span className="icon"><i className="fas fa-clock"></i></span>
              <span className="text">{T.translate('Schedule follow-up')}</span>
            </button>
          </div>
        </div>
        <div className='card'>
          <div className='card-header'>What did you do?</div>
          <div className='card-body'>
            <div className="hubleto component input"><div className="input-element w-full gap-2">
              <input
                className="w-full p-1 mb-2"
                placeholder={T.translate('Type recent activity here and press Enter')}
                ref={refLogActivityInput}
                onKeyUp={(event: any) => {
                  if (event.keyCode == 13) {
                    logCompletedActivity();
                  }
                }}
                onChange={(e) => {
                  refLogActivityInput.current.value = e.target.value;
                }}
              />
            </div></div>
            <button onClick={() => {logCompletedActivity()}} className="btn btn-add-outline">
              <span className="icon"><i className="fas fa-check"></i></span>
              <span className="text">{T.translate('Log completed activity')}</span>
            </button>
          </div>
        </div>
        <div className='card'>
          <div className='card-header'>{T.translate('History')}</div>
          <div className='card-body'>
            {ACTIVITIES ? <div className="list">{ACTIVITIES.map((item: any, index: any) => {
              return <>
                <button key={index} className={"btn btn-small btn-transparent btn-list-item " + (item.completed ? "bg-gray-50" : "")}
                  onClick={() => setShowIdActivity(item.id)}
                >
                  <span className="icon">
                    {item.completed
                      ? <div className="text-green-600"><i className='fas fa-check'></i></div>
                      : <div className="text-gray-100"><i className='fas fa-check'></i></div>
                    }
                  </span>
                  <span className="icon">{item.date_start} {item.time_start}<br/>@{item['_LOOKUP[id_owner]']}</span>
                  <span className="text">
                    {item.subject}
                  </span>
                </button>
              </>
            })}</div> : null}
          </div>
        </div>
      </div>
      <div className='flex-3'>
        <Calendar
          onCreateCallback={() => form.loadRecord()}
          readonly={isClosed}
          initialView='dayGridMonth'
          views={"timeGridDay,timeGridWeek,dayGridMonth,listYear"}
          // headerToolbar={{ start: 'title', center: '', end: 'prev,today,next' }}
          eventsEndpoint={getEventsEndpoint()}
          onDateClick={(date: any, time: any, info: any) => {
            setActivityDate(date);
            setActivityTime(time);
            setActivityAllDay(false);
            setShowIdActivity(-1);
          }}
          onEventClick={(info: any) => {
            setShowIdActivity(parseInt(info.event.id));
            info.jsEvent.preventDefault();
          }}
        ></Calendar>
      </div>
    </div> : null}
    {showIdActivity == 0 ? null : <>
      <Modal
        uid='activity_form'
        isOpen={true}
        type='right'
        onClose={() => setShowIdActivity(0)}
      ><ActivityFormRenderer renderer={props.renderActivityForm} calendarTab={myself} /></Modal>
    </>}
  </CalendarTabContext.Provider>;



};

export default CalendarTab;