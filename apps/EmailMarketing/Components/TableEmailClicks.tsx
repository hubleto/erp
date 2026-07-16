import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import FormEmailClick, { FormEmailClickProps } from './FormEmailClick';

interface TableEmailClicksProps extends TableExtendedProps {
  idEmail?: number,
  email?: string,
}
interface TableEmailClicksState extends TableExtendedState {}

export default class TableEmailClicks extends TableExtended<TableEmailClicksProps, TableEmailClicksState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/EmailMarketing/Models/EmailClick',
    orderBy: {field: 'datetime_clicked', direction: 'desc'},
  }

  props: TableEmailClicksProps;
  state: TableEmailClicksState;

  translationContext: string = 'Hubleto\\App\\Community\\EmailMarketing\\Loader';
  translationContextInner: string = 'Components\\TableEmailClicks';

  constructor(props: TableEmailClicksProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableEmailClicksProps) {
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
      idEmail: this.props.idEmail,
      email: this.props.email,
    }
  }

  getCsvImportEndpointParams(): any {
    return {
      model: this.props.model,
      defaultCsvImportValues: {
        id_email: this.props.idEmail,
      }
    }
  }

  rowClassName(rowData: any): string {
    return rowData.is_closed ? 'bg-slate-300' : super.rowClassName(rowData);
  }

  setRecordFormUrl(id: number) {
    window.history.pushState({}, "", globalThis.hubleto.config.projectUrl + '/email-marketing/emails/clicks/' + (id > 0 ? id : 'add'));
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps() as FormEmailClickProps;
    if (!formProps.description) formProps.description = {};
    formProps.description.defaultValues = { ...formProps.description.defaultValues ?? {}, id_email: this.props.idEmail };
    return <FormEmailClick {...formProps}/>;
  }
}