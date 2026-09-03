import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormRecipientStatus, { FormRecipientStatusProps } from './FormRecipientStatus';

interface TableRecipientStatusesProps extends TableProps {
  idCampaign?: number,
}

const componentName = 'TableRecipientStatuses'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/EmailMarketing';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableRecipientStatuses = (props: TableRecipientStatusesProps) => {
  return <Table
    componentName={componentName}
    model={parentApp + '/Models/RecipientStatus'}
    endpointParams={{idCampaign: props.idCampaign}}
    formUrlSlug='email-marketing/recipients/statuses'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{id_campaign: props.idCampaign}}
    getRowClassName={(table: TableMeta, rowData: any): string => {
      return rowData.is_unsubscribed
        ? 'bg-red-300'
        : (rowData.is_invalid
          ? 'bg-gray-300'
          : table.getDefaultRowClassName(rowData)
        )
      ;
    }}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormRecipientStatus {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableRecipientStatuses;