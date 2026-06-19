import React, { Component } from 'react';
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';
import HtmlFrame from "@hubleto/react-ui/core/HtmlFrame";

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

  refPreview: any = React.createRef();

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
      <h2>{this.state.record.CAMPAIGN?.title ?? '-'}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <>
          {this.inputWrapper('day')}
          {this.inputWrapper('id_email')}
          <div className='card mt-2'>
            <div className='card-header'>{R.EMAIL?.mail_subject}</div>
            <div className='card-header'>From: {R.EMAIL?.SENDER_ACCOUNT?.name}</div>
            <div className='card-body'>
              <HtmlFrame
                ref={this.refPreview}
                className='w-full h-full'
                content={R.EMAIL?.mail_body}
              />
            </div>
          </div>
        </>;
      break

      default:
        return super.renderTab(tabUid);
      break;
    }
  }
}

