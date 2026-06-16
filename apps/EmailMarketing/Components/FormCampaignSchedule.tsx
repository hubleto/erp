import React, { Component } from 'react';
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';

export interface FormCampaignScheduleProps extends FormExtendedProps {}
export interface FormCampaignScheduleState extends FormExtendedState {}

export default class FormCampaignSchedule<P, S> extends FormExtended<FormCampaignScheduleProps, FormCampaignScheduleState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: 'Hubleto/App/Community/EmailMarketing/Models/CampaignSchedule',
  };

  props: FormCampaignScheduleProps;
  state: FormCampaignScheduleState;

  translationContext: string = 'Hubleto\\App\\Community\\EmailMarketing\\Loader';
  translationContextInner: string = 'Components\\FormCampaignSchedule';

  parentApp: string = 'Hubleto/App/Community/EmailMarketing';

  constructor(props: FormCampaignScheduleProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormCampaignScheduleProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Campaign schedule')}</b> },
      ]
    };
  }

  getRecordFormUrl(): string {
    return 'email-marketing/campaign/schedules/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate("Campaign schedule")}</small>
      <h2>{this.state.record.email ?? '-'}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <>
          {this.inputWrapper('id_campaign')}
          {this.inputWrapper('day')}
          {this.inputWrapper('id_email')}
        </>;
      break

      default:
        return super.renderTab(tabUid);
      break;
    }
  }
}

