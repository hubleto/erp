import React, { Component } from 'react';
// import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/components/cc/FormExtended';
import moment from 'moment';
import Translator from "@hubleto/react-ui/core/Translator";
import Form from '@hubleto/react-ui/components/fc/Form';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import Divider from '@hubleto/react-ui/components/fc/FormComponents/Divider';
import { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import { useRecordField, FormRecordStoreContext } from '@hubleto/react-ui/components/fc/FormRecordStore';

export interface FormActivityProps extends FormProps {
  calendarTab: any,
  customInputFields: Array<string>,
  defaultValues: any,
  onClose?: () => void,
  onAfterSaveRecord?: () => void,
}

interface Recurrence {
  period: '' | 'day' | 'week' | 'month' | 'year',
  periodEvery: number, // e.g. repeat every 3 weeks
  periodCount: number, // ends after 'periodCount' occurences
  dates: Array<string>,
}

const expandRecurrenceDates = (recurrence: Recurrence): Array<string> => {
  let dates = [];
  let date = moment();
  let periodCount = recurrence.periodCount;

  for (let i = 0; i < periodCount; i++) {
    dates.push(date.format('YYYY-MM-DD'));
    //@ts-ignore
    date = date.add(recurrence.periodEvery, recurrence.period);
  }

  return dates;

}

const T = new Translator('HubletoApp\\Community\\Calendar\\Loader', 'Components\\FormActivity');

const Content = (props: FormActivityProps): React.JSX.Element => {
  const R = React.useContext(FormRecordStoreContext).getRecord();
  const form = React.useContext(FormMetaContext);

  const recurrence: string = useRecordField('recurrence', '');
  const allDay: number = useRecordField('all_day', 0);
  const dateStart: string = useRecordField('date_start', '');
  const dateEnd: string = useRecordField('date_end', '');
  const timeStart: string = useRecordField('time_start', '');
  const timeEnd: string = useRecordField('time_end', '');

  let recurrenceParsed: Recurrence = {
    period: '',
    periodEvery: 1,
    periodCount: 0,
    dates: [],
  };

  if (recurrence != '') {
    try {
      recurrenceParsed = JSON.parse(recurrence);
      recurrenceParsed.dates = expandRecurrenceDates(recurrenceParsed);
    } catch (ex) {
      recurrenceParsed = {
        period: '',
        periodEvery: 1,
        periodCount: 0,
        dates: [],
      };
      recurrenceParsed.dates = expandRecurrenceDates(recurrenceParsed);
    }
  }

  let daysDuration = moment(dateEnd).diff(moment(dateStart), 'days');
  let hoursDuration = moment(dateEnd + ' ' + timeEnd).diff(moment(dateEnd + ' ' + timeStart), 'hours');
  let minutesDuration = moment(dateEnd + ' ' + timeEnd).diff(moment(dateEnd + ' ' + timeStart), 'minutes') - ((hoursDuration ?? 0) * 60);

  if (isNaN(hoursDuration)) hoursDuration = 0;
  if (isNaN(daysDuration)) daysDuration = 0;
  if (isNaN(minutesDuration)) minutesDuration = 15;

  return <div className='modal-body'>
    <div className='flex gap-2'>
      <div className="grow">
        {props.customInputFields ? props.customInputFields.map((customInputField, key) => {
          return <Input key={key} field={customInputField} readonly></Input>;
        }) : null}
      </div>
    </div>

    <div className="flex gap-2 flex-col md:flex-row">
      <div className='grow'>
        <Input field='subject' cssClass='text-primary text-2xl'></Input>
        <Input field='id_activity_type' customInputProps={{uiStyle: 'buttons'}} renderOnlyInputField></Input>

        <Divider>{T.translate('Start - End - Repeat')}</Divider>
        <div className='flex flex-col gap-2'>
          <div className='flex-dyn'>
            <Input renderOnlyInputField field='date_start' customInputProps={{
              onChange: (input: any, value: any) => {
                form.changeRecord({
                  date_start: value,
                  date_end: moment(value).add(daysDuration, 'days').format('YYYY-MM-DD')
                })
              }
            }}></Input>
            {allDay ? null : <Input renderOnlyInputField field='time_start' customInputProps={{
              hideSeconds: true,
              onChange: (input: any, value: any) => {
                form.changeRecord({
                  time_start: value,
                  time_end: moment(R.date_end + ' ' + value + ':00').add(hoursDuration, 'hours').add(minutesDuration, 'minutes').format('HH:mm:ss')
                })
              }
            }}></Input>}
          </div>

          <div className='flex-dyn'>
            <Input renderOnlyInputField field='date_end' />
            {allDay ? null : <Input renderOnlyInputField field='time_end' customInputProps={{
              hideSeconds: true,
            }}/>}
          </div>

          <Input field='recurrence' content={<div className='hubleto component input flex flex-col items-start gap-2 dark:text-gray-200'>
            {recurrenceParsed && recurrenceParsed.period == '' ?
              <select
                value={recurrenceParsed.period}
                className='w-full dark:bg-gray-800 dark:border-gray-600 dark:text-white'
                onChange={(event) => {
                  let newR = R;
                  //@ts-ignore
                  recurrenceParsed.period = event.currentTarget.value;
                  recurrenceParsed.dates = expandRecurrenceDates(recurrenceParsed);
                  newR.recurrence = JSON.stringify(recurrenceParsed);
                  form.changeRecord(newR);
                }}
              >
                <option value=''>{T.translate('Does not repeat','Hubleto\\App\\Community\\Calendar\\Loader', 'Components\\FormActivity')}</option>
                <option value='day'>{T.translate('Configure custom recurrence','Hubleto\\App\\Community\\Calendar\\Loader', 'Components\\FormActivity')}</option>
              </select>
            : <>
              <div className='flex gap-1 items-center text-nowrap'>
                <span>{T.translate('Repeat every','Hubleto\\App\\Community\\Calendar\\Loader', 'Components\\FormActivity')}</span>
                <input
                  className='dark:bg-gray-800 dark:border-gray-600 dark:text-white rounded border p-1'
                  type='number'
                  value={recurrenceParsed.periodEvery}
                  style={{width: '4em'}}
                  onChange={(event) => {
                    let newR = R;
                    recurrenceParsed.periodEvery = parseInt(event.currentTarget.value) ?? 1;
                    newR.recurrence = JSON.stringify(recurrenceParsed);
                    form.changeRecord(newR);
                  }}
                ></input>
                <select
                  className='dark:bg-gray-800 dark:border-gray-600 dark:text-white rounded border p-1'
                  value={recurrenceParsed.period}
                  style={{width: '8em'}}
                  onChange={(event) => {
                    let newR = R;
                    //@ts-ignore
                    recurrenceParsed.period = event.currentTarget.value;
                    recurrenceParsed.dates = expandRecurrenceDates(recurrenceParsed);
                    newR.recurrence = JSON.stringify(recurrenceParsed);
                    form.changeRecord(newR);
                  }}
                >
                  <option value=''>{T.translate('does not repeat','Hubleto\\App\\Community\\Calendar\\Loader', 'Components\\FormActivity')}</option>
                  <option value='day'>{T.translate('day','Hubleto\\App\\Community\\Calendar\\Loader', 'Components\\FormActivity')}</option>
                  <option value='week'>{T.translate('week','Hubleto\\App\\Community\\Calendar\\Loader', 'Components\\FormActivity')}</option>
                  <option value='month'>{T.translate('month','Hubleto\\App\\Community\\Calendar\\Loader', 'Components\\FormActivity')}</option>
                  <option value='year'>{T.translate('year','Hubleto\\App\\Community\\Calendar\\Loader', 'Components\\FormActivity')}</option>
                </select>
              </div>
              <div className='flex gap-1 items-center text-nowrap'>
                <span>{T.translate('End after','Hubleto\\App\\Community\\Calendar\\Loader', 'Components\\FormActivity')}</span>
                <input
                  className='dark:bg-gray-800 dark:border-gray-600 dark:text-white rounded border p-1'
                  type='number'
                  value={recurrenceParsed.periodCount}
                  style={{width: '4em'}}
                  onChange={(event) => {
                    let newR = R;
                    recurrenceParsed.periodCount = parseInt(event.currentTarget.value) ?? 1;
                    recurrenceParsed.dates = expandRecurrenceDates(recurrenceParsed);
                    newR.recurrence = JSON.stringify(recurrenceParsed);
                    form.changeRecord(newR);
                  }}
                ></input>
                <span>{T.translate('occurences.','Hubleto\\App\\Community\\Calendar\\Loader', 'Components\\FormActivity')}</span>
              </div>
              <div className='flex gap-1 text-nowrap'>
                {T.translate('Repeats from {{ dateFrom }} to {{ dateTo }}.','Hubleto\\App\\Community\\Calendar\\Loader', 'Components\\FormActivity').replace('{{ dateFrom }}', recurrenceParsed.dates[0]).replace('{{ dateTo }}', recurrenceParsed.dates[recurrenceParsed.dates.length - 1])}
              </div>
            </>}
          </div>}></Input>

        </div>

        <Input field='online_meeting_link'></Input>
        <Input field='location'></Input>
        <Input field='description'></Input>
      </div>
      {/* <div className='grow'>
        <Divider>{T.translate('Meeting minutes','Hubleto\\App\\Community\\Calendar\\Loader', 'Components\\FormActivity')}</Divider>
        <Input field='meeting_minutes'></Input>
        <Input field='meeting_minutes_link'></Input>
      </div> */}
    </div>
  </div>;
};
  
const CalendarFormActivity = (props: FormActivityProps) => {
  const id = (props.calendarTab ? props.calendarTab.showIdActivity : props.id);
  const defaultValues = (props.calendarTab ? {
    ...props.defaultValues,
    date_start: props.calendarTab.activityDate,
    time_start: props.calendarTab.activityTime == "00:00:00" ? null : props.calendarTab.activityTime,
    date_end: props.calendarTab.activityDate,
    all_day: props.calendarTab.activityAllDay,
    subject: props.calendarTab.activitySubject,
  } : props.defaultValues)

  return <Form
    id={id}
    model={props.model ?? 'Hubleto/App/Community/Calendar/Models/Activity'}
    renderTitle={() => {
      const subject = useRecordField('subject', '');
      const allDay = useRecordField('all_day', 0);
      const dateStart = useRecordField('date_start', '');
      const dateEnd = useRecordField('date_end', '');
      const timeStart = useRecordField('time_start', '');
      const timeEnd = useRecordField('time_end', '');

      let daysDuration = moment(dateEnd).diff(moment(dateStart), 'days');
      let hoursDuration = moment(dateEnd + ' ' + timeEnd).diff(moment(dateEnd + ' ' + timeStart), 'hours');
      let minutesDuration = moment(dateEnd + ' ' + timeEnd).diff(moment(dateEnd + ' ' + timeStart), 'minutes') - ((hoursDuration ?? 0) * 60);

      if (isNaN(hoursDuration)) hoursDuration = 0;
      if (isNaN(daysDuration)) daysDuration = 0;
      if (isNaN(minutesDuration)) minutesDuration = 15;

      if (allDay) {
        hoursDuration = 0;
        minutesDuration = 0;
      }

      return <div>
        <h2>{subject}</h2>
        <div className="badge badge-warning text-lg">
          {daysDuration > 0 && daysDuration + " d"}
          {(daysDuration > 0 && (hoursDuration > 0 || minutesDuration > 0)) && ", "}
          { hoursDuration > 0 && hoursDuration + " h"}
          {(hoursDuration > 0 && minutesDuration > 0) && ", "}
          {minutesDuration > 0 && minutesDuration + " m"}
        </div>
      </div>
    }}
    renderTopInputs={() => <div className='modal-top-inputs'>
      <Input field='id_owner' renderOnlyInputField />
      <Input field='all_day' renderOnlyInputField customInputProps={{yesText: T.translate('All-day')}}></Input>
      <Input field='completed' renderOnlyInputField customInputProps={{yesText: T.translate('Completed')}}></Input>
    </div>}
    renderContent={() => <Content {...props}/>}
    onClose={() => { 
      if (props.calendarTab) props.calendarTab.setShowIdActivity(0);
      else if (props.onClose) props.onClose();
    }}
    onAfterSaveRecord={(form: any, saveResponse: any) => {
      if (saveResponse.status == "success") {
        if (props.calendarTab) props.calendarTab.setShowIdActivity(0);
        else if (props.onAfterSaveRecord) props.onAfterSaveRecord();
      }
    }}
    description={{
      defaultValues: defaultValues,
    }}
  ></Form>;
}

export default CalendarFormActivity;