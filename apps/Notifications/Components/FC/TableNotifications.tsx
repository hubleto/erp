import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormNotification, { FormNotificationProps } from './FormNotification';
import request from '@hubleto/react-ui/core/Request';

interface TableNotificationsProps extends TableProps {
  folder?: string,
}

const componentName = 'TableNotifications'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Notifications';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableNotifications = (props: TableNotificationsProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Notification'}
    endpointParams={{folder: props.folder}}
    formUrlSlug='parent-app-slug/same-url-slug-as-in-form'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{folder: props.folder}}
    getRowClassName={(table: TableMeta, rowData: any): string => {
      if (props.folder == 'inbox') {
        return rowData.datetime_read ? '' : 'bg-yellow-50 text-yellow-800';
      } else {
        return table.getDefaultRowClassName(rowData);
      }
      
    }}
    renderCell={(table: TableMeta, columnName: string, column: any, data: any, options: any) => {
      if (columnName == "url" && data.url) {
        return <a href={data.url} target="_blank">{data.url}</a>;
      } else {
        return table.renderDefaultCell(columnName, column, data, options);
      }
    }}
    renderActionsColumn={(table: TableMeta, row: any) => {
      if (props.folder == 'inbox') {
        if (row.datetime_read) {
          return <button
            className="btn btn-small btn-transparent text-nowrap"
            onClick={(e) => {
              e.preventDefault();
              request.get( "notifications/api/mark-as-unread", { idNotification: row.id }, (response: any) => { table.loadData(); } )
            }}
          >
            <span className="icon"><i className="fas fa-eye-slash"></i></span>
            <span className="text">{T.translate('Mark as unread')}</span>
          </button>
        } else {
          return <button
            className="btn btn-small btn-transparent text-nowrap"
            onClick={(e) => {
              e.preventDefault();
              request.get(
                "notifications/api/mark-as-read",
                { idNotification: row.id },
                (response: any) => { table.loadData(); }
              )
            }}
          >
            <span className="icon"><i className="fas fa-eye"></i></span>
            <span className="text">{T.translate('Mark as read')}</span>
          </button>
        }
      }
    }}

    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormNotification {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableNotifications;
