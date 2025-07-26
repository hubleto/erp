import React, { Component } from 'react';
import FormCustomer, { FormCustomerProps, FormCustomerState } from '@hubleto/apps/Customers/Components/FormCustomer'
import TableLeads from './TableLeads';
import ModalSimple from "@hubleto/react-ui/core/ModalSimple";
import TranslatedComponent from "@hubleto/react-ui/core/TranslatedComponent";

interface P {
  form: FormCustomer<FormCustomerProps, FormCustomerState>
}

interface S {
  showLeads: boolean;
}

export default class FormCustomerTopMenu extends TranslatedComponent<P, S> {
  props: P;
  state: S;

  translationContext: string = 'HubletoApp\\Community\\Leads\\Loader::Components\\FormLead';

  constructor(props: P) {
    super(props);
    this.state = { showLeads: false };
  }

  render() {
    const form = this.props.form;
    const R = form.state.record;

    if (R.id > 0) {
      return <>
        <button
          className="btn btn-transparent"
          onClick={() => { this.setState({showLeads: !this.state.showLeads}); }}
        >
          <span className="icon"><i className="fas fa-people-arrows"></i></span>
          <span className="text">{this.translate('Leads')}</span>
        </button>
        {this.state.showLeads ?
          <ModalSimple
            uid='customer_table_leads_modal'
            isOpen={true}
            type='centered'
            showHeader={true}
            title="Leads"
            onClose={(modal: ModalSimple) => { this.setState({showLeads: false}); }}
          >
            <TableLeads
              uid={form.props.uid + "_table_leads"}
              tag="CustomerLeads"
              parentForm={form}
              idCustomer={R.id}
            />
          </ModalSimple>
        : null}
      </>
    }
  }
}

