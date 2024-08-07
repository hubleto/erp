import React, { Component } from 'react'
import { deepObjectMerge } from "@adios/Helper";
import Form, { FormProps, FormState } from '@adios/Form';

interface FormUserProps extends FormProps {
}

interface FormUserState extends FormState {
}

export default class FormUser<P, S> extends Form<FormUserProps, FormUserState> {
  static defaultProps: any = {
    model: 'CeremonyCrmApp/Modules/Core/Settings/Models/User',
  }

  props: FormUserProps;
  state: FormUserState;

  constructor(props: FormUserProps) {
    super(props);
  }

  // getEndpointParams(): any {
  //   return {
  //     ...super.getEndpointParams(),
  //   }
  // }

  // loadParams() {
  //   let newState: any = deepObjectMerge({
  //     canCreate: true,
  //     canDelete: true,
  //     canRead: true,
  //     canUpdate: true,
  //     columns: {
  //       idCostingModel: {
  //         type: 'lookup', title: 'Costing model', 'model': 'AquilaCostingApp/Models/CostingModel',
  //         value: this.props.idCostingModel,
  //         readonly: true,
  //       },
  //       name: { type: 'varchar', title: 'Name' },
  //       isdcNumber: { type: 'varchar', title: 'ISDC Number' },
  //     },
  //   }, this.props);

  //   this.setState(newState, () => {
  //     this.loadRecord();
  //   });
  // }

  renderTitle(): JSX.Element {
    return <>
      <h2>{this.state.data.first_name} {this.state.data.middle_name} {this.state.data.last_name}</h2>
      <small>My account</small>
    </>;
  }

  renderContent(): JSX.Element {
    return <>
      {this.inputWrapper('first_name')}
      {this.inputWrapper('middle_name')}
      {this.inputWrapper('last_name')}
      {this.inputWrapper('email')}
      {this.inputWrapper('language')}
    </>;
  }
}
