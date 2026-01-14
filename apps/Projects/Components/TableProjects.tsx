import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import FormProject, { FormProjectProps } from './FormProject';

interface TableProjectsProps extends TableExtendedProps {
  idDeal?: number,
}
interface TableProjectsState extends TableExtendedState { }

export default class TableProjects extends TableExtended<TableProjectsProps, TableProjectsState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Projects/Models/Project',
  }

  props: TableProjectsProps;
  state: TableProjectsState;

  translationContext: string = 'Hubleto\\App\\Community\\Projects\\Loader';
  translationContextInner: string = 'Components\\TableProjects';

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
    window.history.pushState({}, "", globalThis.hubleto.config.projectUrl + '/projects/' + (id > 0 ? id : 'add'));
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