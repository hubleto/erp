import React, { Component } from 'react';
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';

export interface FormNotificationProps extends FormExtendedProps {}
export interface FormNotificationState extends FormExtendedState {}

export default class FormNotification<P, S> extends FormExtended<FormNotificationProps,FormNotificationState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: 'Hubleto/App/Community/Notifications/Models/Notification',
  };

  props: FormNotificationProps;
  state: FormNotificationState;

  translationContext: string = 'Hubleto\\App\\Community\\Notifications\\Loader';
  translationContextInner: string = 'Components\\FormNotification';

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
      <small>{this.translate('Notification')}</small>
      <h2>{this.state.record.subject ? this.state.record.subject : ''}</h2>
    </>;
  }

  renderContent(): JSX.Element {
    return <>
      <div className='flex gap-2'>
        <div className='flex-3'>
          {this.inputWrapper('id_to')}
          {this.inputWrapper('subject')}
          {this.inputWrapper('url')}
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

