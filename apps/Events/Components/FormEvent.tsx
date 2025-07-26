import React, { Component } from 'react'
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import TableEventVenues from './TableEventVenues';
import TableEventAttendees from './TableEventAttendees';
import TableEventSpeakers from './TableEventSpeakers';

interface FormEventProps extends HubletoFormProps { }
interface FormEventState extends HubletoFormState { }

export default class FormEvent<P, S> extends HubletoForm<FormEventProps, FormEventState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Events/Models/Team',
  }

  props: FormEventProps;
  state: FormEventState;

  translationContext: string = 'HubletoApp\\Community\\Events::Components\\FormEvent';

  constructor(props: FormEventProps) {
    super(props);
  }

  renderTitle(): JSX.Element {
    return <>
      <h2>Record #{this.state.record.id ?? '0'}</h2>
      <small>Event</small>
    </>;
  }

  renderContent(): JSX.Element {
    // This is an example code to render content of the form.
    // You should develop your own render content.
    return <>
      <div className='w-full flex gap-2'>
        <div className="flex-1 border-r border-gray-100">
          {this.inputWrapper('title')}
          {this.inputWrapper('id_type')}
          {this.inputWrapper('attendance_options')}
          {this.inputWrapper('brief_description')}
          {this.inputWrapper('full_description')}
        </div>
        <div className="flex-1">
          <div className="card mt-2">
            <div className="card-header">{this.translate('Venues')}</div>
            <div className="card-body">
              {this.state.id < 0 ?
                <div className="badge badge-info">First create event, then you will be prompted to add its venues.</div>
              :
                <TableEventVenues
                  uid={this.props.uid + '_table_venues'}
                  parentForm={this}
                  customEndpointParams={ { idEvent: this.state.id } }
                ></TableEventVenues>
              }
            </div>
          </div>
        </div>
      </div>
      <div className='w-full flex gap-2'>
        <div className="flex-1 border-r border-gray-100">
          <div className="card mt-2">
            <div className="card-header">{this.translate('Attendees')}</div>
            <div className="card-body">
              {this.state.id < 0 ?
                <div className="badge badge-info">First create event, then you will be prompted to add its attendees.</div>
              :
                <TableEventAttendees
                  uid={this.props.uid + '_table_attendees'}
                  parentForm={this}
                  customEndpointParams={ { idEvent: this.state.id } }
                ></TableEventAttendees>
              }
            </div>
          </div>
        </div>
        <div className="flex-1 border-r border-gray-100">
          <div className="card mt-2">
            <div className="card-header">{this.translate('Speakers')}</div>
            <div className="card-body">
              {this.state.id < 0 ?
                <div className="badge badge-info">First create event, then you will be prompted to add its speakers.</div>
              :
                <TableEventSpeakers
                  uid={this.props.uid + '_table_speakers'}
                  parentForm={this}
                  customEndpointParams={ { idEvent: this.state.id } }
                ></TableEventSpeakers>
              }
            </div>
          </div>
        </div>
      </div>
    </>;
  }
}
