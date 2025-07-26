import React, { Component } from 'react';
import FormInput from '@hubleto/react-ui/core/FormInput';
import Lookup from '@hubleto/react-ui/core/Inputs/Lookup';
import FormActivity, { FormActivityProps, FormActivityState } from '@hubleto/apps/Calendar/Components/FormActivity'

export interface DealFormActivityProps extends FormActivityProps {
  idDeal: number,
  idCustomer?: number,
}

export interface DealFormActivityState extends FormActivityState {
}

export default class DealFormActivity<P, S> extends FormActivity<DealFormActivityProps, DealFormActivityState> {
  static defaultProps: any = {
    ...FormActivity.defaultProps,
    model: 'HubletoApp/Community/Deals/Models/DealActivity',
  };

  props: DealFormActivityProps;
  state: DealFormActivityState;

  translationContext: string = 'HubletoApp\\Community\\Deals\\Loader::Components\\FormActivity';

  getActivitySourceReadable(): string
  {
    return this.translate('Deal');
  }

  renderCustomInputs(): JSX.Element {
    const R = this.state.record;

    return <>
      {this.inputWrapper('id_deal')}
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
