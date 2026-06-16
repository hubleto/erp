import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import FormEmail, { FormEmailProps } from './FormEmail';

interface TableEmailsProps extends TableExtendedProps {
  idCampaign?: number,
}
interface TableEmailsState extends TableExtendedState {}

export default class TableEmails extends TableExtended<TableEmailsProps, TableEmailsState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/EmailMarketing/Models/Email',
  }

  props: TableEmailsProps;
  state: TableEmailsState;

  translationContext: string = 'Hubleto\\App\\Community\\EmailMarketing\\Loader';
  translationContextInner: string = 'Components\\TableEmails';

  constructor(props: TableEmailsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableEmailsProps) {
    return {
      ...super.getStateFromProps(props),
    }
  }

  getFormModalProps() {
    return {
      ...super.getFormModalProps(),
      type: 'right wide',
    };
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
      idCampaign: this.props.idCampaign,
    }
  }

  rowClassName(rowData: any): string {
    return rowData.is_closed ? 'bg-slate-300' : super.rowClassName(rowData);
  }

  setRecordFormUrl(id: number) {
    window.history.pushState({}, "", globalThis.hubleto.config.projectUrl + '/email-marketing/emails/' + (id > 0 ? id : 'add'));
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps() as FormEmailProps;
    return <FormEmail {...formProps}/>;
  }
}