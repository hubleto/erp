import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormMail, { FormMailProps } from './FormMail';
import { useRecordField } from '@hubleto/react-ui/components/fc/FormRecordStore';
import request from '@hubleto/react-ui/core/Request';

interface TableMailsProps extends TableProps {
  idAccount: number,
  idMailbox: number,
  showOnlyScheduledToSend?: boolean,
  showOnlySent?: boolean,
}

const componentName = 'TableMails'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Mail';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableMails = (props: TableMailsProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Mail'}
    endpointParams={{
      idAccount: props.idAccount,
      idMailbox: props.idMailbox,
      showOnlyScheduledToSend: props.showOnlyScheduledToSend,
      showOnlySent: props.showOnlySent,
    }}
    formUrlSlug={'mail/' + props.idAccount + '/' + props.idMailbox}
    formModalProps={{type: 'right wide'}}
    formProps={{
      idAccount: props.idAccount,
      idMailbox: props.idMailbox,
    } as FormMailProps}
    formDefaultValues={{
      id_account: props.idAccount,
      id_mailbox: props.idMailbox,
    }}
    getRowClassName={(table: TableMeta, rowData: any): string => {
      return rowData.datetime_read ? '' : 'bg-yellow-50 text-yellow-800';
    }}
    renderCell={(table: TableMeta, columnName: string, column: any, data: any, options: any) => {
      if (columnName == "subject") {
        return <>
          <div>{data['subject']}</div>
          {data.ATTACHMENTS ? <div className='flex gap-1'>{data.ATTACHMENTS.map((att, key) => {
            return <a
              href={globalThis.hubleto.config.uploadUrl + '/' + att.file}
              target='_blank'
              className='font-normal text-xs text-blue-500'
            >
              {att.name}
            </a>
          })}</div> : null}
        </>;
      } else {
        return table.renderDefaultCell(columnName, column, data, options);
      }
    }}
    renderActionsColumn={(table: TableMeta, row: any) => {
      const datetimeRead = useRecordField('datetime_read');

      if (datetimeRead) {
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
          <span className="text text-nowrap">{T.translate('Mark as unread')}</span>
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
          <span className="text text-nowrap">{T.translate('Mark as read')}</span>
        </button>
      }
    }}
    // renderFooter={(table: TableMeta) => { return table.renderDefaultFooter(); }}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormMail {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableMails;
