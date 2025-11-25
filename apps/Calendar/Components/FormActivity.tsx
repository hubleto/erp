import React, { Component } from 'react';
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import moment from 'moment';

export interface FormActivityProps extends HubletoFormProps {}
export interface FormActivityState extends HubletoFormState {}

interface Recurrence {
  period: '' | 'day' | 'week' | 'month' | 'year',
  periodEvery: number, // e.g. repeat every 3 weeks
  periodCount: number, // ends after 'periodCount' occurences
  dates: Array<string>,
}

export default class FormActivity<P, S> extends HubletoForm<FormActivityProps,FormActivityState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    icon: 'fas fa-calendar-days',
    model: 'Hubleto/App/Community/Calendar/Models/Activity',
  };

  props: FormActivityProps;
  state: FormActivityState;

  recurrence: Recurrence;

  translationContext: string = 'Hubleto\\App\\Community\\Calendar\\Loader';
  translationContextInner: string = 'Components\\FormActivity';

  getActivitySourceReadable(): string
  {
    return 'Event';
  }

  expandRecurrenceDates(recurrence: Recurrence): Array<string> {
    let dates = [];
    let date = moment();
    let periodCount = recurrence.periodCount;

    for (let i = 0; i < periodCount; i++) {
      dates.push(date.format('YYYY-MM-DD'));
      date = date.add(recurrence.periodEvery, recurrence.period);
    }

    return dates;

  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.getActivitySourceReadable()}</small>
      <h2>{this.state.record.subject ?? ''}</h2>
    </>;
  }

  renderCustomInputs(): JSX.Element|null {
    return null;
  }

  renderContent(): JSX.Element {
    const R = this.state.record;
    const customInputs = this.renderCustomInputs();

    let recurrence: Recurrence = {
      period: '',
      periodEvery: 1,
      periodCount: 0,
      dates: [],
    };

    if (R.recurrence != '') {
      try {
        recurrence = JSON.parse(R.recurrence);
        recurrence.dates = this.expandRecurrenceDates(recurrence);
      } catch (ex) {
        recurrence = {
          period: '',
          periodEvery: 1,
          periodCount: 0,
          dates: [],
        };
        recurrence.dates = this.expandRecurrenceDates(recurrence);
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
        {customInputs ? <div className="grow p-2 mb-2 bg-blue-50">{customInputs}</div> : null}
        <div className='flex gap-2 flex-col'>
          <div className='w-full'>{this.inputWrapper('completed')}</div>
          <div className='w-full'>{this.inputWrapper('id_owner')}</div>
        </div>
      </div>

      <div className="flex gap-2 flex-col md:flex-row">
        <div className='grow'>
          {this.inputWrapper('subject', {cssClass: 'text-primary text-2xl'})}
          {this.inputWrapper('id_activity_type')}
          {this.inputWrapper('online_meeting_link')}
        </div>
        <div className='grow'>
          {this.inputWrapper('location')}
          {this.inputWrapper('description')}
        </div>
      </div>
      {this.inputWrapper('all_day')}
      <div className='flex gap-2 w-full flex-col md:flex-row'>
        <div className='w-1/2'>
          {this.divider(this.translate('Start - End'))}
          {this.input('date_start', {
            onChange: (input: any, value: any) => {
              this.updateRecord({date_end: moment(value).add(daysDuration, 'days').format('YYYY-MM-DD')})
            }
          })}
          {R.all_day ? null : this.input('time_start', {
            onChange: (input: any, value: any) => {
              console.log(value, moment(R.date_end + ' ' + value + ':00'), moment(R.date_end + ' ' + value + ':00').add(minutesDuration, 'minutes').format('HH:mm:ss'))
              this.updateRecord({time_end: moment(R.date_end + ' ' + value + ':00').add(minutesDuration, 'minutes').format('HH:mm:ss')})
            }
          })}

          {this.input('date_end')}
          {R.all_day ? null : this.input('time_end')}

          <div className="mt-2 alert alert-info">
            Duration: {daysDuration > 0 && daysDuration + " day(s)"}{(daysDuration > 0 && (hoursDuration > 0 || minutesDuration > 0)) && ", "}{ hoursDuration > 0 && hoursDuration + " hours"}{(hoursDuration > 0 && minutesDuration > 0) && ", "}{ minutesDuration > 0 && minutesDuration + " minutes"}
          </div>
        </div>
        <div className='w-1/2'>
          {this.divider(this.translate('Repeats'))}
          {this.inputWrapperCustom('recurrence', {}, '', <div className='hubleto component input flex flex-col items-start gap-2'>
            {recurrence && recurrence.period == '' ?
              <select
                value={recurrence.period}
                className='w-full'
                onChange={(event) => {
                  let newR = R;
                  //@ts-ignore
                  recurrence.period = event.currentTarget.value;
                  recurrence.dates = this.expandRecurrenceDates(recurrence);
                  newR.recurrence = JSON.stringify(recurrence);
                  this.updateRecord(newR);
                }}
              >
                <option value=''>Does not repeat</option>
                <option value='day'>Configure custom recurrence</option>
              </select>
            : <>
              <div className='flex gap-1 items-center text-nowrap'>
                <span>Repeat every</span>
                <input
                  type='number'
                  value={recurrence.periodEvery}
                  style={{width: '4em'}}
                  onChange={(event) => {
                    let newR = R;
                    recurrence.periodEvery = parseInt(event.currentTarget.value) ?? 1;
                    newR.recurrence = JSON.stringify(recurrence);
                    this.updateRecord(newR);
                  }}
                ></input>
                <select
                  value={recurrence.period}
                  style={{width: '8em'}}
                  onChange={(event) => {
                    let newR = R;
                    //@ts-ignore
                    recurrence.period = event.currentTarget.value;
                    recurrence.dates = this.expandRecurrenceDates(recurrence);
                    newR.recurrence = JSON.stringify(recurrence);
                    this.updateRecord(newR);
                  }}
                >
                  <option value=''>does not repeat</option>
                  <option value='day'>day</option>
                  <option value='week'>week</option>
                  <option value='month'>month</option>
                  <option value='year'>year</option>
                </select>
              </div>
              <div className='flex gap-1 items-center text-nowrap'>
                <span>End after</span>
                <input
                  type='number'
                  value={recurrence.periodCount}
                  style={{width: '4em'}}
                  onChange={(event) => {
                    let newR = R;
                    recurrence.periodCount = parseInt(event.currentTarget.value) ?? 1;
                    recurrence.dates = this.expandRecurrenceDates(recurrence);
                    newR.recurrence = JSON.stringify(recurrence);
                    this.updateRecord(newR);
                  }}
                ></input>
                <span>occurences.</span>
              </div>
              <div className='flex gap-1 text-nowrap'>
                Repeats from {recurrence.dates[0]} to {recurrence.dates[recurrence.dates.length - 1]}.
              </div>
            </>}
          </div>)}
        </div>
      </div>
      {this.divider('Meeting minutes')}
      {this.inputWrapper('meeting_minutes')}
      {this.inputWrapper('meeting_minutes_link')}
    </>;
  }
}
