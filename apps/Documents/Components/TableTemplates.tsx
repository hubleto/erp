import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import { FormProps } from '@hubleto/react-ui/core/Form';
import FormTemplate from './FormTemplate';

interface TableTemplatesProps extends HubletoTableProps {}
interface TableTemplatesState extends HubletoTableState {}

export default class TableTemplates extends HubletoTable<TableTemplatesProps, TableTemplatesState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    formUseModalSimple: true,
    orderBy: {
      field: "id",
      direction: "desc"
    },
    model: 'HubletoApp/Community/Documents/Models/Template',
  }

  props: TableTemplatesProps;
  state: TableTemplatesState;

  translationContext: string = 'HubletoApp\\Community\\Documents\\Loader::Components\\TableTemplates';

  constructor(props: TableTemplatesProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableTemplatesProps) {
    return {
      ...super.getStateFromProps(props),
    }
  }

  getFormModalProps(): any {
    let params = super.getFormModalProps();
    params.type = 'centered small';
    return params;
  }

  renderForm(): JSX.Element {
    let formProps: FormProps = this.getFormProps();
    return <FormTemplate {...formProps}/>;
  }
}