import React, { Component } from 'react'
import { deepObjectMerge } from "adios/Helper";
import Form, { FormProps, FormState } from 'adios/Form';

interface FormPersonProps extends FormProps {
  idAccounts: number,
  idReport: number,
}

interface FormPersonState extends FormState {
}

export default class FormPerson<P, S> extends Form<FormPersonProps, FormPersonState> {
  static defaultProps = {
    isInlineEditing: true,
  }

  props: FormPersonProps;
  state: FormPersonState;

  constructor(props: FormPersonProps) {
    super(props);
    // TOTO: Formulár potrebuje mať model pre ReportSenzor
    // props.model = 'EMonitorApp/Models/ReportSenzor'
  }

  getEndpointParams(): object {
    let params: object = super.getEndpointParams();
    params['idZariadenie'] = this.props.idAccounts;
    params['idReport'] = this.props.idReport;
    return params;
  }

  loadParams() {
    let newState: any = deepObjectMerge({
      canCreate: true,
      canDelete: true,
      canRead: true,
      canUpdate: true,
      columns: {
        id_company: {
          type: 'lookup',
          title: 'Company',
          model: 'CeremonyCrmApp/Modules/Core/Customers/Models/Company',
          // endpoint: 'lookups/senzor/?idZariadenie=' + this.props.idZariadenie,
          // readonly: true,
        },
        first_name: { type: "varchar", title: "First Name" },
        last_name: { type: "varchar",title: "Last Name" },
        virt_address: {
            type: "lookup",
            model: "CeremonyCrmApp/Modules/Core/Customers/Models/PersonAddress",
            title: "Address"
        },
        virt_contact: {
            type: "lookup",
            model: "CeremonyCrmApp/Modules/Core/Customers/Models/PersonContact",
            title: "Contact"
        },
      },
    }, this.props);

    this.setState(newState, () => {
      this.loadRecord();
    });
  }

  renderTitle(): JSX.Element {
    return <>
      <h2>{this.state.data.first_name} {this.state.data.last_name}</h2>
      <small>ID #<b>{this.state.data.id ? this.state.data.id : <span className="text-red-500">[?]</span>}</b></small>
    </>
  }

  renderContent(): JSX.Element {
    return <>
      {this.inputWrapper('first_name')}
      {this.inputWrapper('last_name')}
      {this.inputWrapper('virt_address')}
      {this.inputWrapper('virt_contact')}
      {this.inputWrapper('id_company')}
    </>;
  }
}
