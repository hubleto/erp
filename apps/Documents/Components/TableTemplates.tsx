import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import { FormProps } from '@hubleto/react-ui/core/Form';
import FormTemplate from './FormTemplate';

interface TableTemplatesProps extends TableExtendedProps {}
interface TableTemplatesState extends TableExtendedState {}

export default class TableTemplates extends TableExtended<TableTemplatesProps, TableTemplatesState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Documents/Models/Template',
  }

  props: TableTemplatesProps;
  state: TableTemplatesState;

  translationContext: string = 'Hubleto\\App\\Community\\Documents\\Loader';
  translationContextInner: string = 'Components\\TableTemplates';

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
    params.type = 'right wide';
    return params;
  }

  setRecordFormUrl(id: number) {
    window.history.pushState({}, "", globalThis.hubleto.config.projectUrl + '/documents/templates/' + (id > 0 ? id : 'add'));
  }

  renderForm(): JSX.Element {
    let formProps: FormProps = this.getFormProps();
    return <FormTemplate {...formProps}/>;
  }
}