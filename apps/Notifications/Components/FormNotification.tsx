import React, { Component } from 'react';
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';

export interface FormNotificationProps extends HubletoFormProps {}
export interface FormNotificationState extends HubletoFormState {}

export default class FormNotification<P, S> extends HubletoForm<FormNotificationProps,FormNotificationState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Notifications/Models/Notification',
  };

  props: FormNotificationProps;
  state: FormNotificationState;

  translationContext: string = 'HubletoApp\\Community\\Notifications\\Loader::Components\\FormNotification';

  constructor(props: FormNotificationProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormNotificationProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  renderTitle(): JSX.Element {
    return <>
      <small>Notification</small>
      <h2>{this.state.record.subject ? this.state.record.subject : ''}</h2>
    </>;
  }

  renderContent(): JSX.Element {
    return <>
      <div className='flex gap-2'>
        <div className='flex-3'>
          {this.inputWrapper('id_to')}
          {this.inputWrapper('subject')}
          {this.inputWrapper('body')}
        </div>
        <div className='flex-1'>
          {this.inputWrapper('id_from')}
          {this.inputWrapper('priority')}
          {this.inputWrapper('category')}
          {this.inputWrapper('tags')}
          {this.inputWrapper('datetime_sent')}
          {this.inputWrapper('color')}
        </div>
      </div>
    </>;
  }
}

