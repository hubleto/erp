import React, { Component } from 'react';
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import FormActivity, { FormActivityProps, FormActivityState } from '@hubleto/apps/Calendar/Components/FormActivity'
import FormInput from '@hubleto/react-ui/core/FormInput';
import Lookup from '@hubleto/react-ui/core/Inputs/Lookup';

export interface LeadFormActivityProps extends FormActivityProps {
  idLead: number,
  idCustomer?: number,
}

export interface LeadFormActivityState extends FormActivityState {
}

export default class LeadFormActivity<P, S> extends FormActivity<LeadFormActivityProps, LeadFormActivityState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Leads/Models/LeadActivity',
  };

  props: LeadFormActivityProps;
  state: LeadFormActivityState;

  translationContext: string = 'HubletoApp\\Community\\Leads\\Loader::Components\\FormActivity';

  constructor(props: LeadFormActivityProps) {
    super(props);
  }

  getActivitySourceReadable(): string
  {
    return this.translate('Lead');
  }

  renderCustomInputs(): JSX.Element {
    const R = this.state.record;

    return <>
      {this.inputWrapper('id_lead')}
      <FormInput title={this.translate("Contact")}>
        <Lookup {...this.getInputProps('id_contact')}
          model='HubletoApp/Community/Contacts/Models/Contact'
          endpoint={`contacts/get-customer-contacts`}
          customEndpointParams={{id_customer: this.props.idCustomer}}
          value={R.id_contact}
          onChange={(input: any, value: any) => {
            this.updateRecord({ id_contact: value })
            if (R.id_contact == 0) {
              R.id_contact = null;
              this.setState({record: R})
            }
          }}
        ></Lookup>
      </FormInput>
    </>;
  }
}
