import React, { Component } from 'react';
import HubletoForm, {HubletoFormProps, HubletoFormState} from "../../../../src/core/Components/HubletoForm";
import FormInput from 'adios/FormInput';
import Lookup from 'adios/Inputs/Lookup';
import FormActivity, { FormActivityProps, FormActivityState } from '@hubleto/apps/community/Calendar/Components/FormActivity'

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

  renderCustomInputs(): JSX.Element {
    const R = this.state.record;

    return <>
      {this.inputWrapper('id_lead')}
      <FormInput title={"Contact"}>
        <Lookup {...this.getInputProps('id_contact')}
          model='HubletoApp/Community/Contacts/Models/Contact'
          endpoint={`contacts/get-customer-contacts`}
          customEndpointParams={{id_customer: this.props.idCustomer}}
          value={R.id_contact}
          onChange={(value: any) => {
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
