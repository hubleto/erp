import React, { Component } from 'react';
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';
import HtmlFrame from "@hubleto/react-ui/core/HtmlFrame";
import FormEmail from './FormEmail';

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
        { uid: 'default', title: <b>{this.translate('Schedule')}</b> },
      ]
    };
  }

  getRecordFormUrl(): string {
    return 'email-marketing/campaign/schedules/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate("Campaign")} &raquo; {this.translate("Scheduled email")}</small>
      <h2>{this.state.record.CAMPAIGN?.title ?? '-'}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <div>
          <div className='flex gap-2'>
            <div>{this.inputWrapper('day')}</div>
            <div className='grow'>{this.inputWrapper('id_email')}</div>
          </div>
          <div className='mt-4'>
            {R.id_email > 0 ?
              <FormEmail
                id={R.id_email}
                showFooter={false}
              />
            : <div className='alert alert-warning'>Select email to be sent on <b>day {R.day}</b></div>}
          </div>
        </div>;
      break

      default:
        return super.renderTab(tabUid);
      break;
    }
  }
}

