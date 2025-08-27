import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormProject, { FormProjectProps } from './FormProject';

interface TableProjectsProps extends HubletoTableProps {
  idDeal?: number,
}
interface TableProjectsState extends HubletoTableState { }

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

  setRecordFormUrl(id: number) {
    window.history.pushState({}, "", globalThis.main.config.projectUrl + '/projects/' + id);
  }

  rowClassName(rowData: any): string {
    return rowData.is_closed ? 'bg-slate-300' : super.rowClassName(rowData);
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps() as FormProjectProps;
    formProps.customEndpointParams.idDeal = this.props.idDeal;
    if (!formProps.description) formProps.description = {};
    formProps.description.defaultValues = { id_deal: this.props.idDeal };

    return <FormProject {...formProps}/>;
  }
}