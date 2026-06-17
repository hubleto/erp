import React, { Component } from 'react';
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';
import TableCampaignSchedules from '@hubleto/apps/EmailMarketing/Components/TableCampaignSchedules';

export interface FormCampaignProps extends FormExtendedProps {}
export interface FormCampaignState extends FormExtendedState {}

export default class FormCampaign<P, S> extends FormExtended<FormCampaignProps, FormCampaignState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: 'Hubleto/App/Community/EmailMarketing/Models/Campaign',
    renderOwnerManagerUi: true,
  };

  props: FormCampaignProps;
  state: FormCampaignState;

  translationContext: string = 'Hubleto\\App\\Community\\EmailMarketing\\Loader';
  translationContextInner: string = 'Components\\FormCampaign';

  parentApp: string = 'Hubleto/App/Community/EmailMarketing';

  constructor(props: FormCampaignProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormCampaignProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Campaign')}</b> },
      ]
    };
  }

  getRecordFormUrl(): string {
    return 'email-marketing/campaigns/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate("Campaign")}</small>
      <h2>{this.state.record.email ?? '-'}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <div className='flex gap-2'>
          <div className='grow'>
            {this.inputWrapper('title')}
            {this.inputWrapper('target_audience')}
            {this.inputWrapper('goal')}
            {this.inputWrapper('notes')}
            {this.inputWrapper('color')}
            {this.inputWrapper('id_workflow')}
            {this.inputWrapper('id_workflow_step')}
            {this.inputWrapper('id_owner')}
            {this.inputWrapper('id_manager')}
            {this.inputWrapper('is_closed')}
          </div>
          {this.state.id <= 0 ? null :
            <div className='grow'>
              <TableCampaignSchedules
                tag='table_campaign_schedules'
                parentForm={this}
                uid={this.props.uid + "_table_campaign_schedules"}
                idCampaign={R.id}
              />
            </div>
          }
        </div>;
      break

      default:
        return super.renderTab(tabUid);
      break;
    }
  }
}

