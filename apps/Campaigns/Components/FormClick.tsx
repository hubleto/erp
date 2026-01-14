import React, { Component } from 'react';
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';
import request from '@hubleto/react-ui/core/Request';

export interface FormClickProps extends FormExtendedProps {}
export interface FormClickState extends FormExtendedState {
  mailPreviewInfo?: any,
}

export default class FormClick<P, S> extends FormExtended<FormClickProps, FormClickState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: 'Hubleto/App/Community/Campaigns/Models/Click',
  };

  props: FormClickProps;
  state: FormClickState;

  translationContext: string = 'Hubleto\\App\\Community\\Campaigns\\Loader';
  translationContextInner: string = 'Components\\FormClick';

  parentApp: string = 'Hubleto/App/Community/Campaigns';

  constructor(props: FormClickProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormClickProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Campaign')}</b> },
      ]
    };
  }

  getRecordFormUrl(): string {
    return 'campaigns/clicks/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate("Click")}</small>
      <h2>{this.state.record.email ?? '-'}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;
    const mailPreviewInfo: any = this.state.mailPreviewInfo;

    switch (tabUid) {
      case 'default':
        return <>
          {this.inputWrapper('id_campaign')}
          {this.inputWrapper('id_recipient')}
          {this.inputWrapper('url')}
          {this.inputWrapper('datetime_clicked')}
        </>;
      break

      default:
        return super.renderTab(tabUid);
      break;
    }
  }
}

