import React, { Component } from 'react';
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';

export interface FormAccountProps extends FormExtendedProps {}
export interface FormAccountState extends FormExtendedState {}

export default class FormAccount<P, S> extends FormExtended<FormAccountProps,FormAccountState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: 'Hubleto/App/Community/Mail/Models/Account',
    renderOwnerManagerUi: true,
  };

  props: FormAccountProps;
  state: FormAccountState;

  translationContext: string = 'Hubleto\\App\\Community\\Mail\\Loader';
  translationContextInner: string = 'Components\\FormAccount';

  constructor(props: FormAccountProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormAccountProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Email account')}</b> },
        { uid: 'smtp', title: <b>{this.translate('Sending (SMTP)')}</b> },
      ]
    };
  }

  renderTitle(): null|JSX.Element {
    return <>
      <h2>{this.state.record.subject ? this.state.record.subject : ''}</h2>
      <small>{this.translate('Account')}</small>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <>
          {this.inputWrapper('name')}
          {this.inputWrapper('color')}
          {this.divider('Receiving (IMAP)')}
          {this.inputWrapper('imap_host')}
          {this.inputWrapper('imap_port')}
          {this.inputWrapper('imap_encryption')}
          {this.inputWrapper('imap_username')}
          {this.inputWrapper('imap_password')}
        </>;
      break

      case 'smtp':
        return <>
          {this.inputWrapper('sender_email')}
          {this.inputWrapper('sender_name')}
          {this.inputWrapper('smtp_host')}
          {this.inputWrapper('smtp_port')}
          {this.inputWrapper('smtp_encryption')}
          {this.inputWrapper('smtp_username')}
          {this.inputWrapper('smtp_password')}
        </>;
      break

      default:
        return super.renderTab(tabUid);
      break;
    }
  }
}

