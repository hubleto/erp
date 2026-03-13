import React, { Component } from 'react';
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';

export interface FormAuditLogProps extends FormExtendedProps {}
export interface FormAuditLogState extends FormExtendedState {}

export default class FormAuditLog<P, S> extends FormExtended<FormAuditLogProps,FormAuditLogState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: 'Hubleto/App/Community/AuditLogs/Models/AuditLog',
  };

  props: FormAuditLogProps;
  state: FormAuditLogState;

  translationContext: string = 'Hubleto\\App\\Community\\AuditLogs\\Loader';
  translationContextInner: string = 'Components\\FormAuditLog';

  constructor(props: FormAuditLogProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormAuditLogProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate('Audit Log')}</small>
      <h2>{this.state.record.context}</h2>
      <small>{this.state.record.model} #{this.state.record.record_id}</small>
    </>;
  }

  renderContent(): JSX.Element {
    return <>
      {this.inputWrapper('datetime')}
      {this.inputWrapper('type')}
      {this.inputWrapper('context')}
      {this.inputWrapper('model')}
      {this.inputWrapper('record_id')}
      {this.inputWrapper('message')}
      {this.inputWrapper('priority')}
      {this.inputWrapper('id_user')}
      {this.inputWrapper('ip')}
    </>;
  }
}

