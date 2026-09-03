import React, { Component } from 'react'
import FormRecipient from './FormRecipient';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import Translator from '@hubleto/react-ui/core/Translator';

interface TableRecipientsProps extends TableProps {
  idCampaign?: number,
  idEmail?: number
}

const componentName = 'TableRecipients';
const parentApp = 'Hubleto/App/Community/EmailMarketing';
const T = new Translator(parentApp + '/Loader', 'Components/TableRecipients');

const TableRecipients = (props: TableRecipientsProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Recipient'}
    endpointParams={{idCampaign: props.idCampaign, idEmail: props.idEmail}}
    formUrlSlug='email-marketing/recipients'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{id_campaign: props.idCampaign, id_email: props.idEmail}}
    getRowClassName={(table: TableMeta, rowData: any): string => {
      return rowData.is_unsubscribed ? 'bg-red-300' : table.getDefaultRowClassName(rowData);
    }}
    renderCell={(table: TableMeta, columnName: string, column: any, data: any, options: any) => {
      if (columnName == "virt_status" && data.virt_status) {
        const status = data.virt_status.split(',');
        const isUnsubscribed = status[0] == 'unsubscribed';
        const isInvalid = status[1] == 'invalid';
        return <>
          {isUnsubscribed ? <div className='badge badge-danger'>{T.translate('Unsubscribed')}</div> : null}
          {isInvalid ? <div className='badge'>{T.translate('Invalid')}</div> : null}
        </>;
      } else return table.renderDefaultCell(columnName, column, data, options);
    }}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormRecipient {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableRecipients;
