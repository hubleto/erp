import React, { Component } from 'react';
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import moment from 'moment';

export interface FormActivityProps extends HubletoFormProps {}
export interface FormActivityState extends HubletoFormState {}

export default class FormActivity<P, S> extends HubletoForm<FormActivityProps,FormActivityState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Calendar/Models/Activity',
  };

  props: FormActivityProps;
  state: FormActivityState;

  translationContext: string = 'HubletoApp\\Community\\Calendar\\Loader::Components\\FormActivity';

  getActivitySourceReadable(): string
  {
    return 'Event';
  }

  renderTitle(): JSX.Element {
    return <>
      <h2>{this.state.record.subject ?? ''}</h2>
      <small>{this.getActivitySourceReadable()}</small>
    </>;
  }

  renderCustomInputs(): JSX.Element {
    return <></>;
  }

  renderContent(): JSX.Element {
    const R = this.state.record;

    const customInputs = this.renderCustomInputs();

    let daysDuration = moment(R.date_end).diff(moment(R.date_start), 'days');
    let minutesDuration = moment(R.date_end + ' ' + R.time_end).diff(moment(R.date_end + ' ' + R.time_start), 'minutes');

    if (isNaN(daysDuration)) daysDuration = 0;
    if (isNaN(minutesDuration)) minutesDuration = 15;

    return <>
      {customInputs ? <div className="p-2 mb-2 bg-blue-50">{customInputs}</div> : null}

      <div className="flex gap-2 flex-col md:flex-row">
        <div className='w-full'>{this.inputWrapper('subject', {cssClass: 'text-primary text-2xl'})}</div>
        <div className='w-full'>{this.inputWrapper('id_activity_type')}</div>
      </div>
      {this.inputWrapper('all_day')}
      <div className="mt-2 alert alert-info">{daysDuration} day(s), {minutesDuration} minutes</div>
      <div className='flex gap-2 w-full flex-col md:flex-row'>
        <div className='w-full'>
          {this.divider(this.translate('Start'))}
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
        </div>
        <div className='w-full'>
          {this.divider(this.translate('End'))}
          {this.input('date_end')}
          {R.all_day ? null : this.input('time_end')}
        </div>
      </div>
      {this.inputWrapper('meeting_minutes_link')}
      <div className='flex gap-2 w-full flex-col md:flex-row'>
        <div className='w-full'>{this.inputWrapper('completed')}</div>
        <div className='w-full'>{this.inputWrapper('id_owner')}</div>
      </div>
      
    </>;
  }
}
