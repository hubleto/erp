import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormProject from './FormProject';

interface TableProjectsProps extends HubletoTableProps {
  idDeal?: number,
}

interface TableProjectsState extends HubletoTableState {
}

export default class TableProjects extends HubletoTable<TableProjectsProps, TableProjectsState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Projects/Models/Project',
  }

  props: TableProjectsProps;
  state: TableProjectsState;

  translationContext: string = 'HubletoApp\\Community\\Projects::Components\\TableProjects';

  constructor(props: TableProjectsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableProjectsProps) {
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
      idDeal: this.props.idDeal,
    }
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps();
    formProps.customEndpointParams.idDeal = this.props.idDeal;
    if (!formProps.description) formProps.description = {};
    formProps.description.defaultValues = { id_deal: this.props.idDeal };
    return <FormProject {...formProps}/>;
  }
}