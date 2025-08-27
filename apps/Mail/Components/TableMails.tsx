import React, { Component } from 'react'
import request from "@hubleto/react-ui/core/Request";
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';
import { FormProps } from '@hubleto/react-ui/core/Form';
import FormMail from './FormMail';

interface TableMailsProps extends TableProps {
  idMailbox?: number,
  mailboxName?: string,
  showOnlyDrafts?: boolean,
  showOnlyTemplates?: boolean,
}
interface TableMailsState extends TableState {
}

export default class TableMails extends Table<TableMailsProps, TableMailsState> {
  static defaultProps = {
    ...Table.defaultProps,
    formUseModalSimple: true,
    orderBy: {
      field: "id",
      direction: "desc"
    },
    model: 'HubletoApp/Community/Mail/Models/Mail',
  }

  props: TableMailsProps;
  state: TableMailsState;

  translationContext: string = 'HubletoApp\\Community\\Mail\\Loader::Components\\TableMails';

  constructor(props: TableMailsProps) {
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

  rowClassName(rowData: any): string {
    if (this.props.mailboxName == 'INBOX') {
      return rowData.read ? '' : 'bg-yellow-50 text-yellow-800';
    } else {
      return '';
    }
  }

  renderActionsColumn(data: any, options: any) {
    const R = this.findRecordById(data.id);
    if (this.props.mailboxName == 'INBOX') {
      if (R.read) {
        return <button
          className="btn btn-small btn-transparent"
          onClick={(e) => {
            e.preventDefault();
            request.get( "messages/api/mark-as-unread", { idMail: data.id }, (response: any) => { this.loadData(); } )
          }}
        >
          <span className="icon"><i className="fas fa-eye-slash"></i></span>
          <span className="text">Mark as unread</span>
        </button>
      } else {
        return <button
          className="btn btn-small btn-transparent"
          onClick={(e) => {
            e.preventDefault();
            request.get( "messages/api/mark-as-read", { idMail: data.id }, (response: any) => { this.loadData(); } )
          }}
        >
          <span className="icon"><i className="fas fa-eye"></i></span>
          <span className="text">Mark as read</span>
        </button>
      }
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