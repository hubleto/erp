import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormActivityType from './FormActivityType';

interface TableActivityTypesProps extends HubletoTableProps {
  // Uncomment and modify these lines if you want to create URL-based filtering for your model
  // idCustomer?: number,
}

interface TableActivityTypesState extends HubletoTableState {
}

export default class TableActivityTypes extends HubletoTable<TableActivityTypesProps, TableActivityTypesState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Worksheets/Models/ActivityType',
  }

  props: TableActivityTypesProps;
  state: TableActivityTypesState;

  translationContext: string = 'HubletoApp\\Community\\Worksheets::Components\\TableActivityTypes';

  constructor(props: TableActivityTypesProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableActivityTypesProps) {
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
    return <FormActivityType {...formProps}/>;
  }
}