import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormPhase from './FormPhase';

interface TablePhasesProps extends HubletoTableProps {
  // Uncomment and modify these lines if you want to create URL-based filtering for your model
  // idCustomer?: number,
}

interface TablePhasesState extends HubletoTableState {
}

export default class TablePhases extends HubletoTable<TablePhasesProps, TablePhasesState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Projects/Models/Phase',
  }

  props: TablePhasesProps;
  state: TablePhasesState;

  translationContext: string = 'HubletoApp\\Community\\Projects::Components\\TablePhases';

  constructor(props: TablePhasesProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TablePhasesProps) {
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
    // formProps.description.defaultValues = { idDashboard: this.props.idDashboard };
    return <FormPhase {...formProps}/>;
  }
}