import React, { Component } from 'react';
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';
import request from '@hubleto/react-ui/core/Request';

export interface FormRecipientStatusProps extends FormExtendedProps {}
export interface FormRecipientStatusState extends FormExtendedState {
  mailPreviewInfo?: any,
}

export default class FormRecipientStatus<P, S> extends FormExtended<FormRecipientStatusProps, FormRecipientStatusState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: 'Hubleto/App/Community/Campaigns/Models/RecipientStatus',
  };

  props: FormRecipientStatusProps;
  state: FormRecipientStatusState;

  translationContext: string = 'Hubleto\\App\\Community\\Campaigns\\Loader';
  translationContextInner: string = 'Components\\FormRecipientStatus';

  parentApp: string = 'Hubleto/App/Community/Campaigns';

  constructor(props: FormRecipientStatusProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormRecipientStatusProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Campaign')}</b> },
      ]
    };
  }

  getRecordFormUrl(): string {
    return 'campaigns/recipients/statuses/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate("Recipient status")}</small>
      <h2>{this.state.record.email ?? '-'}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;
    const mailPreviewInfo: any = this.state.mailPreviewInfo;

    switch (tabUid) {
      case 'default':
        return <>
          {this.inputWrapper('email')}
          {this.inputWrapper('is_opted_out')}
          {this.inputWrapper('is_invalid')}
        </>;
      break

      default:
        return super.renderTab(tabUid);
      break;
    }
  }
}

