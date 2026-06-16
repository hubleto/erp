import React, { Component } from 'react';
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';

export interface FormEmailClickProps extends FormExtendedProps {}
export interface FormEmailClickState extends FormExtendedState {}

export default class FormEmailClick<P, S> extends FormExtended<FormEmailClickProps, FormEmailClickState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: 'Hubleto/App/Community/EmailMarketing/Models/Click',
  };

  props: FormEmailClickProps;
  state: FormEmailClickState;

  translationContext: string = 'Hubleto\\App\\Community\\EmailMarketing\\Loader';
  translationContextInner: string = 'Components\\FormEmailClick';

  parentApp: string = 'Hubleto/App/Community/EmailMarketing';

  constructor(props: FormEmailClickProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormEmailClickProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Click')}</b> },
      ]
    };
  }

  getRecordFormUrl(): string {
    return 'email-marketing/emails/clicks/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate("Click")}</small>
      <h2>{this.state.record.email ?? '-'}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <>
          {this.inputWrapper('id_email', {readonly: true})}
          {this.inputWrapper('id_recipient', {readonly: true})}
          {this.inputWrapper('url', {readonly: true})}
          {this.inputWrapper('datetime_clicked', {readonly: true})}
          {this.inputWrapper('log', {readonly: true})}
          {this.inputWrapper('bot_score', {readonly: true})}
        </>;
      break

      default:
        return super.renderTab(tabUid);
      break;
    }
  }
}

