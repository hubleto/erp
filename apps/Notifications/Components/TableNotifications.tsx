import React, { Component } from 'react'
import request from "@hubleto/react-ui/core/Request";
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';
import Form, { FormProps } from '@hubleto/react-ui/core/Form';
import FormNotification from './FormNotification';

interface TableNotificationsProps extends TableProps {
  folder?: string,
}
interface TableNotificationsState extends TableState {
}

export default class TableNotifications extends Table<TableNotificationsProps, TableNotificationsState> {
  static defaultProps = {
    ...Table.defaultProps,
    formUseModalSimple: true,
    orderBy: {
      field: "id",
      direction: "desc"
    },
    model: 'HubletoApp/Community/Notifications/Models/Notification',
  }

  props: TableNotificationsProps;
  state: TableNotificationsState;

  translationContext: string = 'HubletoApp\\Community\\Notifications\\Loader::Components\\TableNotifications';

  constructor(props: TableNotificationsProps) {
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

  rowClassName(rowData: any): string {
    if (this.props.folder == 'inbox') {
      return rowData.read ? '' : 'bg-yellow-50 text-yellow-800';
    } else {
      return '';
    }
  }

  renderActionsColumn(data: any, options: any) {
    const R = this.findRecordById(data.id);
    if (this.props.folder == 'inbox') {
      if (R.read) {
        return <button
          className="btn btn-small btn-transparent"
          onClick={(e) => {
            e.preventDefault();
            request.get( "notifications/api/mark-as-unread", { idNotification: data.id }, (response: any) => { this.loadData(); } )
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
            request.get( "notifications/api/mark-as-read", { idNotification: data.id }, (response: any) => { this.loadData(); } )
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
    return <FormNotification {...formProps}/>;
  }

}