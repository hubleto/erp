import React, { Component } from 'react';
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';

export interface FormCampaignScheduleRecipientProps extends FormExtendedProps {}
export interface FormCampaignScheduleRecipientState extends FormExtendedState {}

export default class FormCampaignScheduleRecipient<P, S> extends FormExtended<FormCampaignScheduleRecipientProps, FormCampaignScheduleRecipientState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: 'Hubleto/App/Community/EmailMarketing/Models/CampaignScheduleRecipients',
  };

  props: FormCampaignScheduleRecipientProps;
  state: FormCampaignScheduleRecipientState;

  translationContext: string = 'Hubleto\\App\\Community\\EmailMarketing\\Loader';
  translationContextInner: string = 'Components\\FormCampaignScheduleRecipient';

  parentApp: string = 'Hubleto/App/Community/EmailMarketing';

  constructor(props: FormCampaignScheduleRecipientProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormCampaignScheduleRecipientProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Campaign schedule recipient')}</b> },
      ]
    };
  }

  getRecordFormUrl(): string {
    return 'email-marketing/campaign/schedules/recipients/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate("Campaign schedule recipients")}</small>
      <h2>{this.state.record.email ?? '-'}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <>
          {this.inputWrapper('id_campaign_schedule')}
          {this.inputWrapper('id_recipient')}
          {this.inputWrapper('id_email')}
        </>;
      break

      default:
        return super.renderTab(tabUid);
      break;
    }
  }
}

