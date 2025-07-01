import React, { Component } from 'react';
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/src/core/Components/HubletoForm';
import TableLeads from '@hubleto/apps/community/Leads/Components/TableLeads';

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
      <small>Campaign</small>
    </>;
  }

  renderContent(): JSX.Element {
    const R = this.state.record;
    if (!R) return null;

    // const LEADS = R.LEADS ? R.LEADS : null;

    return <>
      {this.inputWrapper('name')}
      {this.inputWrapper('id_manager')}
      {this.inputWrapper('target_audience')}
      {this.inputWrapper('goal')}
      {this.inputWrapper('color')}
      <div className="card mt-2">
        <div className="card-header">Leads</div>
        <div className="card-body">
          {/* {LEADS ? 
            <div className="list">
              {LEADS.map((lead, key) => {
                return <a href={globalThis.main.config.accountUrl + "/leads?recordId=" + lead.id} target="_blank" key={key} className="btn btn-list-item btn-transparent">
                  <span className="text flex gap-2">
                    <div>{lead.identifier}</div>
                    <div>{lead.title}</div>
                    <div>
                      {lead.CONTACT && lead.CONTACT.VALUES ? lead.CONTACT.VALUES.map((value, key) => {
                        return value.type == 'email' ? <span key={key}>{value.value}</span> : null;
                      }) : null}
                    </div>
                  </span>
                </a>
              })}
            </div>
          : <>No leads in this campaign.</>} */}
          <TableLeads
            uid={this.props.uid + "_table_leads"}
            tag="CampaignLeads"
            parentForm={this}
            idCampaign={R.id}
          />
        </div>
      </div>
    </>;
  }
}

