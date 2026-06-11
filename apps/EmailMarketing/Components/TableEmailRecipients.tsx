import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import FormEmailRecipient, { FormEmailRecipientProps } from './FormEmailRecipient';

interface TableEmailRecipientsProps extends TableExtendedProps {
  idEmail: number
}
interface TableEmailRecipientsState extends TableExtendedState {}

export default class TableEmailRecipients extends TableExtended<TableEmailRecipientsProps, TableEmailRecipientsState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/EmailMarketing/Models/EmailRecipient',
  }

  props: TableEmailRecipientsProps;
  state: TableEmailRecipientsState;

  translationContext: string = 'Hubleto\\App\\Community\\EmailMarketing\\Loader';
  translationContextInner: string = 'Components\\TableEmailRecipients';

  constructor(props: TableEmailRecipientsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableEmailRecipientsProps) {
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
      idEmail: this.props.idEmail
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
    return rowData.is_unsubscribed ? 'bg-red-300' : (rowData.is_invalid ? 'bg-gray-300' : super.rowClassName(rowData));
  }

  setRecordFormUrl(id: number) {
    window.history.pushState({}, "", globalThis.hubleto.config.projectUrl + '/email-marketing/recipients/' + (id > 0 ? id : 'add'));
  }

  renderCell(columnName: string, column: any, data: any, options: any) {
    if (columnName == "virt_status" && data.virt_status) {
      const status = data.virt_status.split(',');
      const isUnsubscribed = status[0] == 'unsubscribed';
      const isInvalid = status[1] == 'invalid';
      return <>
        {isUnsubscribed ? <div className='badge badge-danger'>{this.translate('Unsubscribed')}</div> : null}
        {isInvalid ? <div className='badge'>{this.translate('Invalid')}</div> : null}
      </>;
    } else {
      return super.renderCell(columnName, column, data, options);
    }
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps() as FormEmailRecipient;
    if (!formProps.description) formProps.description = {};
    formProps.description.defaultValues = { id_email: this.props.idEmail };
    return <FormEmailRecipient {...formProps}/>;
  }
}