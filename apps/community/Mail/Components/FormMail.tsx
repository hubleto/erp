import React, { Component } from 'react';
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/src/core/Components/HubletoForm';

export interface FormMailProps extends HubletoFormProps {}
export interface FormMailState extends HubletoFormState {}

export default class FormMail<P, S> extends HubletoForm<FormMailProps,FormMailState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Mail/Models/Mail',
  };

  props: FormMailProps;
  state: FormMailState;

  translationContext: string = 'HubletoApp\\Community\\Mail\\Loader::Components\\FormMail';

  constructor(props: FormMailProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormMailProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  renderTitle(): JSX.Element {
    return <>
      <h2>{this.state.record.subject ? this.state.record.subject : ''}</h2>
      <small>Mail</small>
    </>;
  }

  renderContent(): JSX.Element {
    return <>
      <div className='flex gap-2'>
        <div className='flex-3'>
          {this.inputWrapper('to')}
          {this.inputWrapper('cc')}
          {this.inputWrapper('bcc')}
          {this.inputWrapper('subject')}
          {this.inputWrapper('body')}
        </div>
        <div className='flex-1'>
          {/* {this.inputWrapper('id_owner')} */}
          {this.inputWrapper('from')}
          {this.inputWrapper('priority')}
          {this.inputWrapper('sent')}
          {this.inputWrapper('color')}
        </div>
      </div>
    </>;
  }
}

