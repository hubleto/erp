import React, { Component } from 'react'
import request from "@hubleto/react-ui/core/Request";
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';
import { FormProps } from '@hubleto/react-ui/core/Form';
import FormTemplate from './FormTemplate';

interface TableTemplatesProps extends TableProps {
  idMailbox?: number,
  mailboxName?: string,
  showOnlyDrafts?: boolean,
  showOnlyTemplates?: boolean,
}
interface TableTemplatesState extends TableState {
}

export default class TableTemplates extends Table<TableTemplatesProps, TableTemplatesState> {
  static defaultProps = {
    ...Table.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Mail/Models/Template',
  }

  props: TableTemplatesProps;
  state: TableTemplatesState;

  translationContext: string = 'Hubleto\\App\\Community\\Mail\\Loader';
  translationContextInner: string = 'Components\\TableTemplates';

  constructor(props: TableTemplatesProps) {
    super(props);
    this.state = {
      ...this.getStateFromProps(props),
    };
  }

  getEndpointParams() {
    return {
      ...super.getEndpointParams(),
      idMailbox: this.props.idMailbox,
      showOnlyDrafts: this.props.showOnlyDrafts,
      showOnlyTemplates: this.props.showOnlyTemplates,
    }
  }

  getFormModalProps(): any {
    let params = super.getFormModalProps();
    params.type = 'right wide';
    return params;
  }

  setRecordFormUrl(id: number) {
    window.history.pushState({}, "", globalThis.hubleto.config.projectUrl + '/mail/templates/' + (id > 0 ? id : 'add'));
  }

  renderForm(): JSX.Element {
    let formProps: FormProps = this.getFormProps();
    return <FormTemplate {...formProps}/>;
  }

}