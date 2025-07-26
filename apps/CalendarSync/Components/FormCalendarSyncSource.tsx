import React, { Component } from 'react';
import Form, { FormProps, FormState } from '@hubleto/react-ui/core/Form';
import FormInput from '@hubleto/react-ui/core/FormInput';
import Lookup from '@hubleto/react-ui/core/Inputs/Lookup';

export default class FormCalendarSyncSource<P, S> extends Form<FormProps, FormState> {
  static defaultProps: any = {
    ...Form.defaultProps,
    model: 'HubletoApp/Community/Customers/Models/CustomerActivity',
  };

  props: FormProps;
  state: FormState;

  translationContext: string = 'HubletoApp\\Community\\CalendarSync\\Loader::Components\\FormSource';

  renderContent(): JSX.Element {

    return (
      <>
        {this.inputWrapper('name')}
        {this.inputWrapper('type')}
        {this.inputWrapper('link')}
        { this.state.record.type == 'google' ?
          <div className="alert-info mt-1">
            Enter the Calendar ID from the settings of your calendar in Google Calendar (look at the bottom of the page).
            <br/> It should be in the form of <span>{'<'}Google Calendar identifier{'>'}@group.calendar.google.com</span>.
            <br/> The calendar must also be set to public or the API Key specified in the config file must have access to it.
          </div>
          : <></> }
        { this.state.record.type == 'ics' ?
          <div className="alert-info mt-1">
            Enter the URL that points to an ICS file.
          </div>
          : <></> }
        {this.inputWrapper('color')}
        {this.inputWrapper('active')}
        { this.state.record.type == 'google' ?
          <div className="alert-warning mt-1">
            When using Google calendar, make sure you define an API key in ConfigEnv.php: $config['google-api-key'] = ...
          </div>
          : <></> }
      </>
    );
  }
}
