import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import FormGroup from './FormGroup';

interface TableGroupsProps extends TableExtendedProps {}

interface TableGroupsState extends TableExtendedState {}

export default class TableGroups extends TableExtended<TableGroupsProps, TableGroupsState> {

  static defaultProps = {
    ...TableExtended.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Products/Models/Group',
  }

  props: TableGroupsProps;
  state: TableGroupsState;

  translationContext: string = 'Hubleto\\App\\Community\\Products\\Loader';
  translationContextInner: string = 'Components\\TableGroups';

  constructor(props: TableGroupsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableGroupsProps) {
    return {
      ...super.getStateFromProps(props)
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
    }
  }

  setRecordFormUrl(id: number) {
    window.history.pushState({}, "", globalThis.hubleto.config.projectUrl + '/products/groups/' + (id > 0 ? id : 'add'));
  }

  renderHeaderRight(): Array<JSX.Element> {
    let elements: Array<JSX.Element> = super.renderHeaderRight();

    return elements;
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps();
    return <FormGroup {...formProps}/>;
  }
}