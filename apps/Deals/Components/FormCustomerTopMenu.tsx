import React, { Component } from 'react';
import FormCustomer, { FormCustomerProps, FormCustomerState } from '@hubleto/apps/Customers/Components/FormCustomer'
import TableDeals from './TableDeals';
import ModalSimple from "@hubleto/react-ui/core/ModalSimple";
import TranslatedComponent from "@hubleto/react-ui/core/TranslatedComponent";

interface P {
  form: FormCustomer<FormCustomerProps, FormCustomerState>
}

interface S {
  showDeals: boolean;
}

export default class FormCustomerTopMenu extends TranslatedComponent<P, S> {
  props: P;
  state: S;

  translationContext: string = 'HubletoApp\\Community\\Deals\\Loader::Components\\FormDeal';

  constructor(props: P) {
    super(props);
    this.state = { showDeals: false };
  }

  render() {
    const form = this.props.form;
    const R = form.state.record;

    if (R.id > 0) {
      return <>
        <button
          className="btn btn-transparent"
          onClick={() => { this.setState({showDeals: !this.state.showDeals}); }}
        >
          <span className="icon"><i className="fas fa-handshake"></i></span>
          <span className="text">{this.translate('Deals')}</span>
        </button>
        {this.state.showDeals ?
          <ModalSimple
            uid='customer_table_deals_modal'
            isOpen={true}
            type='centered'
            showHeader={true}
            title={this.translate("Deals")}
            onClose={(modal: ModalSimple) => { this.setState({showDeals: false}); }}
          >
            <TableDeals
              uid={form.props.uid + "_table_deals"}
              parentForm={form}
              idCustomer={R.id}
            />
          </ModalSimple>
        : null}
      </>
    }
  }
}

