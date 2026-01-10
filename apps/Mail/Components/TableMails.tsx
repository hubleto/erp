import React, { Component } from 'react'
import request from "@hubleto/react-ui/core/Request";
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';
import { FormProps } from '@hubleto/react-ui/core/Form';
import FormMail from './FormMail';

interface TableMailsProps extends TableProps {
  idAccount?: number,
  idMailbox?: number,
  mailboxName?: string,
  showOnlyScheduledToSend?: boolean,
  showOnlySent?: boolean,
  showOnlyDrafts?: boolean,
  showOnlyTemplates?: boolean,
}
interface TableMailsState extends TableState {
}

export default class TableMails extends Table<TableMailsProps, TableMailsState> {
  static defaultProps = {
    ...Table.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Mail/Models/Mail',
  }

  props: TableMailsProps;
  state: TableMailsState;

  translationContext: string = 'Hubleto\\App\\Community\\Mail\\Loader';
  translationContextInner: string = 'Components\\TableMails';

  constructor(props: TableMailsProps) {
    super(props);
    this.state = {
      ...this.getStateFromProps(props),
    };
  }

  getEndpointParams() {
    return {
      ...super.getEndpointParams(),
      idAccount: this.props.idAccount,
      idMailbox: this.props.idMailbox,
      mailboxName: this.props.mailboxName,
      showOnlyScheduledToSend: this.props.showOnlyScheduledToSend,
      showOnlySent: this.props.showOnlySent,
      showOnlyDrafts: this.props.showOnlyDrafts,
      showOnlyTemplates: this.props.showOnlyTemplates,
    }
  }

  getFormModalProps(): any {
    let params = super.getFormModalProps();
    params.type = 'right wide';
    return params;
  }

  rowClassName(rowData: any): string {
    return rowData.datetime_read ? '' : 'bg-yellow-50 text-yellow-800';
  }

  renderActionsColumn(data: any, options: any) {
    const R = this.findRecordById(data.id);
    if (R.datetime_read) {
      return <button
        className="btn btn-small btn-transparent"
        onClick={(e) => {
          e.preventDefault();
          request.get("mail/api/mark-as-unread", {
            idAccount: this.props.idAccount,
            idMailbox: this.props.idMailbox,
            idMail: data.id
        }, (response: any) => { this.loadData(); })
        }}
      >
        <span className="icon"><i className="fas fa-eye-slash"></i></span>
        <span className="text">{this.translate('Mark as unread')}</span>
      </button>
    } else {
      return <button
        className="btn btn-small btn-transparent"
        onClick={(e) => {
          e.preventDefault();
          request.get("mail/api/mark-as-read", {
            idAccount: this.props.idAccount,
            idMailbox: this.props.idMailbox,
            idMail: data.id
          }, (response: any) => { this.loadData(); })
        }}
      >
        <span className="icon"><i className="fas fa-eye"></i></span>
        <span className="text">{this.translate('Mark as read')}</span>
      </button>
    }
  }

  renderForm(): JSX.Element {
    let formProps: FormProps = this.getFormProps();
    if (this.props.showOnlyTemplates) {
      formProps.description = {};
      formProps.description.defaultValues = {
        'is_template': true
      };
    }
    return <FormMail {...formProps}/>;
  }

}