import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormAccount, { FormAccountProps } from './FormAccount';
import request from '@hubleto/react-ui/core/Request';

interface TableAccountsProps extends TableProps {
  idAccount?: number,
  idMailbox?: number,
  mailboxName?: string,
}

const componentName = 'TableAccounts'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Mail';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableAccounts = (props: TableAccountsProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Account'}
    endpointParams={{
      idAccount: props.idAccount,
      idMailbox: props.idMailbox,
      mailboxName: props.mailboxName,
    }}
    formUrlSlug='mail/accounts'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{id_mailbox: props.idMailbox}}
    getRowClassName={(table: TableMeta, rowData: any): string => {
      return rowData.datetime_read ? '' : 'bg-yellow-50 text-yellow-800';
    }}
    // renderCell={(table: TableMeta, columnName: string, column: any, data: any, options: any) => { return table.renderDefaultCell(columnName, column, data, options); }}
    renderActionsColumn={(table: TableMeta, row: any) => {
      if (row.datetime_read) {
        return <button
          className="btn btn-small btn-transparent"
          onClick={(e) => {
            e.preventDefault();
            request.get("mail/api/mark-as-unread", {
              idAccount: props.idAccount,
              idMailbox: props.idMailbox,
              idMail: row.id
          }, (response: any) => { table.loadData(); })
          }}
        >
          <span className="icon"><i className="fas fa-eye-slash"></i></span>
          <span className="text">{T.translate('Mark as unread')}</span>
        </button>
      } else {
        return <button
          className="btn btn-small btn-transparent"
          onClick={(e) => {
            e.preventDefault();
            request.get("mail/api/mark-as-read", {
              idAccount: props.idAccount,
              idMailbox: props.idMailbox,
              idMail: row.id
            }, (response: any) => { table.loadData(); })
          }}
        >
          <span className="icon"><i className="fas fa-eye"></i></span>
          <span className="text">{T.translate('Mark as read')}</span>
        </button>
      }
      return table.renderDefaultActionsColumn(row);
    }}
    // renderFooter={(table: TableMeta) => { return table.renderDefaultFooter(); }}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormAccount {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableAccounts;
