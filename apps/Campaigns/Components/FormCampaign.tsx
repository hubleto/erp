import React, { Component } from 'react';
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import TableLeads from '@hubleto/apps/Leads/Components/TableLeads';

export interface FormCampaignProps extends HubletoFormProps {}
export interface FormCampaignState extends HubletoFormState {}

export default class FormCampaign<P, S> extends HubletoForm<FormCampaignProps,FormCampaignState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Campaigns/Models/Campaign',
  };

  props: FormCampaignProps;
  state: FormCampaignState;

  translationContext: string = 'HubletoApp\\Community\\Campaigns\\Loader::Components\\FormCampaign';

  constructor(props: FormCampaignProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormCampaignProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  renderTitle(): JSX.Element {
    return <>
      <h2>{this.state.record.subject ? this.state.record.subject : ''}</h2>
      <small>{this.translate("Campaign")}</small>
    </>;
  }

  renderContent(): JSX.Element {
    const R = this.state.record;
    if (!R) return null;

    // const LEADS = R.LEADS ? R.LEADS : null;

    return <>
      <div className='w-full flex gap-2'>
        <div className='flex-1 border-r border-gray-100'>
          {this.inputWrapper('name')}
          {this.inputWrapper('target_audience')}
          {this.inputWrapper('goal')}
        </div>
        <div className='flex-1'>
          {this.inputWrapper('id_manager')}
          {this.inputWrapper('notes')}
          {this.inputWrapper('color')}
          {this.inputWrapper('datetime_created')}
        </div>
      </div>
      {this.divider(this.translate('Leads'))}
      {this.state.id < 0 ?
          <div className="badge badge-info">{this.translate("First create the campaign, then you will be prompted to create leads.")}</div>
        :
          <TableLeads
            uid={this.props.uid + "_table_leads"}
            tag="CampaignLeads"
            parentForm={this}
            idCampaign={R.id}
            selectionMode='multiple'
          />
      }
    </>;
  }
}

