import React, { Component } from 'react';
import FormInput from '@hubleto/react-ui/core/FormInput';
import Lookup from '@hubleto/react-ui/core/Inputs/Lookup';
import FormActivity, { FormActivityProps, FormActivityState } from '@hubleto/apps/Calendar/Components/FormActivity'

export interface CustomerFormActivityProps extends FormActivityProps {
  idCustomer: number,
}

export interface CustomerFormActivityState extends FormActivityState {}

export default class CustomerFormActivity<P, S> extends FormActivity<CustomerFormActivityProps,CustomerFormActivityState> {
  static defaultProps: any = {
    ...FormActivity.defaultProps,
    model: 'HubletoApp/Community/Customers/Models/CustomerActivity',
  };

  props: CustomerFormActivityProps;
  state: CustomerFormActivityState;

  translationContext: string = 'HubletoApp\\Community\\Customers\\Loader::Components\\CustomerFormActivity';

  getActivitySourceReadable(): string
  {
    return this.translate('Customer');
  }

  renderCustomInputs(): JSX.Element {
    const R = this.state.record;

    return <>
      <FormInput title={this.translate("Customer")} required={true}>
        <Lookup {...this.getInputProps('id_customer')}
          model='HubletoApp/Community/Contacts/Models/Customer'
          endpoint={`customers/api/get-customer`}
          value={R.id_customer}
          onChange={(input: any, value: any) => {
            this.updateRecord({ id_customer: value});
          }}
        ></Lookup>
      </FormInput>
      <FormInput title={this.translate("Contact")}>
        <Lookup {...this.getInputProps("id_contact")}
          model='HubletoApp/Community/Contacts/Models/Contact'
          endpoint={`contacts/get-customer-contacts`}
          customEndpointParams={{id_customer: R.id_customer}}
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
