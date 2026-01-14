import React, { Component } from 'react';
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';
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
    ...FormExtended.defaultProps,
    model: 'Hubleto/App/Community/Leads/Models/LeadActivity',
  };

  props: LeadFormActivityProps;
  state: LeadFormActivityState;

  translationContext: string = 'Hubleto\\App\\Community\\Leads\\Loader';
  translationContextInner: string = 'Components\\FormActivity';

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
      {this.inputWrapper('id_lead', {readonly: R.id > 0})}
      <FormInput title={this.translate("Contact")}>
        <Lookup {...this.getInputProps('id_contact')}
          model='Hubleto/App/Community/Contacts/Models/Contact'
          endpoint={`contacts/api/get-customer-contacts`}
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
