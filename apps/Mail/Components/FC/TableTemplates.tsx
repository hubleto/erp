import React from 'react'
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormTemplate from './FormTemplate';

const componentName = 'TableTemplates'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Mail';

const TableTemplates = (props: TableProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Template'}
    baseUrlSlug='mail/templates'
    formModalProps={{type: 'right wide'}}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormTemplate {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableTemplates;