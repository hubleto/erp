import React, { Component } from 'react';
import FormCustomer, { FormCustomerProps, FormCustomerState } from '@hubleto/apps/community/Customers/Components/FormCustomer'
import TableLeads from './TableLeads';
import ModalSimple from "adios/ModalSimple";
import TranslatedComponent from "adios/TranslatedComponent";

interface P {
  formCustomer: FormCustomer<FormCustomerProps, FormCustomerState>
}

interface S {
  showLeads: boolean;
}

export default class FormCustomerExtraButtons extends TranslatedComponent<P, S> {
  props: P;
  state: S;

  translationContext: string = 'HubletoApp\\Community\\Leads\\Loader::Components\\FormLead';

  constructor(props: P) {
    super(props);
    this.state = { showLeads: false };
  }

  render() {
    const form = this.props.formCustomer;
    const R = form.state.record;

    if (R.id > 0) {
      return <>
        <button
          className="btn btn-transparent w-full"
          onClick={() => { this.setState({showLeads: true}); }}
        >
          <span className="icon"><i className="fas fa-people-arrows"></i></span>
          <span className="text">{this.translate('Show leads')}</span>
        </button>
        {this.state.showLeads ?
          <ModalSimple
            uid='customer_table_leads_modal'
            isOpen={true}
            type='right theme-secondary'
            showHeader={true}
            title="Leads"
            onClose={(modal: ModalSimple) => { this.setState({showLeads: false}); }}
          >
            <TableLeads
              uid={form.props.uid + "_table_leads"}
              parentForm={form}
              idCustomer={R.id}
            />
          </ModalSimple>
        : null}
      </>
    }
  }
}

