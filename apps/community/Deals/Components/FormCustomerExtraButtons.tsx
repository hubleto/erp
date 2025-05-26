import React, { Component } from 'react';
import FormCustomer, { FormCustomerProps, FormCustomerState } from '@hubleto/apps/community/Customers/Components/FormCustomer'
import TableDeals from './TableDeals';
import ModalSimple from "adios/ModalSimple";

interface P {
  formCustomer: FormCustomer<FormCustomerProps, FormCustomerState>
}

interface S {
  showDeals: boolean;
}

export default class FormCustomerExtraButtons extends Component<P, S> {
  props: P;
  state: S;

  constructor(props: P) {
    super(props);
    this.state = { showDeals: false };
  }

  render() {
    const form = this.props.formCustomer;
    const R = form.state.record;

    if (R.id > 0) {
      return <>
        <button
          className="btn btn-transparent"
          onClick={() => { this.setState({showDeals: true}); }}
        >
          <span className="icon"><i className="fas fa-handshake"></i></span>
          <span className="text">Show deals for {R?.name ?? '---'}</span>
        </button>
        {this.state.showDeals ?
          <ModalSimple
            uid='customer_table_deals_modal'
            isOpen={true}
            type='inside-parent'
            showHeader={true}
            title="Deals"
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

