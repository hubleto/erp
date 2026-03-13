import React, { Component } from 'react'
import request from "@hubleto/react-ui/core/Request";
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import Form, { FormProps } from '@hubleto/react-ui/core/Form';
import FormAuditLog from './FormAuditLog';

interface TableAuditLogsProps extends TableExtendedProps {
  folder?: string,
}
interface TableAuditLogsState extends TableExtendedState {
}

export default class TableAuditLogs extends TableExtended<TableAuditLogsProps, TableAuditLogsState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/AuditLogs/Models/AuditLog',
  }

  props: TableAuditLogsProps;
  state: TableAuditLogsState;

  translationContext: string = 'Hubleto\\App\\Community\\AuditLogs\\Loader';
  translationContextInner: string = 'Components\\TableAuditLogs';

  constructor(props: TableAuditLogsProps) {
    super(props);
    this.state = {
      ...this.getStateFromProps(props),
    };
  }

  getEndpointParams() {
    return {
      ...super.getEndpointParams(),
      folder: this.props.folder
    }
  }

  getFormModalProps(): any {
    let params = super.getFormModalProps();
    params.type = 'right';
    return params;
  }

  renderForm(): JSX.Element {
    let formProps: FormProps = this.getFormProps();
    return <FormAuditLog {...formProps}/>;
  }

}