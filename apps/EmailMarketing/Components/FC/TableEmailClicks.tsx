import React from 'react'
import Table from '@hubleto/react-ui/components/fc/Table';
import { type TableMeta, type TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormEmail from './FormEmail';

interface TableEmailClicksProps extends TableProps {
  idEmail?: number,
  email?: string,
}

const componentName = 'TableEmailClicks'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/EmailMarketing';

const TableEmailClicks = (props: TableEmailClicksProps) => {
  return <Table
    componentName={componentName}
    model={parentApp + '/Models/EmailClick'}
    endpointParams={{idEmail: props.idEmail, email: props.email}}
    baseUrlSlug='email-marketing/emails/clicks'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{id_email: props.idEmail}}
    getRowClassName={(table: TableMeta, rowData: any): string => {
      return rowData.is_closed ? 'bg-slate-300' : table.getDefaultRowClassName(rowData);
    }}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormEmail {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableEmailClicks;
