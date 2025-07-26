import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormAgenda from './FormAgenda';

interface TableAgendasProps extends HubletoTableProps {
  // Uncomment and modify these lines if you want to create URL-based filtering for your model
  // idCustomer?: number,
}

interface TableAgendasState extends HubletoTableState {
}

export default class TableAgendas extends HubletoTable<TableAgendasProps, TableAgendasState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Events/Models/Agenda',
  }

  props: TableAgendasProps;
  state: TableAgendasState;

  translationContext: string = 'HubletoApp\\Community\\Events::Components\\TableAgendas';

  constructor(props: TableAgendasProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableAgendasProps) {
    return {
      ...super.getStateFromProps(props),
    }
  }

  getFormModalProps(): any {
    let params = super.getFormModalProps();
    params.type = 'right wide';
    return params;
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
      // Uncomment and modify these lines if you want to create URL-based filtering for your model
      // idCustomer: this.props.idCustomer,
    }
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps();
    // formProps.customEndpointParams.idCustomer = this.props.idCustomer;
    // if (!formProps.description) formProps.description = {};
    // formProps.description.defaultValues = { id_customer: this.props.idCustomer };
    return <FormAgenda {...formProps}/>;
  }
}