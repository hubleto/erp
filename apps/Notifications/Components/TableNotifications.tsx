import React, { Component } from 'react'
import request from "@hubleto/react-ui/core/Request";
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import Form, { FormProps } from '@hubleto/react-ui/core/Form';
import FormNotification from './FormNotification';

interface TableNotificationsProps extends TableExtendedProps {
  folder?: string,
}
interface TableNotificationsState extends TableExtendedState {
}

export default class TableNotifications extends TableExtended<TableNotificationsProps, TableNotificationsState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Notifications/Models/Notification',
  }

  props: TableNotificationsProps;
  state: TableNotificationsState;

  translationContext: string = 'Hubleto\\App\\Community\\Notifications\\Loader';
  translationContextInner: string = 'Components\\TableNotifications';

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
      return rowData.datetime_read ? '' : 'bg-yellow-50 text-yellow-800';
    } else {
      return '';
    }
  }

  renderCell(columnName: string, column: any, data: any, options: any) {
    if (columnName == "url" && data.url) {
      return <a href={data.url} target="_blank">{data.url}</a>;
    } else {
      return super.renderCell(columnName, column, data, options);
    }
  }

  renderActionsColumn(data: any, options: any) {
    const R = this.findRecordById(data.id);
    if (this.props.folder == 'inbox') {
      if (R.datetime_read) {
        return <button
          className="btn btn-small btn-transparent text-nowrap"
          onClick={(e) => {
            e.preventDefault();
            request.get( "notifications/api/mark-as-unread", { idNotification: data.id }, (response: any) => { this.loadData(); } )
          }}
        >
          <span className="icon"><i className="fas fa-eye-slash"></i></span>
          <span className="text">{this.translate('Mark as unread')}</span>
        </button>
      } else {
        return <button
          className="btn btn-small btn-transparent text-nowrap"
          onClick={(e) => {
            e.preventDefault();
            request.get(
              "notifications/api/mark-as-read",
              { idNotification: data.id },
              (response: any) => { this.reload(); }
            )
          }}
        >
          <span className="icon"><i className="fas fa-eye"></i></span>
          <span className="text">{this.translate('Mark as read')}</span>
        </button>
      }
    }
  }

  renderForm(): JSX.Element {
    let formProps: FormProps = this.getFormProps();
    return <FormNotification {...formProps}/>;
  }

}