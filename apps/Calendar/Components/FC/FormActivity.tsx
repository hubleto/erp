import React, { Component } from 'react';
// import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/components/cc/FormExtended';
import moment from 'moment';
import Translator from "@hubleto/react-ui/core/Translator";
import Form from '@hubleto/react-ui/components/fc/Form';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import Divider from '@hubleto/react-ui/components/fc/FormComponents/Divider';
import { FormMetaContext, FormDescriptionContext } from '@hubleto/react-ui/components/fc/Form';
import { useRecordField, FormRecordStoreContext } from '@hubleto/react-ui/components/fc/FormRecordStore';
import Modal from '@hubleto/react-ui/components/fc/Modal';

export interface FormActivityProps extends FormProps {
  renderCustomInputs?: (form: typeof FormMetaContext) => React.JSX.Element,
  activitySource?: string,
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

const Title = (props: FormActivityProps): React.JSX.Element => <>
  <small>{props.activitySource ?? 'Event'}</small>
  <h2>{useRecordField('subject') ?? '-'}</h2>
</>;

const Content = (props: FormActivityProps): React.JSX.Element => {
  const R = React.useContext(FormRecordStoreContext).getRecord();
  const form = React.useContext(FormMetaContext);
  const customInputs = props.renderCustomInputs(form) ?? null;

  let recurrence: Recurrence = {
    period: '',
    periodEvery: 1,
    periodCount: 0,
    dates: [],
  };

  if (R.recurrence != '') {
    try {
      recurrence = JSON.parse(R.recurrence);
      recurrence.dates = expandRecurrenceDates(recurrence);
    } catch (ex) {
      recurrence = {
        period: '',
        periodEvery: 1,
        periodCount: 0,
        dates: [],
      };
      recurrence.dates = expandRecurrenceDates(recurrence);
    }
  }

  let daysDuration = moment(R.date_end).diff(moment(R.date_start), 'days');
  let hoursDuration = moment(R.date_end + ' ' + R.time_end).diff(moment(R.date_end + ' ' + R.time_start), 'hours');
  let minutesDuration = moment(R.date_end + ' ' + R.time_end).diff(moment(R.date_end + ' ' + R.time_start), 'minutes') - ((hoursDuration ?? 0) * 60);

  if (isNaN(hoursDuration)) hoursDuration = 0;
  if (isNaN(daysDuration)) daysDuration = 0;
  if (isNaN(minutesDuration)) minutesDuration = 15;

  if (R.all_day) {
    hoursDuration = 0;
    minutesDuration = 0;
  }

  return <>
    <div className='flex gap-2'>
      {customInputs ? <div className="grow">{customInputs}</div> : null}
      <div className='flex gap-2 flex-col'>
        <div className='w-full'><Input field='completed' customInputProps={{yesText: T.translate('Completed')}}></Input></div>
        <Input field='id_owner'></Input>
      </div>
    </div>

    <div className="flex gap-2 flex-col md:flex-row">
      <div className='grow'>
        <Input field='subject' cssClass='text-primary text-2xl'></Input>
        <Input field='id_activity_type'></Input>
        <Input field='online_meeting_link'></Input>
      </div>
      <div className='grow'>
        <Input field='location'></Input>
        <Input field='description'></Input>
      </div>
    </div>
    <Input field='all_day' customInputProps={{yesText: T.translate('All-day')}}></Input>
    <div className='flex gap-2 w-full flex-col md:flex-row'>
      <div className='w-1/2'>
        <Divider>{T.translate('Start - End','Hubleto\\App\\Community\\Calendar\\Loader', 'Components\\FormActivity')}</Divider>
        <Input renderOnlyInput field='date_start' customInputProps={{
          onChange: (input: any, value: any) => {
            form.changeRecord({date_end: moment(value).add(daysDuration, 'days').format('YYYY-MM-DD')})
          }
        }}></Input>
        {R.all_day ? null : <Input renderOnlyInput field='time_start' customInputProps={{
          onChange: (input: any, value: any) => {
            form.changeRecord({time_end: moment(R.date_end + ' ' + value + ':00').add(minutesDuration, 'minutes').format('HH:mm:ss')})
          }
        }}></Input>}

        <Input renderOnlyInput field='date_end' />
        {R.all_day ? null : <Input renderOnlyInput field='time_end' />}

        <div className="mt-2 alert alert-info">
          {T.translate('Duration','Hubleto\\App\\Community\\Calendar\\Loader', 'Components\\FormActivity')}: {daysDuration > 0 && daysDuration + " " + T.translate('day(s)','Hubleto\\App\\Community\\Calendar\\Loader', 'Components\\FormActivity')}{(daysDuration > 0 && (hoursDuration > 0 || minutesDuration > 0)) && ", "}{ hoursDuration > 0 && hoursDuration + " " + T.translate('hours','Hubleto\\App\\Community\\Calendar\\Loader', 'Components\\FormActivity')}{(hoursDuration > 0 && minutesDuration > 0) && ", "}{ minutesDuration > 0 && minutesDuration + " " + T.translate('minutes','Hubleto\\App\\Community\\Calendar\\Loader', 'Components\\FormActivity')}
        </div>
      </div>
      <div className='w-1/2'>
        <Divider>{T.translate('Repeats','Hubleto\\App\\Community\\Calendar\\Loader', 'Components\\FormActivity')}</Divider>
        <Input field='recurrence' content={<div className='hubleto component input flex flex-col items-start gap-2 dark:text-gray-200'>
          {recurrence && recurrence.period == '' ?
            <select
              value={recurrence.period}
              className='w-full dark:bg-gray-800 dark:border-gray-600 dark:text-white'
              onChange={(event) => {
                let newR = R;
                //@ts-ignore
                recurrence.period = event.currentTarget.value;
                recurrence.dates = expandRecurrenceDates(recurrence);
                newR.recurrence = JSON.stringify(recurrence);
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
                value={recurrence.periodEvery}
                style={{width: '4em'}}
                onChange={(event) => {
                  let newR = R;
                  recurrence.periodEvery = parseInt(event.currentTarget.value) ?? 1;
                  newR.recurrence = JSON.stringify(recurrence);
                  form.changeRecord(newR);
                }}
              ></input>
              <select
                className='dark:bg-gray-800 dark:border-gray-600 dark:text-white rounded border p-1'
                value={recurrence.period}
                style={{width: '8em'}}
                onChange={(event) => {
                  let newR = R;
                  //@ts-ignore
                  recurrence.period = event.currentTarget.value;
                  recurrence.dates = expandRecurrenceDates(recurrence);
                  newR.recurrence = JSON.stringify(recurrence);
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
                value={recurrence.periodCount}
                style={{width: '4em'}}
                onChange={(event) => {
                  let newR = R;
                  recurrence.periodCount = parseInt(event.currentTarget.value) ?? 1;
                  recurrence.dates = expandRecurrenceDates(recurrence);
                  newR.recurrence = JSON.stringify(recurrence);
                  form.changeRecord(newR);
                }}
              ></input>
              <span>{T.translate('occurences.','Hubleto\\App\\Community\\Calendar\\Loader', 'Components\\FormActivity')}</span>
            </div>
            <div className='flex gap-1 text-nowrap'>
              {T.translate('Repeats from {{ dateFrom }} to {{ dateTo }}.','Hubleto\\App\\Community\\Calendar\\Loader', 'Components\\FormActivity').replace('{{ dateFrom }}', recurrence.dates[0]).replace('{{ dateTo }}', recurrence.dates[recurrence.dates.length - 1])}
            </div>
          </>}
        </div>}
        ></Input>
      </div>
    </div>
    <Divider>{T.translate('Meeting minutes','Hubleto\\App\\Community\\Calendar\\Loader', 'Components\\FormActivity')}</Divider>
    <Input field='meeting_minutes'></Input>
    <Input field='meeting_minutes_link'></Input>
  </>;
};
  
const FormActivity = (props: FormActivityProps) => {
  return <Form
    model='Hubleto/App/Community/Calendar/Models/Activity'
    title={{sub: props.activitySource ? 'Activity for ' + props.activitySource : 'Event', field: 'subject'}}
    {...props}
  >
    <Content {...props}/>
  </Form>;
}

export default FormActivity;