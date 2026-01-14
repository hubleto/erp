import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import FormRecipient, { FormRecipientProps } from './FormRecipient';

interface TableRecipientsProps extends TableExtendedProps {
  idCampaign: number
}
interface TableRecipientsState extends TableExtendedState {}

export default class TableRecipients extends TableExtended<TableRecipientsProps, TableRecipientsState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Campaigns/Models/Recipient',
  }

  props: TableRecipientsProps;
  state: TableRecipientsState;

  translationContext: string = 'Hubleto\\App\\Community\\Campaigns\\Loader';
  translationContextInner: string = 'Components\\TableRecipients';

  constructor(props: TableRecipientsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableRecipientsProps) {
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
      idCampaign: this.props.idCampaign
    }
  }

  getCsvImportEndpointParams(): any {
    return {
      model: this.props.model,
      defaultCsvImportValues: {
        id_campaign: this.props.idCampaign,
      }
    }
  }

  rowClassName(rowData: any): string {
    return rowData.is_opted_out ? 'bg-red-300' : (rowData.is_invalid ? 'bg-gray-300' : super.rowClassName(rowData));
  }

  setRecordFormUrl(id: number) {
    window.history.pushState({}, "", globalThis.hubleto.config.projectUrl + '/campaigns/recipients/' + (id > 0 ? id : 'add'));
  }

  renderCell(columnName: string, column: any, data: any, options: any) {
    if (columnName == "virt_status" && data.virt_status) {
      const status = data.virt_status.split(',');
      const isOptedOut = status[0] == 'opted-out';
      const isInvalid = status[1] == 'invalid';
      return <>
        {isOptedOut ? <div className='badge badge-danger'>{this.translate('Opted out')}</div> : null}
        {isInvalid ? <div className='badge'>{this.translate('Invalid')}</div> : null}
      </>;
    } else {
      return super.renderCell(columnName, column, data, options);
    }
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps() as FormRecipientProps;
    if (!formProps.description) formProps.description = {};
    formProps.description.defaultValues = { id_campaign: this.props.idCampaign };
    return <FormRecipient {...formProps}/>;
  }
}